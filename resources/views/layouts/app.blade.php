<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BrewTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6F4E37">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="BrewTrack">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <style>
        /* =====================================================
           RESET
        ===================================================== */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f0e8;
            color: #1a1a2e;
            display: flex;
            min-height: 100vh;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */
        .sidebar {
            width: 220px;
            min-width: 220px;
            background: #3d2b1f;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 200;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.28s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .sidebar-brand > i   { color: #fff; font-size: 18px; }
        .sidebar-brand > span{ color: #fff; font-weight: 700; font-size: 16px; }

        .sidebar-close-btn {
            display: none;
            margin-left: auto;
            background: none;
            border: none;
            color: rgba(255,255,255,0.5);
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
            padding: 2px 4px;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .user-avatar {
            width: 34px; height: 34px;
            background: #6F4E37;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 13px;
            flex-shrink: 0;
        }
        .user-name { color: #fff; font-size: 12px; font-weight: 600; line-height: 1.3; }
        .user-role { color: rgba(255,255,255,0.45); font-size: 11px; }

        .nav-section {
            padding: 14px 16px 3px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.3);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: rgba(255,255,255,0.62);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.18s, color 0.18s;
        }
        .nav-link i        { width: 16px; text-align: center; font-size: 13px; flex-shrink: 0; }
        .nav-link:hover    { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-link.active   { background: #6F4E37; color: #fff; }

        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.7);
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.22); color: #fff; }

        /* Dark overlay — mobile only */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.48);
            z-index: 199;
        }

        /* =====================================================
           TOPBAR (mobile)
        ===================================================== */
        .topbar {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            flex-shrink: 0;
        }
        .topbar-hamburger {
            background: none;
            border: none;
            font-size: 20px;
            color: #3d2b1f;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
        }
        .topbar-logo {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 700;
            font-size: 15px;
            color: #6F4E37;
        }

        /* Notification bell */
        .notif-wrap {
            margin-left: auto;
            position: relative;
        }
        .notif-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #6b7280;
            cursor: pointer;
            padding: 6px;
            position: relative;
            line-height: 1;
        }
        .notif-dot {
            position: absolute;
            top: 3px; right: 3px;
            width: 9px; height: 9px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
            display: none;
        }
        .notif-dot.show { display: block; }

        /* Notification dropdown */
        .notif-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 42px;
            width: 310px;
            max-width: calc(100vw - 24px);
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.14);
            z-index: 9999;
            overflow: hidden;
            max-height: 420px;
            display: none;
            flex-direction: column;
        }
        .notif-dropdown.open { display: flex; }
        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 16px;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
        }
        .notif-header span { font-weight: 600; font-size: 13px; }
        .notif-mark-all {
            background: none;
            border: none;
            font-size: 12px;
            color: #6F4E37;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        .notif-list { overflow-y: auto; flex: 1; }
        .notif-empty {
            padding: 28px 16px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }
        .notif-empty i { font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.35; }
        .notif-footer {
            display: block;
            padding: 11px 16px;
            text-align: center;
            font-size: 13px;
            color: #6F4E37;
            text-decoration: none;
            font-weight: 500;
            border-top: 1px solid #f3f4f6;
            flex-shrink: 0;
        }
        .notif-footer:hover { background: #fafafa; }

        /* =====================================================
           MAIN CONTENT
        ===================================================== */
        .main-content {
            margin-left: 220px;
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .content-body {
            flex: 1;
            padding: 26px 28px;
        }

        /* =====================================================
           PAGE HEADER
        ===================================================== */
        .page-header { margin-bottom: 20px; }
        .page-header h1 { font-size: 22px; font-weight: 700; }
        .page-header p  { font-size: 13px; color: #6b7280; margin-top: 2px; }

        /* =====================================================
           CARDS
        ===================================================== */
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.055);
        }
        .card-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .card-title i { color: #6F4E37; }

        /* =====================================================
           STAT CARDS
        ===================================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.055);
            display: flex;
            align-items: center;
            gap: 13px;
        }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; flex-shrink: 0;
        }
        .icon-brown  { background: #6F4E37; }
        .icon-green  { background: #10b981; }
        .icon-yellow { background: #f59e0b; }
        .icon-gray   { background: #6b7280; }
        .icon-red    { background: #ef4444; }
        .stat-value { font-size: 20px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .stat-sub   { font-size: 11px; font-weight: 500; margin-top: 3px; }
        .text-danger  { color: #ef4444; }
        .text-success { color: #10b981; }

        /* =====================================================
           TWO COLUMN GRID
        ===================================================== */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        /* =====================================================
           TABLES
        ===================================================== */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 2px solid #f3f4f6;
            white-space: nowrap;
        }
        tbody td { padding: 11px 12px; border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafafa; }

        /* =====================================================
           BADGES
        ===================================================== */
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-preparing { background: #dbeafe; color: #1e40af; }
        .badge-ready     { background: #d1fae5; color: #065f46; }
        .badge-served    { background: #e5e7eb; color: #374151; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        /* =====================================================
           BUTTONS
        ===================================================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            transition: background 0.18s, transform 0.18s;
            white-space: nowrap;
            line-height: 1.4;
        }
        .btn-primary        { background: #6F4E37; color: #fff; }
        .btn-primary:hover  { background: #5a3d2b; }
        .btn-secondary      { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover{ background: #d1d5db; }
        .btn-sm             { padding: 5px 10px; font-size: 12px; }
        .btn-edit           { background: #dbeafe; color: #1e40af; }
        .btn-delete         { background: #fee2e2; color: #991b1b; }

        /* =====================================================
           FORMS
        ===================================================== */
        .form-group { margin-bottom: 14px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 5px;
            color: #374151;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s;
            background: #fff;
            -webkit-appearance: none;
            appearance: none;
        }
        .form-control:focus { outline: none; border-color: #6F4E37; }
        .form-error { font-size: 12px; color: #ef4444; margin-top: 3px; }

        /* =====================================================
           ALERTS
        ===================================================== */
        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error   { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef3c7; color: #92400e; }

        /* =====================================================
           RESPONSIVE BREAKPOINTS
        ===================================================== */

        /* Tablet */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* Mobile */
        @media (max-width: 768px) {

            /* Show topbar, hide sidebar by default */
            .topbar   { display: flex; }
            .sidebar  { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close-btn { display: block; }

            /* Push content to top, no left margin */
            .main-content { margin-left: 0; }
            .content-body { padding: 14px 14px 24px; }

            /* Single column stats */
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }

            /* Single column grid */
            .grid-2 { grid-template-columns: 1fr; }

            /* Smaller stat cards */
            .stat-card  { padding: 12px 14px; gap: 10px; }
            .stat-icon  { width: 38px; height: 38px; font-size: 16px; border-radius: 8px; }
            .stat-value { font-size: 17px; }

            /* Page header */
            .page-header h1 { font-size: 18px; }

            /* Buttons full width in certain contexts */
            .page-header-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            /* Card padding */
            .card { padding: 14px; }
        }

        /* Small phones */
        @media (max-width: 380px) {
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-value { font-size: 15px; }
            .stat-label { font-size: 10px; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ===================== SIDEBAR OVERLAY ===================== --}}
<div id="sidebarOverlay" class="sidebar-overlay"
     onclick="closeSidebar()"></div>

{{-- ===================== SIDEBAR ===================== --}}
<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <i class="fas fa-coffee"></i>
        <span>BrewTrack</span>
        <button class="sidebar-close-btn" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
        </div>
        <div>
            <div class="user-name">{{ auth()->user()->full_name }}</div>
            <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
    </div>

    <nav style="flex:1; overflow-y:auto; padding:4px 0;">

        @if(auth()->user()->isAdmin())

            @php
                $lowStockCount = \App\Models\Inventory::where('is_active', true)
                    ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
                    ->count();
                $unreadNotifCount = \App\Models\StockNotification::where('is_read', false)->count();
            @endphp

            <div class="nav-section">MAIN</div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-receipt"></i>
                Orders
            </a>

            <div class="nav-section">MANAGEMENT</div>

            <a href="{{ route('admin.inventory.index') }}"
               class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-boxes"></i>
                Inventory
                @if($lowStockCount > 0)
                    <span class="nav-badge">{{ $lowStockCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.menu.index') }}"
               class="nav-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-utensils"></i>
                Menu Items
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-tags"></i>
                Categories
            </a>

            <div class="nav-section">ADMINISTRATION</div>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-users"></i>
                Users
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>

            <a href="{{ route('admin.notifications.index') }}"
               class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-bell"></i>
                Notifications
                @if($unreadNotifCount > 0)
                    <span class="nav-badge" id="sidebarNotifBadge">
                        {{ $unreadNotifCount }}
                    </span>
                @else
                    <span class="nav-badge" id="sidebarNotifBadge"
                          style="display:none;"></span>
                @endif
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-cog"></i>
                Settings
            </a>

            <div class="nav-section">ACCOUNT</div>

        @else

            <div class="nav-section">MAIN</div>

            <a href="{{ route('cashier.pos') }}"
               class="nav-link {{ request()->routeIs('cashier.pos') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-cash-register"></i>
                Point of Sale
            </a>

            <a href="{{ route('cashier.orders.history') }}"
               class="nav-link {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <i class="fas fa-history"></i>
                Order History
            </a>

            <div class="nav-section">ACCOUNT</div>

        @endif
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>

</aside>

{{-- ===================== MAIN CONTENT ===================== --}}
<div class="main-content">

    {{-- TOPBAR (mobile only) --}}
    <div class="topbar">
        <button class="topbar-hamburger" onclick="openSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-logo">
            <i class="fas fa-coffee"></i>
            BrewTrack
        </div>

        @if(auth()->user()->isAdmin())
        <div class="notif-wrap" id="notifWrap">
            <button class="notif-btn" id="notifBtn"
                    onclick="toggleDropdown()">
                <i class="fas fa-bell"></i>
                <span class="notif-dot" id="notifDot"></span>
            </button>

            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span>Stock Alerts</span>
                    <button class="notif-mark-all"
                            onclick="markAllRead()">
                        Mark all read
                    </button>
                </div>
                <div class="notif-list" id="notifList">
                    <div class="notif-empty">
                        <i class="fas fa-bell-slash"></i>
                        No new alerts
                    </div>
                </div>
                <a href="{{ route('admin.notifications.index') }}"
                   class="notif-footer"
                   onclick="closeDropdown()">
                    View all notifications →
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- PAGE CONTENT --}}
    <div class="content-body">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle" style="flex-shrink:0;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle" style="flex-shrink:0;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')

    </div>
</div>

{{-- ===================== SCRIPTS ===================== --}}
<script>
    /* ---------- Sidebar ---------- */
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeSidebar(); closeDropdown(); }
    });

    /* ---------- Notification dropdown ---------- */
    function toggleDropdown() {
        var d = document.getElementById('notifDropdown');
        if (d.classList.contains('open')) {
            closeDropdown();
        } else {
            d.classList.add('open');
            loadNotifications();
        }
    }

    function closeDropdown() {
        var d = document.getElementById('notifDropdown');
        if (d) d.classList.remove('open');
    }

    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('notifWrap');
        if (wrap && !wrap.contains(e.target)) closeDropdown();
    });
</script>

@if(auth()->user()->isAdmin())
<script>
    /* ---------- Notification polling — every 10 seconds ---------- */
    var CSRF     = document.querySelector('meta[name="csrf-token"]').content;
    var UNREAD   = '{{ route("admin.notifications.unread") }}';
    var READ_ALL = '{{ route("admin.notifications.read-all") }}';
    var lastCount = 0;

    poll();
    setInterval(poll, 10000);

    async function poll() {
        try {
            var res  = await fetch(UNREAD, { headers:{ Accept:'application/json' } });
            if (!res.ok) return;
            var data = await res.json();
            updateBadges(data.count);
            window._notifData = data.notifications;
        } catch(e) {}
    }

    function updateBadges(count) {
        /* topbar dot */
        var dot = document.getElementById('notifDot');
        if (dot) dot.classList.toggle('show', count > 0);

        /* sidebar badge */
        var sb = document.getElementById('sidebarNotifBadge');
        if (sb) {
            sb.textContent    = count;
            sb.style.display  = count > 0 ? 'inline-block' : 'none';
        }
    }

    function loadNotifications() {
        var list = document.getElementById('notifList');
        if (!list) return;
        var items = window._notifData || [];

        if (!items.length) {
            list.innerHTML = '<div class="notif-empty">'
                + '<i class="fas fa-bell-slash"></i>No new alerts</div>';
            return;
        }

        var html = '';
        items.forEach(function(n) {
            var crit = n.type === 'critical_stock';
            html += '<div style="padding:11px 16px;'
                  + 'border-bottom:1px solid #f3f4f6;'
                  + 'background:' + (crit ? '#fff5f5' : '#fffbeb') + ';">'
                  + '<div style="display:flex;align-items:flex-start;gap:9px;">'
                  + '<i class="fas ' + (crit ? 'fa-exclamation-triangle' : 'fa-exclamation-circle')
                  + '" style="color:' + (crit ? '#ef4444' : '#f59e0b')
                  + ';font-size:13px;margin-top:2px;flex-shrink:0;"></i>'
                  + '<div style="flex:1;min-width:0;">'
                  + '<div style="font-size:13px;font-weight:600;color:#1a1a2e;">'
                  + n.item + '</div>'
                  + '<div style="font-size:11px;color:#6b7280;margin-top:2px;">'
                  + 'Stock: ' + n.stock + ' ' + n.unit
                  + ' &nbsp;·&nbsp; ' + n.time + '</div>'
                  + '</div>'
                  + '<span style="font-size:10px;font-weight:700;padding:2px 6px;'
                  + 'border-radius:4px;white-space:nowrap;flex-shrink:0;'
                  + (crit ? 'background:#fee2e2;color:#991b1b;'
                          : 'background:#fef3c7;color:#92400e;') + '">'
                  + (crit ? 'CRITICAL' : 'LOW') + '</span>'
                  + '</div></div>';
        });

        list.innerHTML = html;
    }

    async function markAllRead() {
        try {
            await fetch(READ_ALL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' }
            });
            window._notifData = [];
            updateBadges(0);
            loadNotifications();
            closeDropdown();
        } catch(e) {}
    }
</script>
@endif

{{-- Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    }
</script>

@stack('scripts')
</body>
</html>