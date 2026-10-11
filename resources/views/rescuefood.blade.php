<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RescueFood Bekasi — Food Concurrency & Escrow Platform</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet.js Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- REFINED MINIMALIST DARK PALETTE (LINEAR / VERCEL GRADE) -->
    <style>
        :root {
            --bg-base: #090a0f;
            --bg-surface: #11141c;
            --bg-subtle: #171b26;
            --border-subtle: rgba(255, 255, 255, 0.07);
            --border-hover: rgba(16, 185, 129, 0.35);
            --text-primary: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
            --emerald-accent: #10b981;
            --emerald-dark: #059669;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
            letter-spacing: -0.01em;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .card-surface {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 0.875rem;
            transition: border-color 0.15s ease, transform 0.15s ease;
        }

        .card-surface:hover {
            border-color: var(--border-hover);
        }

        .segmented-control {
            background: #0d0f15;
            padding: 4px;
            border-radius: 0.625rem;
            border: 1px solid var(--border-subtle);
            display: inline-flex;
            gap: 2px;
        }

        .segmented-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.8125rem;
            font-weight: 500;
            padding: 0.4rem 0.85rem;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
        }

        .segmented-btn.active {
            background: var(--bg-subtle);
            color: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .btn-emerald {
            background-color: var(--emerald-accent);
            border: 1px solid rgba(255,255,255,0.1);
            color: #062419;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.625rem;
            padding: 0.55rem 1rem;
            transition: all 0.15s ease;
        }

        .btn-emerald:hover {
            background-color: #34d399;
            color: #041d14;
        }

        .badge-subtle {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.55rem;
            border-radius: 0.375rem;
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-warning-subtle {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.55rem;
            border-radius: 0.375rem;
            background: rgba(245, 158, 11, 0.1);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        #rescue-map {
            height: 340px;
            width: 100%;
            border-radius: 0.75rem;
        }

        .leaflet-container {
            background: #090a0f !important;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.03em;
        }

        .modal-content {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 1rem;
            color: #f8fafc;
        }

        .form-control-dark {
            background: #090a0f;
            border: 1px solid var(--border-subtle);
            color: #fff;
            border-radius: 0.5rem;
        }

        .form-control-dark:focus {
            background: #090a0f;
            border-color: var(--emerald-accent);
            color: #fff;
            box-shadow: none;
        }
    </style>
</head>
<body>

    <!-- TOAST NOTIFICATION -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border border-secondary" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body small" id="toastMessage">
                    Aksi berhasil diproses.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- CLEAN NAVBAR -->
    <nav class="navbar sticky-top py-3" style="background: rgba(9, 10, 15, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border-subtle);">
        <div class="container-fluid px-lg-5">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 text-decoration-none">
                    <div style="width: 28px; height: 28px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-box2-heart-fill text-success" style="font-size: 14px;"></i>
                    </div>
                    <span class="fw-bold fs-6 tracking-tight text-white">RescueFood<span style="color: var(--emerald-accent);">.id</span></span>
                    <span class="badge-subtle ms-1">Laravel 11</span>
                </div>
            </div>

            <!-- SEGMENTED ROLE CONTROLLER -->
            <div class="segmented-control">
                <button class="segmented-btn active" id="btnRoleCustomer" onclick="switchRole('customer')">
                    <i class="bi bi-bag me-1"></i> Penyelamat
                </button>
                <button class="segmented-btn" id="btnRoleMerchant" onclick="switchRole('merchant')">
                    <i class="bi bi-shop me-1"></i> Kasir Mitra
                </button>
                <button class="segmented-btn" id="btnRoleDkpp" onclick="switchRole('dkpp')">
                    <i class="bi bi-shield-check me-1"></i> DKPP & ESG
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-lg-5 py-4">

        <!-- ================================================================= -->
        <!-- VIEW 1: CUSTOMER VIEW                                             -->
        <!-- ================================================================= -->
        <div id="viewCustomer">
            <!-- HERO SECTION -->
            <div class="card-surface p-4 p-md-5 mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge-subtle"><i class="bi bi-clock me-1"></i> Flash Sale Jelang Tutup Gerai</span>
                            <span class="text-dim small">•</span>
                            <span class="text-dim small">Kota Bekasi</span>
                        </div>
                        <h1 class="display-6 fw-bold text-white mb-3" style="letter-spacing: -0.03em;">
                            Selamatkan Surplus Makanan Berkualitas dengan Diskon 50–80%.
                        </h1>
                        <p class="text-muted fs-6 mb-4" style="max-width: 580px; line-height: 1.6;">
                            Platform civic-tech berbasis Laravel dengan arsitektur <strong>Escrow & Concurrency Locking</strong>. Mencegah perebutan stok ganda (anti race condition) dan mengunci dana di rekening penampung sampai verifikasi kasir.
                        </p>

                        <!-- FLASH SALE COUNTDOWN -->
                        <div class="d-inline-flex align-items-center gap-3 p-2 px-3 rounded-3" style="background: var(--bg-base); border: 1px solid var(--border-subtle);">
                            <span class="small text-muted">Batas Waktu Pengambilan:</span>
                            <span class="font-monospace fw-bold text-success" id="flashTimer">00:41:28</span>
                        </div>
                    </div>

                    <!-- METRICS GRID -->
                    <div class="col-lg-5">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card-surface p-3">
                                    <span class="small text-dim d-block mb-1">Reduksi Emisi CO₂e</span>
                                    <div class="stat-value" id="statCo2">{{ number_format($impactSummary['total_co2e_kg'], 1) }} <span class="fs-6 fw-normal text-dim">kg</span></div>
                                    <small class="text-muted" style="font-size: 11px;">Standar LCA KLHK</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card-surface p-3">
                                    <span class="small text-dim d-block mb-1">Pangan Terselamatkan</span>
                                    <div class="stat-value" id="statFood">{{ number_format($impactSummary['total_food_kg'], 1) }} <span class="fs-6 fw-normal text-dim">kg</span></div>
                                    <small class="text-muted" style="font-size: 11px;">Total surplus</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card-surface p-3">
                                    <span class="small text-dim d-block mb-1">Konservasi Air</span>
                                    <div class="stat-value">{{ number_format($impactSummary['total_water_l']) }} <span class="fs-6 fw-normal text-dim">liter</span></div>
                                    <small class="text-muted" style="font-size: 11px;">Air irigasi virtual</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card-surface p-3">
                                    <span class="small text-dim d-block mb-1">Total Penghematan</span>
                                    <div class="stat-value text-success">Rp {{ number_format($impactSummary['total_money_saved'] / 1000000, 2) }}M</div>
                                    <small class="text-muted" style="font-size: 11px;">Efisiensi pengeluaran</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAP SECTION WITH CARTO DARK MATTER -->
            <div class="card-surface p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <span class="small fw-semibold text-white"><i class="bi bi-geo-alt me-1 text-danger"></i> Sebaran Gerai Mitra (Bekasi Hyper-Local Radar)</span>
                    <span class="badge-subtle">{{ count($merchants) }} Gerai Aktif</span>
                </div>
                <div id="rescue-map"></div>
            </div>

            <!-- CATALOG ITEMS -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0">Mystery Bag Siap Ambil Hari Ini</h5>
                    <span class="small text-dim">Sinkronisasi Database SQLite / PostgreSQL</span>
                </div>

                <div class="row g-3" id="itemCatalog">
                    @foreach($items as $item)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card-surface p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge-subtle">{{ $item->item_type }}</span>
                                    <span class="small text-muted"><i class="bi bi-star-fill text-warning me-1"></i>{{ $item->merchant->rating }}</span>
                                </div>
                                <h6 class="fw-semibold text-white mb-1">{{ $item->title }}</h6>
                                <div class="small text-muted mb-2">{{ $item->merchant->name }}</div>
                                <div class="small text-dim mb-3" style="font-size: 11px;"><i class="bi bi-pin-map me-1"></i>{{ $item->merchant->address }}</div>

                                <div class="p-2 rounded-2 mb-3" style="background: var(--bg-base); border: 1px solid var(--border-subtle); font-size: 12px;">
                                    <div class="d-flex justify-content-between text-muted mb-1">
                                        <span>Jam Ambil:</span>
                                        <span class="text-white">{{ $item->pickup_start }} - {{ $item->pickup_end }} WIB</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted">
                                        <span>Sisa Kuota:</span>
                                        <span class="fw-semibold {{ $item->stock_qty <= 2 ? 'text-danger' : 'text-success' }}" id="stock-{{ $item->id }}">{{ $item->stock_qty }} Kantong</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-top" style="border-color: var(--border-subtle) !important;">
                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                    <div>
                                        <span class="text-dim text-decoration-line-through small">Rp {{ number_format($item->original_price) }}</span>
                                        <div class="fs-5 fw-bold text-white">Rp {{ number_format($item->rescue_price) }}</div>
                                    </div>
                                    <span class="badge-subtle text-danger bg-danger bg-opacity-10 border-danger border-opacity-25">-{{ $item->discount_pct }}%</span>
                                </div>

                                <button class="btn btn-emerald w-100" 
                                        onclick="openCheckoutModal('{{ $item->id }}', '{{ $item->title }}', '{{ $item->merchant->name }}', {{ $item->rescue_price }}, {{ $item->original_price }})"
                                        id="btnClaim-{{ $item->id }}">
                                    Ambil Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- MY ORDERS -->
            <div class="card-surface p-4 mb-4">
                <h6 class="fw-bold text-white mb-3"><i class="bi bi-receipt me-2"></i> Tiket Penyelamatan Aktif (Escrow Status)</h6>
                <div class="row g-3">
                    @foreach($orders as $order)
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3" style="background: var(--bg-base); border: 1px solid var(--border-subtle);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-monospace small text-muted">{{ $order->order_code }}</span>
                                <span class="{{ $order->escrow_status == 'VERIFIED_VIA_OTP' ? 'badge-subtle' : 'badge-warning-subtle' }}">
                                    {{ $order->escrow_status == 'VERIFIED_VIA_OTP' ? 'Selesai Diambil' : 'Dana Ditahan di Escrow' }}
                                </span>
                            </div>
                            <div class="fw-semibold text-white mb-1">{{ $order->item->title ?? 'Surplus Bag' }}</div>
                            <div class="small text-muted mb-3">{{ $order->item->merchant->name ?? 'Mitra Bekasi' }}</div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: var(--border-subtle) !important;">
                                <div>
                                    <small class="text-dim d-block" style="font-size: 10px;">KODE OTP PENYERAHAN:</small>
                                    <span class="font-monospace fs-4 fw-bold text-success">{{ $order->pickup_otp }}</span>
                                </div>
                                <button class="btn btn-sm btn-outline-light rounded-2 px-3" onclick="showPassModal('{{ $order->order_code }}', '{{ $order->pickup_otp }}', '{{ $order->item->title ?? 'Mystery Bag' }}', '{{ $order->item->merchant->name ?? 'Mitra' }}')">
                                    Tampilkan QR Pass
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- VIEW 2: MERCHANT CASHIER VIEW                                     -->
        <!-- ================================================================= -->
        <div id="viewMerchant" class="d-none">
            <div class="card-surface p-4 mb-4">
                <div class="row align-items-center justify-content-between g-3">
                    <div class="col-md-8">
                        <span class="badge-warning-subtle mb-2 d-inline-block">Kasir & Point of Sale</span>
                        <h4 class="fw-bold text-white mb-1">Verifikasi OTP & Pencairan Escrow 85%</h4>
                        <p class="text-muted small mb-0">Input 6-digit OTP dari aplikasi pembeli saat serah-terima makanan. Dana escrow otomatis cair ke rekening mitra.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <small class="text-dim d-block">Saldo Mitra Siap Tarik (85%):</small>
                        <h3 class="fw-bold text-success mb-0" id="merchantBalance">Rp 170.000</h3>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card-surface p-4">
                        <h6 class="fw-semibold text-white mb-3">Input OTP Pengambilan</h6>
                        <div class="mb-3">
                            <input type="text" id="inputOtpKasir" maxlength="6" class="form-control form-control-lg form-control-dark text-center font-monospace fs-3 fw-bold" placeholder="782910">
                        </div>
                        <button class="btn btn-emerald w-100 py-2 mb-3" onclick="submitVerifyOtp()">
                            Validasi & Cairkan Escrow
                        </button>
                        <div class="small text-muted p-3 rounded-2" style="background: var(--bg-base); border: 1px solid var(--border-subtle); line-height: 1.5;">
                            <strong class="text-white d-block mb-1">Mekanisme Transaksi Atomik:</strong>
                            1. Validasi OTP memicu verifikasi hash di database.<br>
                            2. 85% nominal dicatat di ledger hak mitra.<br>
                            3. 15% dialokasikan sebagai biaya pemeliharaan platform.
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card-surface p-4">
                        <h6 class="fw-semibold text-white mb-3">Antrean Pesanan Masuk (Realtime)</h6>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover small mb-0" style="background: transparent;">
                                <thead>
                                    <tr class="text-dim" style="border-color: var(--border-subtle);">
                                        <th>Kode</th>
                                        <th>Pelanggan</th>
                                        <th>Item</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody style="border-color: var(--border-subtle);">
                                    @foreach($orders as $ord)
                                    <tr>
                                        <td class="font-monospace text-muted">{{ $ord->order_code }}</td>
                                        <td>{{ $ord->customer_name }}</td>
                                        <td>{{ $ord->item->title ?? 'Mystery Bag' }}</td>
                                        <td class="text-white">Rp {{ number_format($ord->total_amount) }}</td>
                                        <td>
                                            <span class="{{ $ord->escrow_status == 'VERIFIED_VIA_OTP' ? 'badge-subtle' : 'badge-warning-subtle' }}">
                                                {{ $ord->escrow_status == 'VERIFIED_VIA_OTP' ? 'Cair 85%' : 'Escrow Held' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- VIEW 3: DKPP ESG VIEW                                             -->
        <!-- ================================================================= -->
        <div id="viewDkpp" class="d-none">
            <div class="card-surface p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="badge-subtle mb-2 d-inline-block">Dinas Ketahanan Pangan & Perikanan Kota Bekasi</span>
                        <h4 class="fw-bold text-white mb-1">Pusat Data Emisi & Ketahanan Pangan Wilayah</h4>
                        <p class="text-muted small mb-0">Perhitungan dampak ilmiah berbasis metodologi <strong>Life Cycle Assessment (LCA) Standar FAO & KLHK</strong>.</p>
                    </div>
                    <button class="btn btn-outline-light btn-sm rounded-2 px-3" data-bs-toggle="modal" data-bs-target="#esgModal">
                        Cetak Sertifikat Kepatuhan ESG
                    </button>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-surface p-4">
                        <small class="text-dim text-uppercase fw-semibold" style="font-size: 11px;">Indeks Penyelamatan Pangan (FSI)</small>
                        <div class="stat-value my-2" style="color: #38bdf8;">{{ $impactSummary['fsi_percentage'] }}%</div>
                        <small class="text-muted">Target reduksi sampah organik TPA tercapai.</small>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-surface p-4">
                        <small class="text-dim text-uppercase fw-semibold" style="font-size: 11px;">Reduksi Gas Metana (CO₂e)</small>
                        <div class="stat-value my-2" style="color: #34d399;">{{ number_format($impactSummary['total_co2e_kg'], 1) }} kg</div>
                        <small class="text-muted">Faktor konversi 2.5x bobot makanan.</small>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-surface p-4">
                        <small class="text-dim text-uppercase fw-semibold" style="font-size: 11px;">Konservasi Air Virtual</small>
                        <div class="stat-value my-2" style="color: #818cf8;">{{ number_format($impactSummary['total_water_l']) }} L</div>
                        <small class="text-muted">Jejak air irigasi agrikultur terselamatkan.</small>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-surface p-4">
                        <small class="text-dim text-uppercase fw-semibold" style="font-size: 11px;">Serapan Kalori Gizi (AKG)</small>
                        <div class="stat-value my-2" style="color: #fbbf24;">81,250 kkal</div>
                        <small class="text-muted">Memenuhi kebutuhan 38 pekerja/hari.</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL CHECKOUT -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 p-0 mb-3">
                    <h6 class="modal-title fw-bold text-white">Pembayaran Escrow Midtrans Snap</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 rounded-2 mb-3" style="background: var(--bg-base); border: 1px solid var(--border-subtle);">
                        <div class="fw-semibold text-white mb-1" id="mItemTitle">Surprise Pastry Bag</div>
                        <small class="text-muted d-block" id="mMerchantName">Holland Bakery</small>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top" style="border-color: var(--border-subtle) !important;">
                            <span class="small text-muted">Total Pembayaran:</span>
                            <span class="fs-5 fw-bold text-white" id="mRescuePrice">Rp 25.000</span>
                        </div>
                    </div>

                    <div class="small text-muted mb-3" style="line-height: 1.5;">
                        <i class="bi bi-shield-check text-success me-1"></i> Dana Anda diamankan di rekening penampung (Escrow Pool). Merchant baru menerima pembayaran setelah Anda memvalidasi OTP di gerai.
                    </div>

                    <button class="btn btn-emerald w-100 py-2" onclick="executeLaravelCheckout()">
                        Bayar & Kunci Stok
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DIGITAL PASS -->
    <div class="modal fade" id="passModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-4 text-center">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge-subtle font-monospace" id="passCode">RFC-BKS-8912</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <h6 class="fw-bold text-white mb-1" id="passTitle">Surprise Bag</h6>
                <small class="text-muted d-block mb-3" id="passMerchant">Holland Bakery</small>

                <div class="p-3 rounded-3 mb-3" style="background: #ffffff; width: 150px; height: 150px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 100 100" class="w-100 h-100">
                        <rect x="10" y="10" width="25" height="25" rx="2" fill="#000"/>
                        <rect x="65" y="10" width="25" height="25" rx="2" fill="#000"/>
                        <rect x="10" y="65" width="25" height="25" rx="2" fill="#000"/>
                        <rect x="42" y="15" width="16" height="8" fill="#000"/>
                        <rect x="15" y="42" width="8" height="16" fill="#000"/>
                        <rect x="45" y="45" width="10" height="10" fill="#000"/>
                        <rect x="65" y="65" width="10" height="25" fill="#000"/>
                        <rect x="80" y="45" width="10" height="15" fill="#000"/>
                        <rect x="45" y="70" width="15" height="10" fill="#000"/>
                    </svg>
                </div>

                <div class="p-2 rounded-2 mb-2" style="background: var(--bg-base); border: 1px solid var(--border-subtle);">
                    <small class="text-dim d-block" style="font-size: 10px;">KODE OTP KASIR:</small>
                    <span class="fs-3 fw-bold font-monospace text-success" id="passOtp">782910</span>
                </div>
                <small class="text-dim" style="font-size: 11px;">Tunjukkan layar ini saat pengambilan.</small>
            </div>
        </div>
    </div>

    <!-- MODAL ESG -->
    <div class="modal fade" id="esgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-white mb-0">Sertifikat Kepatuhan ESG & Reduksi Food Waste</h6>
                        <small class="text-dim">Dinas Ketahanan Pangan dan Perikanan Kota Bekasi</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="small text-muted py-3" style="line-height: 1.6;">
                    Audit kepatuhan lingkungan berbasis formula Life Cycle Assessment (LCA) KLHK. Proyek ini memvalidasi penghematan <strong>{{ number_format($impactSummary['total_co2e_kg'], 1) }} kg CO₂e</strong> dan <strong>{{ number_format($impactSummary['total_water_l']) }} Liter</strong> air irigasi.
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: var(--border-subtle) !important;">
                    <span class="small text-dim font-monospace">REG: DKPP-BKS/ESG-2026/099182</span>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="window.print()">Cetak Dokumen</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentActiveItem = null;

        function switchRole(role) {
            document.getElementById('viewCustomer').classList.add('d-none');
            document.getElementById('viewMerchant').classList.add('d-none');
            document.getElementById('viewDkpp').classList.add('d-none');

            document.getElementById('btnRoleCustomer').classList.remove('active');
            document.getElementById('btnRoleMerchant').classList.remove('active');
            document.getElementById('btnRoleDkpp').classList.remove('active');

            if (role === 'customer') {
                document.getElementById('viewCustomer').classList.remove('d-none');
                document.getElementById('btnRoleCustomer').classList.add('active');
            } else if (role === 'merchant') {
                document.getElementById('viewMerchant').classList.remove('d-none');
                document.getElementById('btnRoleMerchant').classList.add('active');
            } else if (role === 'dkpp') {
                document.getElementById('viewDkpp').classList.remove('d-none');
                document.getElementById('btnRoleDkpp').classList.add('active');
            }
        }

        function showToast(msg) {
            document.getElementById('toastMessage').innerText = msg;
            const toastEl = document.getElementById('liveToast');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            try {
                // CartoDB Dark Matter tile layer: Sleek dark map that DOES NOT blind the eyes!
                const map = L.map('rescue-map').setView([-6.2383, 106.9850], 12);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '© OpenStreetMap contributors © CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(map);

                const merchantsData = [
                    { name: 'Holland Bakery Summarecon', lat: -6.223847, lng: 107.001248, item: 'Surprise Pastry Bag (Rp 25.000)' },
                    { name: 'Kopi Nako & Toast Galaxy', lat: -6.269412, lng: 106.974182, item: 'Mystery Toast Combo (Rp 24.000)' },
                    { name: 'Dapur Solo Bu Titik Kranji', lat: -6.230192, lng: 106.978211, item: 'Lauk Nusantara (Rp 15.000)' },
                    { name: 'Dough Deli Harapan Indah', lat: -6.182910, lng: 106.985210, item: 'Croissant Box (Rp 30.000)' }
                ];

                merchantsData.forEach(m => {
                    const marker = L.marker([m.lat, m.lng]).addTo(map);
                    marker.bindPopup(`<strong style="color:#111;">${m.name}</strong><br><span style="color:#059669; font-weight:600;">${m.item}</span>`);
                });
            } catch (err) {
                console.error("Map error:", err);
            }

            let sec = 41 * 60 + 28;
            setInterval(() => {
                if (sec > 0) {
                    sec--;
                    const m = Math.floor(sec / 60);
                    const s = sec % 60;
                    document.getElementById('flashTimer').innerText = 
                        `00:${m < 10 ? '0' + m : m}:${s < 10 ? '0' + s : s}`;
                }
            }, 1000);
        });

        function openCheckoutModal(id, title, merchant, rescuePrice, origPrice) {
            currentActiveItem = { id, title, merchant, rescuePrice, origPrice };
            document.getElementById('mItemTitle').innerText = title;
            document.getElementById('mMerchantName').innerText = merchant;
            document.getElementById('mRescuePrice').innerText = 'Rp ' + Number(rescuePrice).toLocaleString('id-ID');
            const myModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
            myModal.show();
        }

        function executeLaravelCheckout() {
            if (!currentActiveItem) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_id: currentActiveItem.id,
                    customer_name: 'Dimas (Mahasiswa)',
                    customer_phone: '081234567890'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    showToast(`Pembayaran Sukses! Dana Rp ${Number(data.data.total_amount).toLocaleString('id-ID')} terkunci di Escrow.`);
                    showPassModal(data.data.order_code, data.data.pickup_otp, data.data.item_title, currentActiveItem.merchant);
                    const stockEl = document.getElementById(`stock-${currentActiveItem.id}`);
                    if (stockEl) stockEl.innerText = `${data.data.remaining_stock} Kantong`;
                } else {
                    alert(data.message || 'Gagal memproses pesanan.');
                }
            })
            .catch(err => {
                console.error(err);
                alert("Koneksi backend error.");
            });
        }

        function showPassModal(code, otp, title, merchant) {
            document.getElementById('passCode').innerText = code;
            document.getElementById('passOtp').innerText = otp;
            document.getElementById('passTitle').innerText = title;
            document.getElementById('passMerchant').innerText = merchant;
            const modal = new bootstrap.Modal(document.getElementById('passModal'));
            modal.show();
        }

        function submitVerifyOtp() {
            const otpVal = document.getElementById('inputOtpKasir').value.trim();
            if (otpVal.length !== 6) {
                alert("Kode OTP wajib 6 digit angka.");
                return;
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/verify-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ otp: otpVal })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message);
                    document.getElementById('inputOtpKasir').value = '';
                    const balEl = document.getElementById('merchantBalance');
                    const curBal = 170000 + Number(data.data.payout_amount);
                    balEl.innerText = 'Rp ' + curBal.toLocaleString('id-ID');
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Gagal memverifikasi OTP.");
            });
        }
    </script>
</body>
</html>
