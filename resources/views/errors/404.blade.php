<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — Charrak Technology</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            background:
                radial-gradient(circle at 15% 20%, rgba(37, 99, 235, .16), transparent 30%),
                radial-gradient(circle at 85% 80%, rgba(99, 102, 241, .14), transparent 30%),
                #f1f5f9;
        }
        .error-card {
            width: min(680px, 100%);
            padding: 48px;
            text-align: center;
            background: rgba(255, 255, 255, .94);
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .12);
        }
        .logo { width: 76px; height: 76px; object-fit: contain; margin-bottom: 20px; }
        .code {
            margin: 0;
            font-size: clamp(72px, 18vw, 140px);
            line-height: .9;
            font-weight: 800;
            letter-spacing: -.08em;
            color: #1a56db;
        }
        h1 { margin: 24px 0 10px; font-size: clamp(25px, 5vw, 36px); }
        p { margin: 0 auto 30px; max-width: 500px; color: #64748b; line-height: 1.7; }
        .actions { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 46px;
            padding: 0 20px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            color: #334155;
            background: white;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-primary { border-color: #1a56db; color: white; background: #1a56db; }
        .btn:hover { transform: translateY(-1px); }
        @media (max-width: 520px) { .error-card { padding: 36px 20px; } }
    </style>
</head>
<body>
    <main class="error-card">
        <img class="logo" src="{{ asset('images/Logo.png') }}" alt="Charrak Technology">
        <div class="code">404</div>
        <h1>Page introuvable</h1>
        <p>La page demandée n’existe pas, a été déplacée ou n’est actuellement pas disponible.</p>
        <div class="actions">
            <button type="button" class="btn" onclick="history.back()">
                <i class="bi bi-arrow-left"></i> Page précédente
            </button>
            @auth
                <a class="btn btn-primary" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid"></i> Tableau de bord
                </a>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </a>
            @endauth
        </div>
    </main>
</body>
</html>
