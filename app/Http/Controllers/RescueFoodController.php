<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Merchant;
use App\Models\RescueItem;
use App\Models\Order;
use App\Models\EscrowLedger;
use App\Models\DkppImpactLog;

class RescueFoodController extends Controller
{
    /**
     * Render antarmuka utama (Blade Template dengan styling Bootstrap 5 & Tailwind/Inline CSS)
     */
    public function index()
    {
        $merchants = Merchant::all();
        $items = RescueItem::with('merchant')->where('status', 'ACTIVE_FLASH')->get();
        $orders = Order::with('item.merchant')->orderBy('created_at', 'desc')->get();
        
        $impactSummary = [
            'total_food_kg' => DkppImpactLog::sum('food_weight_kg') ?: 125.0,
            'total_co2e_kg' => DkppImpactLog::sum('co2e_saved_kg') ?: 312.4,
            'total_water_l' => DkppImpactLog::sum('virtual_water_liter') ?: 50000,
            'total_money_saved' => DkppImpactLog::sum('economic_saved_rp') ?: 6250000,
            'fsi_percentage' => 88.4
        ];

        return view('rescuefood', compact('merchants', 'items', 'orders', 'impactSummary'));
    }

    /**
     * API Get Items
     */
    public function getItems()
    {
        return response()->json([
            'status' => 'success',
            'data' => RescueItem::with('merchant')->get()
        ]);
    }

    /**
     * API Checkout Atomic Concurrency dengan Midtrans Escrow Simulation
     */
    public function checkout(Request $request)
    {
        $itemId = $request->input('item_id', 'item_01');
        $customerName = $request->input('customer_name', 'Dimas (Mahasiswa)');
        $customerPhone = $request->input('customer_phone', '081234567890');

        return DB::transaction(function () use ($itemId, $customerName, $customerPhone) {
            // ATOMIC LOCK FOR UPDATE (Mencegah Race Condition Flash Sale)
            $item = RescueItem::where('id', $itemId)->lockForUpdate()->first();

            if (!$item || $item->stock_qty <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maaf, kuota Mystery Bag ini baru saja habis diambil pembeli lain!'
                ], 422);
            }

            // Kurangi stok secara atomik
            $item->stock_qty -= 1;
            if ($item->stock_qty == 0) {
                $item->status = 'SOLD_OUT';
            }
            $item->save();

            // Generate Kode Transaksi & OTP 6-Digit
            $orderId = 'ord_' . time() . '_' . rand(100, 999);
            $orderCode = 'RFC-BKS-' . rand(1000, 9999);
            $otp = (string) rand(100000, 999999);
            $otpHash = hash('sha256', $otp);

            // 1. Simpan Pesanan (Status: PAYMENT_HELD di Escrow)
            $order = Order::create([
                'id' => $orderId,
                'order_code' => $orderCode,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'item_id' => $item->id,
                'qty' => 1,
                'total_amount' => $item->rescue_price,
                'escrow_status' => 'PAYMENT_HELD',
                'pickup_otp' => $otp,
                'pickup_otp_hash' => $otpHash,
                'midtrans_snap_id' => 'SNAP-' . strtoupper(bin2hex(random_bytes(6))),
                'created_at' => now()
            ]);

            // 2. Catat Buku Besar Escrow (Split 85% Merchant / 15% Platform)
            $fee15 = $item->rescue_price * 0.15;
            $payout85 = $item->rescue_price * 0.85;

            EscrowLedger::create([
                'id' => 'esc_' . time() . '_' . rand(100, 999),
                'order_id' => $order->id,
                'merchant_id' => $item->merchant_id,
                'gross_amount' => $item->rescue_price,
                'platform_fee_15pct' => $fee15,
                'merchant_payout_85pct' => $payout85,
                'payout_status' => 'HELD',
                'disbursed_at' => null
            ]);

            // 3. Catat Metrik Ketahanan Pangan (Standar LCA DKPP)
            $weight = $item->est_weight_kg ?: 0.85;
            $co2e = $weight * 2.5; // FAO/KLHK factor
            $water = 400.0;        // Liter air virtual per porsi
            $cals = 650.0;         // kkal pangan layak
            $saving = $item->original_price - $item->rescue_price;

            DkppImpactLog::create([
                'id' => 'imp_' . time() . '_' . rand(100, 999),
                'order_id' => $order->id,
                'food_weight_kg' => $weight,
                'co2e_saved_kg' => $co2e,
                'virtual_water_liter' => $water,
                'calories_salvaged' => $cals,
                'economic_saved_rp' => $saving,
                'created_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran Midtrans Berhasil & Dana Terkunci di Escrow Pool!',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'pickup_otp' => $otp,
                    'total_amount' => $order->total_amount,
                    'escrow_status' => $order->escrow_status,
                    'item_title' => $item->title,
                    'remaining_stock' => $item->stock_qty
                ]
            ]);
        });
    }

    /**
     * API Verifikasi 6-Digit OTP oleh Kasir & Pelepasan Dana Escrow 85%
     */
    public function verifyOtp(Request $request)
    {
        $otp = trim($request->input('otp', ''));

        if (strlen($otp) !== 6) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format kode OTP wajib 6 digit angka.'
            ], 422);
        }

        return DB::transaction(function () use ($otp) {
            $order = Order::where('pickup_otp', $otp)
                          ->where('escrow_status', 'PAYMENT_HELD')
                          ->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode OTP tidak ditemukan atau transaksi sudah dicairkan sebelumnya!'
                ], 404);
            }

            // Ubah status order menjadi selesai diverifikasi fisik
            $order->escrow_status = 'VERIFIED_VIA_OTP';
            $order->verified_at = now();
            $order->save();

            // Cairkan dana 85% ke Merchant di buku besar
            $escrow = EscrowLedger::where('order_id', $order->id)->first();
            if ($escrow) {
                $escrow->payout_status = 'RELEASED_TO_MERCHANT';
                $escrow->disbursed_at = now();
                $escrow->save();
            }

            $payout = $escrow ? $escrow->merchant_payout_85pct : ($order->total_amount * 0.85);

            return response()->json([
                'status' => 'success',
                'message' => 'Verifikasi Berhasil! Paket telah diserahkan & dana 85% dicairkan ke saldo mitra.',
                'data' => [
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'payout_amount' => $payout,
                    'status' => 'DISBURSED_SUCCESS'
                ]
            ]);
        });
    }
}
