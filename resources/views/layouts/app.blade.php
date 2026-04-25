<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BrewTrack</title>

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =====================================================
           CSS VARIABLES — change these to restyle everything
        ===================================================== */
        :root {
            --brown-dark:   #5a3d2b;  /* Sidebar background */
            --brown-mid:    #6F4E37;  /* Buttons, active states */
            --brown-light:  #9b7559;  /* Hover states */
            --cream:        #f5f0e8;  /* Page background */
            --white:        #ffffff;
            --text-dark:    #1a1a2e;
            --text-gray:    #6b7280;
            --border:       #e5e7eb;
            --success:      #10b981;
            --warning:      #f59e0b;
            --danger:       #ef4444;
            --sidebar-width: 200px;
        }

        /* =====================================================
           RESET & BASE
        ===================================================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* =====================================================
           SIDEBAR NAVIGATION
        ===================================================== */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--brown-dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        /* Logo at top of sidebar */
        .sidebar-brand {
            padding: 20px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand i { color: white; font-size: 20px; }
        .sidebar-brand span { color: white; font-weight: 700; font-size: 16px; }

        /* User info box */
        .sidebar-user {
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: var(--brown-mid);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; font-size: 14px;
            flex-shrink: 0;
        }
        .user-name { color: white; font-size: 13px; font-weight: 600; }
        .user-role { color: rgba(255,255,255,0.6); font-size: 11px; }

        /* Section label (e.g. "MAIN", "MANAGEMENT") */
        .nav-section {
            padding: 12px 16px 4px;
            font-size: 10px;
            font-weight: 600;
            color: rgba(255,255,255,0.4);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Individual nav link */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            border-radius: 0;
        }
        .nav-link i { width: 16px; text-align: center; font-size: 13px; }
        .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: var(--brown-mid); color: white; }

        /* Logout button at bottom */
        .sidebar-footer {
            margin-top: auto;
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 10px 14px;
            background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);
            border: none; border-radius: 8px;
            font-size: 13px; font-family: 'Poppins', sans-serif;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.3); color: white; }

        /* =====================================================
           MAIN CONTENT AREA
        ===================================================== */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 28px;
            min-height: 100vh;
        }

        /* Page header */
        .page-header {
            margin-bottom: 24px;
        }
        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }
        .page-header p { color: var(--text-gray); font-size: 14px; }

        /* =====================================================
           CARDS
        ===================================================== */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 24px;
        }
        .card-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-title i { color: var(--brown-mid); }

        /* =====================================================
           STAT CARDS (the 4 boxes at top of dashboard)
        ===================================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: white; flex-shrink: 0;
        }
        .stat-icon.brown  { background: var(--brown-mid); }
        .stat-icon.green  { background: var(--success); }
        .stat-icon.yellow { background: var(--warning); }
        .stat-icon.gray   { background: #6b7280; }
        .stat-value { font-size: 22px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-gray); margin-top: 3px; }
        .stat-sub   { font-size: 12px; margin-top: 4px; }
        .stat-sub.danger  { color: var(--danger); font-weight: 500; }
        .stat-sub.success { color: var(--success); font-weight: 500; }

        /* =====================================================
           TABLES
        ===================================================== */
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
        }
        tbody td { padding: 12px 16px; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafafa; }

        /* =====================================================
           BADGES (status labels)
        ===================================================== */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-preparing { background: #dbeafe; color: #1e40af; }
        .badge-ready     { background: #d1fae5; color: #065f46; }
        .badge-served    { background: #e5e7eb; color: #374151; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-admin     { background: #ede9fe; color: #5b21b6; }
        .badge-cashier   { background: #e0f2fe; color: #075985; }
        .badge-ok        { background: #d1fae5; color: #065f46; }
        .badge-low       { background: #fef3c7; color: #92400e; }
        .badge-critical  { background: #fee2e2; color: #991b1b; }
        .badge-available { background: #d1fae5; color: #065f46; }

        /* =====================================================
           BUTTONS
        ===================================================== */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px;
            border-radius: 8px; border: none;
            font-size: 13px; font-weight: 500;
            cursor: pointer; text-decoration: none;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--brown-mid); color: white; }
        .btn-primary:hover { background: var(--brown-dark); }
        .btn-secondary { background: var(--border); color: var(--text-dark); }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .btn-edit   { background: #dbeafe; color: #1e40af; }
        .btn-delete { background: #fee2e2; color: #991b1b; }

        /* =====================================================
           FORM ELEMENTS
        ===================================================== */
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: 13px;
            font-weight: 500; margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 2px solid var(--border); border-radius: 8px;
            font-size: 14px; font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s;
        }
        .form-control:focus { outline: none; border-color: var(--brown-mid); }
        .form-error { font-size: 12px; color: var(--danger); margin-top: 4px; }

        /* =====================================================
           ALERTS (flash messages)
        ===================================================== */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>

    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <!-- Logo -->
        <div class="sidebar-brand">
            <i class="fas fa-coffee"></i>
            <span>BrewTrack</span>
        </div>

        <!-- Logged-in user info -->
        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->full_name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>

        <!-- Navigation links -->
        <nav style="flex: 1; padding: 8px 0;">

            <div class="nav-section">MAIN</div>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i> Orders
                </a>

                <div class="nav-section">MANAGEMENT</div>

                <a href="{{ route('admin.inventory.index') }}"
                   class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
                <a href="{{ route('admin.menu.index') }}"
                   class="nav-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                    <i class="fas fa-utensils"></i> Menu Items
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Categories
                </a>

                <div class="nav-section">ADMINISTRATION</div>

                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="{{ route('admin.settings.index') }}"
                   class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            @else
                <a href="{{ route('cashier.pos') }}"
                   class="nav-link {{ request()->routeIs('cashier.pos') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i> Point of Sale
                </a>
                <a href="{{ route('cashier.orders.history') }}"
                   class="nav-link {{ request()->routeIs('cashier.orders.history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Order History
                </a>
            @endif

            <div class="nav-section">ACCOUNT</div>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- Flash messages (shown after redirects) -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Page-specific content goes here -->
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>