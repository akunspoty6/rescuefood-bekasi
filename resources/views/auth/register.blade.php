<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — RescueFood Bekasi</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-base: #090a0f;
            --bg-surface: #11141c;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --emerald-accent: #10b981;
        }

        body {
            background-color: var(--bg-base);
            color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 1rem;
            width: 100%;
            max-width: 460px;
            padding: 2.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .form-control-dark, .form-select-dark {
            background: #090a0f;
            border: 1px solid var(--border-subtle);
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
        }

        .form-control-dark:focus, .form-select-dark:focus {
            background: #090a0f;
            border-color: var(--emerald-accent);
            color: #fff;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .btn-emerald {
            background-color: var(--emerald-accent);
            border: none;
            color: #062419;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            padding: 0.7rem;
            width: 100%;
            transition: all 0.15s ease;
        }

        .btn-emerald:hover {
            background-color: #34d399;
            color: #041d14;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px;">
                <i class="bi bi-box2-heart-fill text-success fs-5"></i>
            </div>
            <h5 class="fw-bold text-white mb-1">Daftar Akun Baru</h5>
            <small class="text-muted">Bergabung dalam ekosistem penyelamatan surplus pangan</small>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small mb-3" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #f87171;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-muted mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-control form-control-dark" placeholder="Rafi Ahmad">
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-control form-control-dark" placeholder="rafi@bekasicore.local">
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted mb-1">Nomor WhatsApp / HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="form-control form-control-dark" placeholder="081299887766">
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted mb-1">Peran Pengguna</label>
                <select name="role" required class="form-select form-select-dark">
                    <option value="customer" selected>Penyelamat Makanan (Customer)</option>
                    <option value="merchant">Kasir / Mitra Kuliner (Merchant)</option>
                </select>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Kata Sandi</label>
                    <input type="password" name="password" required class="form-control form-control-dark" placeholder="••••••••">
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" required class="form-control form-control-dark" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-emerald mb-3">
                Daftar Sekarang
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">Sudah punya akun? <a href="/login" class="text-decoration-none" style="color: var(--emerald-accent);">Masuk di sini</a></small>
        </div>
    </div>

</body>
</html>
