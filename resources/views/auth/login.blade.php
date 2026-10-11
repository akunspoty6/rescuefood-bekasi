<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — RescueFood Bekasi</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-base: #090a0f;
            --bg-surface: #11141c;
            --bg-subtle: #171b26;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-focus: rgba(16, 185, 129, 0.5);
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
            max-width: 440px;
            padding: 2.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .form-control-dark {
            background: #090a0f;
            border: 1px solid var(--border-subtle);
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
        }

        .form-control-dark:focus {
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

        .demo-pill {
            background: #090a0f;
            border: 1px solid var(--border-subtle);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
            width: 100%;
        }

        .demo-pill:hover {
            border-color: rgba(16, 185, 129, 0.4);
            background: #0e121a;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <!-- BRAND -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px;">
                <i class="bi bi-box2-heart-fill text-success fs-5"></i>
            </div>
            <h5 class="fw-bold text-white mb-1">RescueFood<span style="color: var(--emerald-accent);">.id</span></h5>
            <small class="text-muted">Portal Autentikasi Sistem Kota Bekasi</small>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 small mb-3 text-center" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); color: #34d399;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 small mb-3" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #f87171;">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- LOGIN FORM -->
        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-muted mb-1">Email</label>
                <input type="email" name="email" id="inputEmail" value="{{ old('email') }}" required class="form-control form-control-dark" placeholder="nama@email.com">
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small text-muted mb-0">Kata Sandi</label>
                </div>
                <input type="password" name="password" id="inputPassword" required class="form-control form-control-dark" placeholder="••••••••">
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input bg-dark border-secondary" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label small text-muted" for="rememberMe">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn btn-emerald mb-3">
                Masuk ke Aplikasi
            </button>
        </form>

        <!-- DEMO ACCOUNTS QUICK FILL -->
        <div class="pt-3 border-top" style="border-color: var(--border-subtle) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted fw-semibold" style="font-size: 11px;">KREDENSIAL DEMO (KLIK UNTUK ISI):</small>
                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #34d399; font-size: 10px;">Seed Data</span>
            </div>

            <div class="d-flex flex-column gap-2">
                <button type="button" class="demo-pill" onclick="fillDemo('dimas@penyelamat.id', 'password123')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small fw-semibold text-white d-block">Dimas Pratama</span>
                            <span class="text-muted" style="font-size: 11px;">dimas@penyelamat.id</span>
                        </div>
                        <span class="badge bg-secondary text-uppercase" style="font-size: 9px;">Penyelamat</span>
                    </div>
                </button>

                <button type="button" class="demo-pill" onclick="fillDemo('kasir@holland.id', 'password123')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small fw-semibold text-white d-block">Kasir Holland Bakery</span>
                            <span class="text-muted" style="font-size: 11px;">kasir@holland.id</span>
                        </div>
                        <span class="badge bg-warning text-dark text-uppercase" style="font-size: 9px;">Kasir</span>
                    </div>
                </button>

                <button type="button" class="demo-pill" onclick="fillDemo('officer@dkpp.bekasi.go.id', 'password123')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small fw-semibold text-white d-block">Ir. Bambang Sutrisno</span>
                            <span class="text-muted" style="font-size: 11px;">officer@dkpp.bekasi.go.id</span>
                        </div>
                        <span class="badge bg-info text-dark text-uppercase" style="font-size: 9px;">DKPP</span>
                    </div>
                </button>
            </div>
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">Belum punya akun? <a href="/register" class="text-decoration-none" style="color: var(--emerald-accent);">Daftar di sini</a></small>
        </div>
    </div>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('inputEmail').value = email;
            document.getElementById('inputPassword').value = pass;
        }
    </script>
</body>
</html>
