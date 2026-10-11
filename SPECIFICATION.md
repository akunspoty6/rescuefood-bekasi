# 📑 MASTER SPECIFICATION: RESCUEFOOD BEKASI
**Project Name:** `rescuefood_bekasi`  
**Tech Stack:** Tailwind CSS (CDN), Alpine.js, Lucide Icons, Leaflet.js, PostgreSQL/Supabase DDL, Midtrans Escrow Engine.  
**Version:** 1.0.0-PROD  

---

## 🏛️ Arsitektur Tiga Tingkat (Three-Tier Architecture)

### 1. Presentation Tier (Frontend Client)
- **Framework:** High-End Single-File Inline Reactive SPA (Alpine.js v3 + Tailwind CSS CDN).
- **Komponen Utama:**
  - Header Navigasi dengan Role Switcher:
    - 🛒 **Mode Penyelamat Makanan (Pelanggan)**
    - 🏪 **Mode Kasir Toko / Mitra Kuliner**
    - 🏛️ **Mode Dinas Ketahanan Pangan (DKPP Audit & ESG)**
  - Banner Hitung Mundur Flash Sale Global & Indikator Status Toko Buka.
  - Peta Interaktif Leaflet.js wilayah Kota Bekasi (Summarecon, Grand Galaxy, Kranji, Harapan Indah).
  - Katalog Kartu Mystery Bag dengan status stok real-time, badge diskon 50-80%, dan tombol klaim instan.
  - Dialog Modal Checkout & Simulasi Pembayaran Midtrans Snap (QRIS, GoPay, BCA VA).
  - Dialog Modal Tiket Digital Pengambilan (Digital Pass) dengan QR Code SVG dan 6-Digit OTP.
  - Panel Kasir Merchant untuk validasi OTP, pencairan dana 85%, dan pembuatan kantong surplus baru.
  - Dashboard Eksekutif DKPP dengan Live Impact Calculator (Kg Pangan, CO₂e, Air Virtual, dan Kalori) plus fitur cetak sertifikat ESG.

### 2. Business Logic & State Machine Tier
- Atomic decrement logic untuk menghindari race condition pada saat flash sale berlangsung.
- Escrow Lifecycle Controller:
  - `PAYMENT_PENDING` ➔ `PAYMENT_HELD` ➔ `VERIFIED_VIA_OTP` ➔ `DISBURSED`.
- DKPP Calculation Engine:
  - Pengali 2.5 untuk konversi kg makanan ke kg CO₂e.
  - Pengali 400L untuk air virtual agrikultur.

### 3. Data Tier (Relational Storage)
- Skema terdistribusi PostgreSQL/Supabase dengan tabel `merchants`, `rescue_items`, `orders`, `escrow_ledger`, dan `dkpp_impact_logs`.
- SQLite backup compatibility untuk evaluasi pre-flight lokal.
