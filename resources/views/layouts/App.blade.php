<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion Commerciale') — GestPro</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1245b8;
            --primary-light: #e8f0fe;
            --secondary: #6366f1;
            --success: #059669;
            --danger: #dc2626;
            --warning: #d97706;
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #1a56db;
            --topbar-height: 64px;
            --body-bg: #f1f5f9;
            --card-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 12px rgba(0,0,0,.05);
            --radius: 12px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--body-bg);
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            transition: transform .25s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: var(--topbar-height);
        }

        .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 700;
            color: #f8fafc;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 11px;
            color: var(--sidebar-text);
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #475569;
            padding: 8px 12px 4px;
            margin-top: 8px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 9px;
            color: var(--sidebar-text);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all .15s ease;
            margin-bottom: 2px;
            position: relative;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: #f1f5f9;
        }

        .sidebar-nav .nav-link.active {
            background: rgba(26,86,219,.18);
            color: #60a5fa;
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 20px;
            background: var(--primary);
            border-radius: 2px;
        }

        .sidebar-nav .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .badge-alert {
            margin-left: auto;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 20px;
            background: #dc2626;
            color: white;
            font-weight: 700;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            z-index: 900;
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            flex: 1;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            background: white;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 22px;
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
        }

        .card-body { padding: 22px; }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border-radius: var(--radius);
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 18px;
            border: none;
            box-shadow: var(--card-shadow);
            background: white;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,.1);
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.blue { background: #eff6ff; color: var(--primary); }
        .stat-icon.green { background: #f0fdf4; color: var(--success); }
        .stat-icon.purple { background: #f5f3ff; color: var(--secondary); }
        .stat-icon.orange { background: #fff7ed; color: var(--warning); }
        .stat-icon.red { background: #fef2f2; color: var(--danger); }
        .stat-icon.teal { background: #f0fdfa; color: #0d9488; }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .stat-label {
            font-size: 12.5px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ===== TABLES ===== */
        .table {
            font-size: 13.5px;
        }

        .table thead th {
            background: #f8fafc;
            font-weight: 700;
            font-size: 11.5px;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }

        .table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: #f8fafc; }

        /* ===== BUTTONS ===== */
        .btn {
            font-size: 13.5px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all .15s ease;
        }

        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        .btn-sm { padding: 5px 11px; font-size: 12.5px; }

        /* ===== BADGES ===== */
        .badge {
            font-weight: 600;
            font-size: 11.5px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* ===== STOCK BADGES ===== */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .stock-badge.normal { background: #f0fdf4; color: #166534; }
        .stock-badge.faible { background: #fff7ed; color: #92400e; }
        .stock-badge.rupture { background: #fef2f2; color: #991b1b; }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 13.5px;
            font-weight: 500;
        }

        .alert-success { background: #f0fdf4; color: #166534; }
        .alert-danger { background: #fef2f2; color: #991b1b; }
        .alert-warning { background: #fffbeb; color: #92400e; }

        /* ===== FORMS ===== */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 13.5px;
            padding: 9px 14px;
            transition: border-color .15s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,86,219,.12);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        /* ===== SEARCH ===== */
        .search-box {
            position: relative;
        }
        .search-box .form-control {
            padding-left: 38px;
        }
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .page-header .breadcrumb {
            font-size: 12.5px;
            margin: 4px 0 0;
        }

        /* ===== AVATAR ===== */
        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex-shrink: 0;
        }

        /* ===== SIDEBAR FOOTER ===== */
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 9px;
            cursor: pointer;
            transition: background .15s;
            color: var(--sidebar-text);
            text-decoration: none;
        }

        .sidebar-user:hover { background: rgba(255,255,255,.06); }

        .sidebar-user .user-info { flex: 1; }
        .sidebar-user .user-name { font-size: 13px; font-weight: 600; color: #f1f5f9; display: block; }
        .sidebar-user .user-role { font-size: 11px; color: #64748b; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
        }

        /* ===== MONEY FORMAT ===== */
        .money {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13.5px;
            font-weight: 500;
        }

        /* ===== PRODUCT ROW ===== */
        #produits-container .produit-row {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
            transition: border-color .15s;
        }

        #produits-container .produit-row:hover {
            border-color: var(--primary);
        }

        /* ===== TOOLTIP ===== */
        .info-tooltip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11.5px;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ===== SIDEBAR ===== -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">G</div>
            <div>
                <div class="brand-name">GestPro</div>
                <div class="brand-sub">Gestion Commerciale</div>
            </div>
        </div>

        <div class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Tableau de bord
            </a>

            <div class="nav-section-title">Commercial</div>

            <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clients
            </a>

            <a href="{{ route('historique.index') }}" class="nav-link {{ request()->routeIs('historique.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Historique Services
            </a>

            <a href="{{ route('historique.mensuel') }}" class="nav-link {{ request()->routeIs('historique.mensuel') ? 'active' : '' }}">
                <i class="bi bi-calendar-month-fill"></i> Rapport Mensuel
            </a>

            <div class="nav-section-title">Catalogue</div>

            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Catégories
            </a>

            <a href="{{ route('produits.index') }}" class="nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i> Produits
            </a>

            <div class="nav-section-title">Stock</div>

            <a href="{{ route('stock.etat') }}" class="nav-link {{ request()->routeIs('stock.etat') ? 'active' : '' }}">
                <i class="bi bi-archive-fill"></i> État du Stock
                @php $alertes = \App\Models\Produit::stockFaible()->count(); @endphp
                @if($alertes > 0)
                    <span class="badge-alert">{{ $alertes }}</span>
                @endif
            </a>

            <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i> Mouvements Stock
            </a>
        </div>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();"
                   class="sidebar-user">
                    <div class="avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="user-role">Administrateur</span>
                    </div>
                    <i class="bi bi-box-arrow-right" style="font-size: 14px;"></i>
                </a>
            </form>
        </div>
    </nav>

    <!-- ===== TOPBAR ===== -->
    <header class="topbar">
        <button class="btn btn-sm btn-light d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="bi bi-list" style="font-size: 18px;"></i>
        </button>

        <div class="topbar-title">@yield('page-title', 'Tableau de bord')</div>

        <div class="topbar-actions">
            @php $produitsAlerte = \App\Models\Produit::stockFaible()->count(); @endphp
            @if($produitsAlerte > 0)
            <a href="{{ route('stock.etat') }}" class="btn btn-sm btn-danger position-relative">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ $produitsAlerte }} alerte(s) stock
            </a>
            @endif

            <a href="{{ route('historique.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nouveau Service
            </a>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-x-circle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>