# 📄 SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
## STANDAR IEEE 830-1998
**Nama Sistem:** RescueFood Bekasi Core Engine  
**Versi:** 1.0.0-PROD  
**Tanggal:** 11 Oktober 2026  
**Status Audit:** Passed Internal Architecture Review  

---

## 1. Pendahuluan
### 1.1 Tujuan
Dokumen ini mendefinisikan spesifikasi kebutuhan perangkat lunak untuk sistem **RescueFood Bekasi**, mencakup arsitektur state machine transaksi, antarmuka pembayaran escrow, protokol verifikasi serah terima OTP, dan mesin kalkulasi ketahanan pangan.

### 1.2 Cakupan Sistem
Sistem ini menyediakan portal berbasis web multi-aktor yang mengelola penawaran makanan berlebih berjangka waktu terbatas (*flash sale rescue window*), proses penahanan dana escrow, dan audit dampak ekologis.

---

## 2. Deskripsi Umum (Overall Description)
### 2.1 Perspektif Produk
Sistem beroperasi sebagai SaaS mandiri dengan kapabilitas integrasi ke PostgreSQL/Supabase, gateway pembayaran Midtrans Snap Sandbox, dan layanan peta open-source OpenStreetMap / Leaflet.

### 2.2 Karakteristik Pengguna
- **End Customer:** Memerlukan antarmuka yang cepat, intuitif, dan tidak membingungkan saat memesan makanan sisa beberapa menit sebelum toko tutup.
- **Merchant Operator (Kasir):** Memerlukan verifikasi cepat tanpa langkah administrasi rumit (cukup verifikasi 6 digit OTP).
- **Public Officer / Auditor:** Memerlukan agregasi data tabular dan grafik dampak ekologis.

---

## 3. State Machine & Transaksi Spesifik

### 3.1 Siklus Hidup Item Penyelamatan (Rescue Item Lifecycle)
```
[DRAFT]
   │
   ▼ (Jam Operasional Masuk: misal 20.00 WIB)
[ACTIVE_FLASH_SALE] ──(Stok Habis / Dipesan)──► [SOLD_OUT]
   │
   ▼ (Lewat Waktu Jemput: misal 21.30 WIB)
[EXPIRED_VOID]
```

### 3.2 Siklus Hidup Transaksi & Escrow (Escrow Lifecycle)
```
[ORDER_CREATED]
   │ (User menekan tombol Ambil Sekarang)
   ▼
[PAYMENT_PENDING] (Midtrans Snap Token Generated)
   │ (Webhook HTTP 200: Settlement Confirmed)
   ▼
[PAYMENT_HELD_ESCROW] ──(Dana terkunci di Pool; OTP 6-Digit Terbit ke User)
   │
   ├──► [VERIFIED_VIA_OTP] (Kasir memasukkan OTP valid)
   │       │
   │       ▼
   │    [FUNDS_DISBURSED] (85% ke Merchant, 15% ke Platform Platform Fee)
   │
   └──► [CANCELLED_REFUND] (Batal sebelum jam jemput / Toko tutup)
```

---

## 4. Spesifikasi Database (Supabase / PostgreSQL)

### 4.1 Tabel `merchants`
- `id` : UUID PRIMARY KEY DEFAULT gen_random_uuid()
- `name` : VARCHAR(150) NOT NULL
- `category` : VARCHAR(50) (Bakery, Resto, Cafe, Hotel)
- `address` : TEXT NOT NULL
- `latitude` : NUMERIC(10, 7) NOT NULL
- `longitude` : NUMERIC(10, 7) NOT NULL
- `phone` : VARCHAR(30)
- `is_verified_dkpp` : BOOLEAN DEFAULT TRUE
- `bank_account` : VARCHAR(100)
- `rating` : NUMERIC(3, 2) DEFAULT 4.8

### 4.2 Tabel `rescue_items`
- `id` : UUID PRIMARY KEY DEFAULT gen_random_uuid()
- `merchant_id` : UUID REFERENCES merchants(id) ON DELETE CASCADE
- `title` : VARCHAR(200) NOT NULL
- `item_type` : VARCHAR(50) (Mystery Bag, Specific Item)
- `original_price` : NUMERIC(12, 2) NOT NULL
- `discount_pct` : INT NOT NULL CHECK (discount_pct BETWEEN 50 AND 90)
- `rescue_price` : NUMERIC(12, 2) NOT NULL
- `stock_qty` : INT NOT NULL CHECK (stock_qty >= 0)
- `pickup_start` : TIME NOT NULL
- `pickup_end` : TIME NOT NULL
- `est_weight_kg` : NUMERIC(5, 2) DEFAULT 0.8
- `status` : VARCHAR(30) DEFAULT 'ACTIVE_FLASH'

### 4.3 Tabel `orders`
- `id` : UUID PRIMARY KEY DEFAULT gen_random_uuid()
- `order_code` : VARCHAR(30) UNIQUE NOT NULL
- `customer_name` : VARCHAR(100) NOT NULL
- `customer_phone` : VARCHAR(30) NOT NULL
- `item_id` : UUID REFERENCES rescue_items(id)
- `qty` : INT NOT NULL DEFAULT 1
- `total_amount` : NUMERIC(12, 2) NOT NULL
- `escrow_status` : VARCHAR(30) DEFAULT 'PAYMENT_PENDING'
- `pickup_otp` : VARCHAR(6) NOT NULL
- `pickup_otp_hash` : VARCHAR(255) NOT NULL
- `verified_at` : TIMESTAMP WITH TIME ZONE NULL
- `created_at` : TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP

### 4.4 Tabel `escrow_ledger`
- `id` : UUID PRIMARY KEY DEFAULT gen_random_uuid()
- `order_id` : UUID REFERENCES orders(id)
- `gross_amount` : NUMERIC(12, 2) NOT NULL
- `platform_fee_15pct` : NUMERIC(12, 2) NOT NULL
- `merchant_payout_85pct` : NUMERIC(12, 2) NOT NULL
- `payout_status` : VARCHAR(30) DEFAULT 'HELD'
- `disbursed_at` : TIMESTAMP WITH TIME ZONE NULL

### 4.5 Tabel `dkpp_impact_logs`
- `id` : UUID PRIMARY KEY DEFAULT gen_random_uuid()
- `order_id` : UUID REFERENCES orders(id)
- `food_weight_kg` : NUMERIC(8, 2) NOT NULL
- `co2e_saved_kg` : NUMERIC(8, 2) NOT NULL
- `virtual_water_liter` : NUMERIC(10, 2) NOT NULL
- `calories_salvaged` : NUMERIC(10, 2) NOT NULL
- `created_at` : TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP

---

## 5. Algoritma Perhitungan Dampak Pangan & Lingkungan (Formula DKPP)
Sesuai acuan Life Cycle Assessment (LCA) Food Loss and Waste:
1. **Emisi Karbon Dicegah (CO₂e Saved):**
   $$\text{CO}_2\text{e (kg)} = \text{Food Weight (kg)} \times 2.5$$
2. **Air Virtual Terselamatkan:**
   $$\text{Virtual Water (Liter)} = \text{Porsi} \times 400\text{ Liter}$$
3. **Kalori Terselamatkan:**
   $$\text{Total Kalori (kkal)} = \text{Porsi} \times 650\text{ kkal}$$
4. **Indeks Manfaat Ekonomi Masyarakat (Economic Savings):**
   $$\text{Penghematan (Rp)} = \text{Original Price} - \text{Rescue Price}$$
