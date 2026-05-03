<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — GestPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #2563eb; --blue-dark: #1d4ed8;
            --indigo: #4f46e5; --emerald: #059669;
            --slate: #0f172a; --muted: #64748b;
            --border: #e2e8f0; --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh; display: flex;
            background: var(--slate);
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            width: 420px; position: relative; overflow: hidden;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 60px 48px;
            background: linear-gradient(145deg, #064e3b 0%, #065f46 40%, #047857 100%);
        }
        .blob {
            position: absolute; border-radius: 50%;
            opacity: .12; animation: float 9s ease-in-out infinite;
        }
        .blob-1 { width:380px;height:380px;background:#6ee7b7;top:-120px;right:-100px;animation-delay:0s; }
        .blob-2 { width:240px;height:240px;background:#34d399;bottom:-50px;left:-50px;animation-delay:-4s; }
        @keyframes float {
            0%,100%{transform:translateY(0) scale(1);}
            50%{transform:translateY(-20px) scale(1.04);}
        }
        .panel-left::before {
            content:''; position:absolute; inset:0;
            background-image: radial-gradient(rgba(255,255,255,.07) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .logo-wrap {
            position:relative;z-index:1;
            width:66px;height:66px;border-radius:20px;
            background:rgba(255,255,255,.12);
            border:1.5px solid rgba(255,255,255,.2);
            display:flex;align-items:center;justify-content:center;
            font-size:28px;font-weight:900;color:white;
            backdrop-filter:blur(8px); margin-bottom:24px;
        }
        .brand-name {
            position:relative;z-index:1;
            font-size:34px;font-weight:800;color:white;
            letter-spacing:-.02em;margin-bottom:8px;
        }
        .brand-tagline {
            position:relative;z-index:1;
            font-size:14px;color:rgba(255,255,255,.65);
            text-align:center;max-width:280px;line-height:1.6;
            margin-bottom:40px;
        }

        /* Steps */
        .steps {
            position:relative;z-index:1;
            width:100%;max-width:300px;
        }
        .step {
            display:flex;align-items:flex-start;gap:14px;
            padding:14px 0;border-bottom:1px solid rgba(255,255,255,.08);
        }
        .step:last-child{border:none;}
        .step-num {
            width:30px;height:30px;border-radius:50%;
            background:rgba(255,255,255,.18);
            display:flex;align-items:center;justify-content:center;
            font-size:13px;font-weight:700;color:white;flex-shrink:0;
        }
        .step-num.done {
            background:rgba(255,255,255,.3);
        }
        .step-text .step-title {
            font-size:13.5px;font-weight:600;color:rgba(255,255,255,.9);
            margin-bottom:2px;
        }
        .step-text .step-desc {
            font-size:12px;color:rgba(255,255,255,.5);
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            flex:1;background:white;
            display:flex;flex-direction:column;
            justify-content:center;padding:52px 60px;
            overflow-y:auto;
        }

        .form-header{margin-bottom:32px;}
        .form-header h1{font-size:26px;font-weight:800;color:var(--slate);margin-bottom:5px;}
        .form-header p{font-size:14px;color:var(--muted);}

        .row-2 { display:grid;grid-template-columns:1fr 1fr;gap:16px; }

        .form-group{margin-bottom:18px;}
        .form-label{
            display:block;font-size:13px;font-weight:600;
            color:#374151;margin-bottom:7px;
        }
        .input-wrap{position:relative;}
        .input-wrap .ic {
            position:absolute;left:13px;top:50%;
            transform:translateY(-50%);
            color:#94a3b8;font-size:15px;pointer-events:none;
        }
        .input-wrap .toggle-pw{
            position:absolute;right:13px;top:50%;
            transform:translateY(-50%);
            color:#94a3b8;font-size:15px;cursor:pointer;
            background:none;border:none;padding:0;
        }
        .form-ctrl {
            width:100%;padding:11.5px 13px 11.5px 40px;
            border:1.5px solid var(--border);border-radius:10px;
            font-size:13.5px;font-family:inherit;
            outline:none;transition:border-color .15s,box-shadow .15s;
            color:var(--slate);
        }
        .form-ctrl:focus{
            border-color:var(--emerald);
            box-shadow:0 0 0 3.5px rgba(5,150,105,.12);
        }
        .form-ctrl.is-invalid{border-color:var(--danger);}
        .err-msg{font-size:12px;color:var(--danger);margin-top:5px;}

        /* strength bar */
        .strength-bar-wrap{margin-top:8px;}
        .strength-bars{display:flex;gap:4px;margin-bottom:4px;}
        .strength-bars span{
            flex:1;height:3px;border-radius:2px;
            background:var(--border);transition:background .3s;
        }
        .strength-label{font-size:11px;color:var(--muted);}

        /* terms */
        .terms-row{
            display:flex;align-items:flex-start;gap:10px;
            margin-bottom:22px;
        }
        .terms-row input[type=checkbox]{
            accent-color:var(--emerald);
            width:15px;height:15px;margin-top:2px;flex-shrink:0;
        }
        .terms-row label{font-size:12.5px;color:var(--muted);line-height:1.5;}
        .terms-row a{color:var(--blue);text-decoration:none;font-weight:600;}

        .btn-submit{
            width:100%;padding:13px;
            background:linear-gradient(135deg,var(--emerald) 0%,#047857 100%);
            border:none;border-radius:11px;
            font-size:15px;font-weight:700;font-family:inherit;
            color:white;cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:8px;
            transition:opacity .15s,transform .15s;
        }
        .btn-submit:hover{opacity:.9;transform:translateY(-1px);}
        .btn-submit:active{transform:translateY(0);}

        .divider{
            display:flex;align-items:center;gap:12px;
            margin:20px 0;color:var(--muted);font-size:13px;
        }
        .divider::before,.divider::after{
            content:'';flex:1;height:1px;background:var(--border);
        }

        .link-row{
            text-align:center;font-size:13.5px;color:var(--muted);
            margin-top:16px;
        }
        .link-row a{color:var(--blue);font-weight:600;text-decoration:none;}
        .link-row a:hover{text-decoration:underline;}

        .alert-err{
            display:flex;align-items:flex-start;gap:10px;
            background:#fef2f2;border:1px solid #fecaca;
            border-radius:10px;padding:12px 14px;
            color:#991b1b;font-size:13px;margin-bottom:18px;
        }

        @media(max-width:900px){
            .panel-left{display:none;}
            .panel-right{padding:36px 24px;}
            .row-2{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>

<!-- ═══ GAUCHE ═══ -->
<div class="panel-left">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="logo-wrap">G</div>
    <div class="brand-name">GestPro</div>
    <div class="brand-tagline">
        Créez votre compte et commencez à gérer votre activité dès maintenant.
    </div>

    <div class="steps">
        <div class="step">
            <div class="step-num done">1</div>
            <div class="step-text">
                <div class="step-title">Créer votre compte</div>
                <div class="step-desc">Renseignez vos informations</div>
            </div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text">
                <div class="step-title">Accéder au tableau de bord</div>
                <div class="step-desc">Vue d'ensemble instantanée</div>
            </div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text">
                <div class="step-title">Gérer clients & stock</div>
                <div class="step-desc">CRUD complet + PDF</div>
            </div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-text">
                <div class="step-title">Générer devis & factures</div>
                <div class="step-desc">Impression en un clic</div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ DROITE ═══ -->
<div class="panel-right">
    <div class="form-header">
        <h1>Créer un compte ✨</h1>
        <p>Quelques secondes suffisent pour démarrer</p>
    </div>

    @if($errors->any())
    <div class="alert-err">
        <i class="bi bi-exclamation-circle-fill" style="font-size:16px;flex-shrink:0;margin-top:1px;"></i>
        <div>
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Nom complet <span style="color:var(--danger)">*</span></label>
            <div class="input-wrap">
                <i class="bi bi-person ic"></i>
                <input type="text" name="name"
                       class="form-ctrl {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       placeholder="Mohamed Alami"
                       value="{{ old('name') }}" required autofocus>
            </div>
            @error('name')<div class="err-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Adresse email <span style="color:var(--danger)">*</span></label>
            <div class="input-wrap">
                <i class="bi bi-envelope ic"></i>
                <input type="email" name="email"
                       class="form-ctrl {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       placeholder="votre@email.ma"
                       value="{{ old('email') }}" required>
            </div>
            @error('email')<div class="err-msg">{{ $message }}</div>@enderror
        </div>

        <div class="row-2">
            <div class="form-group">
                <label class="form-label">Mot de passe <span style="color:var(--danger)">*</span></label>
                <div class="input-wrap">
                    <i class="bi bi-lock ic"></i>
                    <input type="password" name="password" id="pw1"
                           class="form-ctrl {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Min. 8 caractères"
                           oninput="checkStrength(this.value)" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw1','eye1')">
                        <i class="bi bi-eye" id="eye1"></i>
                    </button>
                </div>
                <div class="strength-bar-wrap">
                    <div class="strength-bars">
                        <span id="s1"></span><span id="s2"></span>
                        <span id="s3"></span><span id="s4"></span>
                    </div>
                    <div class="strength-label" id="slabel"></div>
                </div>
                @error('password')<div class="err-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer mot de passe <span style="color:var(--danger)">*</span></label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill ic"></i>
                    <input type="password" name="password_confirmation" id="pw2"
                           class="form-ctrl"
                           placeholder="Répéter"
                           oninput="checkMatch()" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw2','eye2')">
                        <i class="bi bi-eye" id="eye2"></i>
                    </button>
                </div>
                <div class="err-msg" id="match-msg" style="display:none;">Les mots de passe ne correspondent pas.</div>
            </div>
        </div>

        <div class="terms-row">
            <input type="checkbox" id="terms" required>
            <label for="terms">
                J'accepte les <a href="#">conditions d'utilisation</a> et la
                <a href="#">politique de confidentialité</a> de GestPro.
            </label>
        </div>

        <button type="submit" class="btn-submit" id="btn-register">
            <i class="bi bi-person-check-fill"></i>
            Créer mon compte
        </button>
    </form>

    <div class="divider">ou</div>

    <div class="link-row">
        Déjà un compte ?
        <a href="{{ route('login') }}">Se connecter</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePw(id, iconId) {
    const el = document.getElementById(id);
    const ic = document.getElementById(iconId);
    el.type = el.type === 'password' ? 'text' : 'password';
    ic.className = el.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const bars   = [document.getElementById('s1'), document.getElementById('s2'),
                    document.getElementById('s3'), document.getElementById('s4')];
    const label  = document.getElementById('slabel');
    const levels = [
        { re: /.{1}/, color: '#ef4444', text: 'Très faible' },
        { re: /.{6}/, color: '#f97316', text: 'Faible' },
        { re: /(?=.*[A-Z])(?=.*[0-9]).{8}/, color: '#eab308', text: 'Moyen' },
        { re: /(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{10}/, color: '#22c55e', text: 'Fort' },
    ];
    let score = 0;
    levels.forEach((l, i) => { if (l.re.test(val)) score = i + 1; });
    bars.forEach((b, i) => {
        b.style.background = i < score ? levels[score - 1].color : '#e2e8f0';
    });
    label.textContent = val.length ? levels[score - 1].text : '';
    label.style.color = score ? levels[score - 1].color : '';
}

function checkMatch() {
    const v1  = document.getElementById('pw1').value;
    const v2  = document.getElementById('pw2').value;
    const msg = document.getElementById('match-msg');
    msg.style.display = (v2.length && v1 !== v2) ? 'block' : 'none';
}
</script>
</body>
</html>