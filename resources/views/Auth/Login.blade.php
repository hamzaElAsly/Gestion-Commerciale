<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GestPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
            margin: 0;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #1a56db 0%, #6366f1 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            top: -100px; right: -100px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
            bottom: -80px; left: -60px;
        }

        .brand-logo {
            width: 70px; height: 70px;
            background: rgba(255,255,255,.15);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 900; color: white;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
        }

        .brand-title {
            font-size: 36px; font-weight: 800; color: white;
            margin-bottom: 12px;
        }

        .brand-desc {
            font-size: 15px; color: rgba(255,255,255,.7);
            text-align: center; max-width: 320px; line-height: 1.6;
        }

        .features {
            margin-top: 40px;
            list-style: none;
            padding: 0;
            width: 100%;
            max-width: 340px;
        }

        .features li {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 0;
            color: rgba(255,255,255,.85);
            font-size: 14px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .features li:last-child { border: none; }

        .features li i {
            width: 28px; height: 28px;
            background: rgba(255,255,255,.15);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }

        .login-right {
            width: 480px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 50px;
        }

        .login-title {
            font-size: 26px; font-weight: 800; color: #0f172a;
            margin-bottom: 6px;
        }

        .login-sub { font-size: 14px; color: #64748b; margin-bottom: 36px; }

        .form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26,86,219,.12);
            outline: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #1a56db, #6366f1);
            border: none; border-radius: 10px;
            padding: 13px; font-size: 15px; font-weight: 700;
            color: white; width: 100%; cursor: pointer;
            transition: opacity .15s, transform .15s;
        }

        .btn-login:hover { opacity: .92; transform: translateY(-1px); }

        .input-icon { position: relative; }
        .input-icon i {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%); color: #94a3b8;
            font-size: 16px; pointer-events: none;
        }
        .input-icon .form-control { padding-left: 42px; }

        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { width: 100%; padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <!-- Panneau gauche -->
    <div class="login-left">
        <div class="brand-logo">G</div>
        <div class="brand-title">GestPro</div>
        <div class="brand-desc">
            Application professionnelle de gestion commerciale, clients et stock en temps réel.
        </div>

        <ul class="features">
            <li>
                <i class="bi bi-people-fill"></i>
                Gestion complète des clients
            </li>
            <li>
                <i class="bi bi-box-seam-fill"></i>
                Catalogue produits & catégories
            </li>
            <li>
                <i class="bi bi-clock-history"></i>
                Historique des services réalisés
            </li>
            <li>
                <i class="bi bi-archive-fill"></i>
                Stock automatique en temps réel
            </li>
            <li>
                <i class="bi bi-file-earmark-pdf-fill"></i>
                Export PDF factures & rapports
            </li>
        </ul>
    </div>

    <!-- Panneau droit -->
    <div class="login-right">
        <div class="login-title">Bienvenue 👋</div>
        <div class="login-sub">Connectez-vous à votre espace de gestion</div>

        @if($errors->any())
        <div class="alert alert-danger" style="border-radius:10px;border:none;background:#fef2f2;color:#991b1b;font-size:13.5px;padding:12px 14px;margin-bottom:20px;">
            <i class="bi bi-exclamation-circle me-2"></i>
            Email ou mot de passe incorrect.
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label">Adresse email</label>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@gestpro.ma"
                           value="{{ old('email', 'admin@gestpro.ma') }}" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:13px;">Se souvenir de moi</label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
            </button>
        </form>

        <div class="mt-4 p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:4px;">
                <i class="bi bi-info-circle me-1"></i> Compte de démonstration
            </div>
            <div style="font-size:12px;color:#166534;">
                Email : <strong>admin@gestpro.ma</strong><br>
                Mot de passe : <strong>password</strong>
            </div>
        </div>
    </div>

</body>
</html>