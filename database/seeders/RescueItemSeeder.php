<?php

namespace Database\Seeders;

use App\Models\RescueItem;
use App\Models\Order;
use App\Models\EscrowLedger;
use App\Models\DkppImpactLog;
use Illuminate\Database\Seeder;

class RescueItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'id' => 'item_01',
                'merchant_id' => 'm_01',
                'title' => 'Surprise Pastry & Bread Bag (Isi 4-5 Roti)',
                'item_type' => 'Mystery Bag',
                'original_price' => 75000.00,
                'discount_pct' => 67,
                'rescue_price' => 25000.00,
                'stock_qty' => 4,
                'pickup_start' => '20:30',
                'pickup_end' => '21:45',
                'est_weight_kg' => 0.95,
                'status' => 'ACTIVE_FLASH',
            ],
            [
                'id' => 'item_02',
                'merchant_id' => 'm_02',
                'title' => 'Mystery Toast & Cold Brew Combo',
                'item_type' => 'Mystery Bag',
                'original_price' => 60000.00,
                'discount_pct' => 60,
                'rescue_price' => 24000.00,
                'stock_qty' => 3,
                'pickup_start' => '21:00',
                'pickup_end' => '22:30',
                'est_weight_kg' => 0.65,
                'status' => 'ACTIVE_FLASH',
            ],
            [
                'id' => 'item_03',
                'merchant_id' => 'm_03',
                'title' => 'Kantong Lauk Matang Nusantara (Porsi Komplit)',
                'item_type' => 'Mystery Bag',
                'original_price' => 50000.00,
                'discount_pct' => 70,
                'rescue_price' => 15000.00,
                'stock_qty' => 6,
                'pickup_start' => '20:00',
                'pickup_end' => '21:30',
                'est_weight_kg' => 0.85,
                'status' => 'ACTIVE_FLASH',
            ],
            [
                'id' => 'item_04',
                'merchant_id' => 'm_04',
                'title' => 'Sourdough Loaf & Croissant Heritage Box',
                'item_type' => 'Mystery Bag',
                'original_price' => 95000.00,
                'discount_pct' => 68,
                'rescue_price' => 30000.00,
                'stock_qty' => 2,
                'pickup_start' => '20:15',
                'pickup_end' => '21:30',
                'est_weight_kg' => 1.10,
                'status' => 'ACTIVE_FLASH',
            ],
        ];

        foreach ($items as $item) {
            RescueItem::updateOrCreate(['id' => $item['id']], $item);
        }

        // Initial Seeded Completed Transaction & Escrow Audit
        $order = Order::updateOrCreate(
            ['id' => 'ord_101'],
            [
                'order_code' => 'RFC-BKS-8912',
                'customer_name' => 'Dimas Pratama',
                'customer_phone' => '081234567890',
                'item_id' => 'item_01',
                'qty' => 1,
                'total_amount' => 25000.00,
                'escrow_status' => 'VERIFIED_VIA_OTP',
                'pickup_otp' => '782910',
                'pickup_otp_hash' => hash('sha256', '782910'),
                'midtrans_snap_id' => 'MID-SNAP-89127361',
                'verified_at' => now(),
            ]
        );

        EscrowLedger::updateOrCreate(
            ['id' => 'esc_101'],
            [
                'order_id' => 'ord_101',
                'merchant_id' => 'm_01',
                'gross_amount' => 25000.00,
                'platform_fee_15pct' => 3750.00,
                'merchant_payout_85pct' => 21250.00,
                'payout_status' => 'RELEASED_TO_MERCHANT',
                'disbursed_at' => now(),
            ]
        );

        DkppImpactLog::updateOrCreate(
            ['id' => 'imp_101'],
            [
                'order_id' => 'ord_101',
                'food_weight_kg' => 0.95,
                'co2e_saved_kg' => 2.37,
                'virtual_water_liter' => 380.00,
                'calories_salvaged' => 720.00,
                'economic_saved_rp' => 50000.00,
            ]
        );
    }
}
