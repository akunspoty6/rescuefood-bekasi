-- ============================================================================
-- RESCUEFOOD BEKASI - DDL & SEED DATA (SUPABASE / POSTGRESQL & SQLITE HYBRID)
-- Civic-Tech & Food Security Concurrency Architecture
-- ============================================================================

-- 1. Tabel Mitra Kuliner (Merchants)
CREATE TABLE IF NOT EXISTS merchants (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    address TEXT NOT NULL,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    phone TEXT NOT NULL,
    is_verified_dkpp INTEGER DEFAULT 1,
    bank_account TEXT NOT NULL,
    rating REAL DEFAULT 4.8,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Item Makanan Surplus / Mystery Bag (Rescue Items)
CREATE TABLE IF NOT EXISTS rescue_items (
    id TEXT PRIMARY KEY,
    merchant_id TEXT NOT NULL,
    title TEXT NOT NULL,
    item_type TEXT NOT NULL, -- 'Mystery Bag' / 'Specific Item'
    original_price REAL NOT NULL,
    discount_pct INTEGER NOT NULL,
    rescue_price REAL NOT NULL,
    stock_qty INTEGER NOT NULL CHECK (stock_qty >= 0),
    pickup_start TEXT NOT NULL,
    pickup_end TEXT NOT NULL,
    est_weight_kg REAL DEFAULT 0.8,
    status TEXT DEFAULT 'ACTIVE_FLASH', -- 'ACTIVE_FLASH', 'SOLD_OUT', 'EXPIRED'
    FOREIGN KEY (merchant_id) REFERENCES merchants (id)
);

-- 3. Tabel Pesanan & Escrow Handshake (Orders)
CREATE TABLE IF NOT EXISTS orders (
    id TEXT PRIMARY KEY,
    order_code TEXT UNIQUE NOT NULL,
    customer_name TEXT NOT NULL,
    customer_phone TEXT NOT NULL,
    item_id TEXT NOT NULL,
    qty INTEGER NOT NULL DEFAULT 1,
    total_amount REAL NOT NULL,
    escrow_status TEXT DEFAULT 'PAYMENT_HELD', -- 'PENDING', 'PAYMENT_HELD', 'VERIFIED_VIA_OTP', 'DISBURSED', 'REFUNDED'
    pickup_otp TEXT NOT NULL,
    pickup_otp_hash TEXT NOT NULL,
    midtrans_snap_id TEXT,
    verified_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES rescue_items (id)
);

-- 4. Tabel Buku Besar Escrow (Escrow Ledger)
CREATE TABLE IF NOT EXISTS escrow_ledger (
    id TEXT PRIMARY KEY,
    order_id TEXT NOT NULL,
    merchant_id TEXT NOT NULL,
    gross_amount REAL NOT NULL,
    platform_fee_15pct REAL NOT NULL,
    merchant_payout_85pct REAL NOT NULL,
    payout_status TEXT DEFAULT 'HELD', -- 'HELD', 'RELEASED_TO_MERCHANT', 'REFUNDED'
    disbursed_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders (id),
    FOREIGN KEY (merchant_id) REFERENCES merchants (id)
);

-- 5. Tabel Metrik Dampak Ketahanan Pangan (DKPP Impact Logs)
CREATE TABLE IF NOT EXISTS dkpp_impact_logs (
    id TEXT PRIMARY KEY,
    order_id TEXT NOT NULL,
    food_weight_kg REAL NOT NULL,
    co2e_saved_kg REAL NOT NULL,
    virtual_water_liter REAL NOT NULL,
    calories_salvaged REAL NOT NULL,
    economic_saved_rp REAL NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders (id)
);

-- ============================================================================
-- SEED DATA MITRA & ITEM SURPLUS KOTA BEKASI
-- ============================================================================

INSERT OR REPLACE INTO merchants (id, name, category, address, latitude, longitude, phone, is_verified_dkpp, bank_account, rating) VALUES
('m_01', 'Holland Bakery Summarecon', 'Bakery & Pastry', 'Ruko Sinpasa Blok A-12, Summarecon Bekasi', -6.223847, 107.001248, '081288991122', 1, 'BCA 8720192831 a/n PT Holland Bekasi', 4.9),
('m_02', 'Kopi Nako & Toast Grand Galaxy', 'Cafe & Toast', 'Jl. Boulevard Raya Barat No. 8, Jaka Setia, Bekasi Selatan', -6.269412, 106.974182, '081399882233', 1, 'Mandiri 13200981293 a/n Kopi Nako Galaxy', 4.8),
('m_03', 'Dapur Solo Bu Titik Kranji', 'Restoran Tradisional', 'Jl. Jenderal Sudirman No. 45, Kranji, Bekasi Barat', -6.230192, 106.978211, '081122334455', 1, 'BRI 019201928371 a/n Siti Titik', 4.7),
('m_04', 'Dough Deli Artisan Harapan Indah', 'Artisan Bakery', 'Ruko Sentra Niaga 2 No. 19, Medan Satria, Bekasi', -6.182910, 106.985210, '081566778899', 1, 'BCA 7710293812 a/n Dough Deli', 4.9);

INSERT OR REPLACE INTO rescue_items (id, merchant_id, title, item_type, original_price, discount_pct, rescue_price, stock_qty, pickup_start, pickup_end, est_weight_kg, status) VALUES
('item_01', 'm_01', 'Surprise Pastry & Bread Bag (Isi 4-5 Roti)', 'Mystery Bag', 75000.00, 67, 25000.00, 4, '20:30', '21:45', 0.95, 'ACTIVE_FLASH'),
('item_02', 'm_02', 'Mystery Toast & Cold Brew Combo', 'Mystery Bag', 60000.00, 60, 24000.00, 3, '21:00', '22:30', 0.65, 'ACTIVE_FLASH'),
('item_03', 'm_03', 'Kantong Lauk Matang Nusantara (Porsi Komplit)', 'Mystery Bag', 50000.00, 70, 15000.00, 6, '20:00', '21:30', 0.85, 'ACTIVE_FLASH'),
('item_04', 'm_04', 'Sourdough Loaf & Croissant Heritage Box', 'Mystery Bag', 95000.00, 68, 30000.00, 2, '20:15', '21:30', 1.10, 'ACTIVE_FLASH');

-- Sample Initial Completed Rescue Transaction
INSERT OR REPLACE INTO orders (id, order_code, customer_name, customer_phone, item_id, qty, total_amount, escrow_status, pickup_otp, pickup_otp_hash, midtrans_snap_id, verified_at) VALUES
('ord_101', 'RFC-BKS-8912', 'Dimas Pratama', '081234567890', 'item_01', 1, 25000.00, 'VERIFIED_VIA_OTP', '782910', 'hash_782910', 'MID-SNAP-89127361', CURRENT_TIMESTAMP);

INSERT OR REPLACE INTO escrow_ledger (id, order_id, merchant_id, gross_amount, platform_fee_15pct, merchant_payout_85pct, payout_status, disbursed_at) VALUES
('esc_101', 'ord_101', 'm_01', 25000.00, 3750.00, 21250.00, 'RELEASED_TO_MERCHANT', CURRENT_TIMESTAMP);

INSERT OR REPLACE INTO dkpp_impact_logs (id, order_id, food_weight_kg, co2e_saved_kg, virtual_water_liter, calories_salvaged, economic_saved_rp) VALUES
('imp_101', 'ord_101', 0.95, 2.37, 380.0, 720.0, 50000.00);
