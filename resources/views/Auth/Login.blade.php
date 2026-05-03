<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GestPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--blue:#2563eb;--blue-d:#1d4ed8;--indigo:#4f46e5;--slate:#0f172a;--muted:#64748b;--border:#e2e8f0;--danger:#ef4444;--success:#059669}
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;display:flex;background:var(--slate);overflow:hidden}

        /* ──── PANNEAU GAUCHE ──── */
        .auth-left{flex:1;position:relative;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px 64px;background:linear-gradient(150deg,#1e3a8a 0%,#3730a3 45%,#1e1b4b 100%);overflow:hidden}
        .auth-left::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:48px 48px}
        .orb{position:absolute;border-radius:50%;filter:blur(70px);animation:drift 12s ease-in-out infinite;pointer-events:none}
        .orb-1{width:500px;height:500px;background:rgba(99,102,241,.35);top:-180px;right:-130px;animation-delay:0s}
        .orb-2{width:340px;height:340px;background:rgba(56,189,248,.22);bottom:-90px;left:-90px;animation-delay:-5s}
        .orb-3{width:220px;height:220px;background:rgba(167,139,250,.28);top:55%;left:20%;animation-delay:-9s}
        @keyframes drift{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(18px,-22px) scale(1.05)}66%{transform:translate(-12px,16px) scale(.97)}}

        .lc{position:relative;z-index:2;width:100%;max-width:380px}
        .logo-badge{width:68px;height:68px;border-radius:20px;background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.22);backdrop-filter:blur(12px);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;color:white;margin-bottom:28px;box-shadow:0 8px 32px rgba(0,0,0,.2);letter-spacing:-.02em}
        .lt{font-size:42px;font-weight:900;color:white;letter-spacing:-.03em;line-height:1.05;margin-bottom:12px}
        .ls{font-size:15px;color:rgba(255,255,255,.6);line-height:1.65;margin-bottom:44px}
        .fc{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:16px 18px;margin-bottom:10px;display:flex;align-items:center;gap:16px;backdrop-filter:blur(6px);transition:background .2s,transform .2s}
        .fc:hover{background:rgba(255,255,255,.11);transform:translateX(4px)}
        .fi-box{width:40px;height:40px;border-radius:11px;background:rgba(255,255,255,.14);display:flex;align-items:center;justify-content:center;font-size:17px;color:rgba(255,255,255,.9);flex-shrink:0}
        .ft strong{display:block;font-size:13.5px;font-weight:700;color:white;margin-bottom:2px}
        .ft span{font-size:12px;color:rgba(255,255,255,.5)}
        .sr{display:flex;gap:14px;margin-top:28px}
        .sp{flex:1;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:14px;text-align:center}
        .sp .sv{font-size:20px;font-weight:800;color:white}
        .sp .sl{font-size:10.5px;color:rgba(255,255,255,.45);margin-top:3px;text-transform:uppercase;letter-spacing:.04em}

        /* ──── PANNEAU DROIT ──── */
        .auth-right{width:520px;min-height:100vh;background:white;display:flex;flex-direction:column;justify-content:center;padding:56px 52px;overflow-y:auto;position:relative}
        .auth-right::after{content:'';position:absolute;bottom:-50px;left:-50px;width:160px;height:160px;border-radius:50%;background:linear-gradient(135deg,rgba(37,99,235,.05),rgba(79,70,229,.05));pointer-events:none}

        .step-lbl{font-size:11.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--blue);margin-bottom:10px;display:flex;align-items:center;gap:7px}
        .step-lbl::before{content:'';width:20px;height:2px;background:var(--blue);border-radius:2px}
        .fh{font-size:30px;font-weight:900;color:var(--slate);letter-spacing:-.02em;margin-bottom:6px}
        .fsub{font-size:14.5px;color:var(--muted);margin-bottom:32px}

        .field{margin-bottom:18px}
        .flabel{display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:7px;letter-spacing:.01em}
        .fw{position:relative}
        .fw .ic{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;pointer-events:none;transition:color .15s}
        .fw:focus-within .ic{color:var(--blue)}
        .fw .eye{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;padding:4px;color:#94a3b8;cursor:pointer;font-size:16px;transition:color .15s}
        .fw .eye:hover{color:var(--blue)}
        .fi{width:100%;padding:12.5px 14px 12.5px 42px;border:1.5px solid var(--border);border-radius:11px;font-size:14px;font-family:inherit;color:var(--slate);outline:none;transition:border-color .15s,box-shadow .15s,background .15s;background:#fcfcfd}
        .fi:focus{border-color:var(--blue);box-shadow:0 0 0 3.5px rgba(37,99,235,.11);background:white}
        .fi.err{border-color:var(--danger)}
        .fi.err:focus{box-shadow:0 0 0 3.5px rgba(239,68,68,.11)}
        .err-txt{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--danger);margin-top:5px}

        .row-opts{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px}
        .c-chk{display:flex;align-items:center;gap:8px}
        .c-chk input{accent-color:var(--blue);width:15px;height:15px;cursor:pointer}
        .c-chk label{font-size:13px;color:var(--muted);cursor:pointer;user-select:none}

        .btn-auth{width:100%;padding:14px;border:none;border-radius:12px;font-size:15px;font-weight:700;font-family:inherit;color:white;cursor:pointer;background:linear-gradient(135deg,var(--blue) 0%,var(--indigo) 100%);display:flex;align-items:center;justify-content:center;gap:9px;transition:opacity .15s,transform .2s,box-shadow .2s;box-shadow:0 4px 18px rgba(37,99,235,.3)}
        .btn-auth:hover{opacity:.92;transform:translateY(-2px);box-shadow:0 8px 26px rgba(37,99,235,.4)}
        .btn-auth:active{transform:translateY(0)}

        .alert-err{display:flex;align-items:center;gap:10px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:11px;padding:13px 15px;color:#991b1b;font-size:13.5px;margin-bottom:22px;animation:shake .35s}
        @keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

        .divider{display:flex;align-items:center;gap:14px;color:var(--muted);font-size:13px;margin:22px 0}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}

        .link-row{text-align:center;font-size:14px;color:var(--muted);margin-top:8px}
        .link-row a{color:var(--blue);font-weight:700;text-decoration:none}
        .link-row a:hover{text-decoration:underline}

        .demo-card{margin-top:24px;background:linear-gradient(135deg,#f0fdf4,#ecfdf5);border:1.5px solid #86efac;border-radius:12px;padding:14px 16px}
        .demo-card .dct{font-size:12px;font-weight:700;color:var(--success);margin-bottom:7px;display:flex;align-items:center;gap:6px}
        .demo-card .dcr{display:flex;gap:0;flex-direction:column;gap:3px}
        .demo-card .dcr span{font-size:12.5px;color:#166534}
        .demo-card .dcr strong{font-weight:700}

        @media(max-width:960px){.auth-left{display:none}.auth-right{width:100%}}
        @media(max-width:480px){.auth-right{padding:40px 22px}}
    </style>
</head>
<body>

<!-- ════ GAUCHE ════ -->
<div class="auth-left">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="lc">
        <div class="logo-badge">G</div>
        <h1 class="lt">GestPro</h1>
        <p class="ls">La solution complète pour piloter votre activité commerciale, votre stock et vos devis.</p>

        <div class="fc">
            <div class="fi-box"><i class="bi bi-people-fill"></i></div>
            <div class="ft"><strong>Clients & ICE</strong><span>Fiches, historique, statistiques</span></div>
        </div>
        <div class="fc">
            <div class="fi-box"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="ft"><strong>Devis & Facturation PDF</strong><span>Impression professionnelle instantanée</span></div>
        </div>
        <div class="fc">
            <div class="fi-box"><i class="bi bi-archive-fill"></i></div>
            <div class="ft"><strong>Stock automatique</strong><span>Décrément en temps réel par service</span></div>
        </div>
        <div class="fc">
            <div class="fi-box"><i class="bi bi-clock-history"></i></div>
            <div class="ft"><strong>Historique des services</strong><span>Rapports mensuels PDF exportables</span></div>
        </div>

        <div class="sr">
            <div class="sp"><div class="sv">100%</div><div class="sl">Sécurisé</div></div>
            <div class="sp"><div class="sv">PDF</div><div class="sl">Export</div></div>
            <div class="sp"><div class="sv">MVC</div><div class="sl">Laravel 10</div></div>
        </div>
    </div>
</div>

<!-- ════ DROITE ════ -->
<div class="auth-right">
    <div class="step-lbl">Authentification</div>
    <h1 class="fh">Bon retour 👋</h1>
    <p class="fsub">Connectez-vous à votre espace de gestion</p>

    @if($errors->any())
    <div class="alert-err">
        <i class="bi bi-exclamation-circle-fill" style="font-size:18px;flex-shrink:0;"></i>
        <span>Email ou mot de passe incorrect. Veuillez réessayer.</span>
    </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="field">
            <label class="flabel" for="email">Adresse email</label>
            <div class="fw">
                <i class="bi bi-envelope ic"></i>
                <input type="email" id="email" name="email"
                       class="fi {{ $errors->has('email') ? 'err' : '' }}"
                       placeholder="vous@exemple.ma"
                       value="{{ old('email', 'admin@gestpro.ma') }}"
                       autocomplete="email" required autofocus>
            </div>
        </div>

        <div class="field">
            <label class="flabel" for="pw">Mot de passe</label>
            <div class="fw">
                <i class="bi bi-lock ic"></i>
                <input type="password" id="pw" name="password"
                       class="fi" placeholder="••••••••"
                       autocomplete="current-password" required>
                <button type="button" class="eye" onclick="togglePw('pw','epw')">
                    <i class="bi bi-eye" id="epw"></i>
                </button>
            </div>
        </div>

        <div class="row-opts">
            <div class="c-chk">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-box-arrow-in-right"></i>
            Se connecter
        </button>
    </form>

    <div class="divider">ou</div>
    <div class="link-row">
        Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a>
    </div>

    <div class="demo-card">
        <div class="dct"><i class="bi bi-shield-check-fill"></i> Compte démo</div>
        <div class="dcr">
            <span><strong>Email :</strong> admin@gestpro.ma</span>
            <span><strong>Mot de passe :</strong> password</span>
        </div>
    </div>
</div>

<script>
function togglePw(i, e) {
    const inp = document.getElementById(i);
    const ic  = document.getElementById(e);
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    ic.className = inp.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
</body>
</html>