<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BrewTrack</title>
    {{-- PWA Manifest --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6F4E37">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="BrewTrack">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f0e8;
            display: flex;
            min-height: 100vh;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */
        .sidebar {
            width: 220px;
            background: #3d2b1f;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        /* Hidden on mobile by default */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }

        .sidebar-brand {
            padding: 18px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-brand i {
            color: white;
            font-size: 20px;
        }

        .sidebar-brand span {
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        < !-- Desktop notification bell (visible on desktop only) --><div style="display:flex; align-items:center; justify-content:flex-end;
 padding:8px 16px;
        border-bottom:1px solid rgba(255, 255, 255, 0.1);
        "
 class="desktop-notif-bar">
        @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.notifications.index') }}"
                    style="color:rgba(255,255,255,0.6); text-decoration:none;
             font-size:13px;
                    display:flex;
                    align-items:center;
                    gap:6px;
                    ">
             <i class="fas fa-bell"></i><span id="desktopBadge" style="display:none;
             background:#ef4444;
                    color:white;
                    font-size:10px;
                    font-weight:700;
                    padding:1px 6px;
                    border-radius:10px;
                    ">
         </span></a>@endif </div>

        /* Close button — mobile only */
        .sidebar-close {
            display: none;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 20px;
            cursor: pointer;
            margin-left: auto;
            padding: 4px;
        }

        @media (max-width: 768px) {
            .sidebar-close {
                display: block;
            }
        }

        .sidebar-user {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: #6F4E37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-name {
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .user-role {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
        }

        .nav-section {
            padding: 14px 16px 4px;
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.35);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
        }

        .nav-link i {
            width: 16px;
            text-align: center;
            font-size: 13px;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .nav-link.active {
            background: #6F4E37;
            color: white;
        }

        /* Notification badge on nav link */
        .nav-badge {
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: auto;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 12px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 9px 12px;
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.75);
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.25);
            color: white;
        }

        /* Dark overlay behind sidebar on mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.open {
            display: block;
        }

        /* =====================================================
           TOPBAR (mobile header)
        ===================================================== */
        .topbar {
            display: none;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        @media (max-width: 768px) {
            .topbar {
                display: flex;
            }
        }

        .topbar-menu-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #3d2b1f;
            cursor: pointer;
            padding: 4px;
        }

        .topbar-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            flex: 1;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #6F4E37;
            font-size: 16px;
        }

        /* =====================================================
           MAIN CONTENT
        ===================================================== */
        .main-content {
            margin-left: 220px;
            flex: 1;
            padding: 28px;
            min-width: 0;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 18px;
            }
        }

        .page-header p {
            color: #6b7280;
            font-size: 13px;
            margin-top: 2px;
        }

        /* =====================================================
           CARDS
        ===================================================== */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            padding: 20px;
            overflow: hidden;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .card-title i {
            color: #6F4E37;
        }

        /* =====================================================
           STAT CARDS — responsive grid
        ===================================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            flex-shrink: 0;
        }

        .icon-brown {
            background: #6F4E37;
        }

        .icon-green {
            background: #10b981;
        }

        .icon-yellow {
            background: #f59e0b;
        }

        .icon-gray {
            background: #6b7280;
        }

        .icon-red {
            background: #ef4444;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }

        @media (max-width: 480px) {
            .stat-value {
                font-size: 16px;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }
        }

        .stat-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        .stat-sub {
            font-size: 11px;
            margin-top: 3px;
            font-weight: 500;
        }

        .text-danger {
            color: #ef4444;
        }

        .text-success {
            color: #10b981;
        }

        /* =====================================================
           TWO COLUMN GRID — stacks on mobile
        ===================================================== */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        @media (max-width: 900px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        /* =====================================================
           TABLES — scrollable on mobile
        ===================================================== */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #f3f4f6;
            white-space: nowrap;
        }

        tbody td {
            padding: 11px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #fafafa;
        }

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

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-preparing {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-ready {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-served {
            background: #e5e7eb;
            color: #374151;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =====================================================
           BUTTONS — larger tap targets on mobile
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
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-primary {
            background: #6F4E37;
            color: white;
        }

        .btn-primary:hover {
            background: #5a3d2b;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .btn {
                padding: 10px 18px;
                font-size: 14px;
            }

            .btn-sm {
                padding: 7px 12px;
                font-size: 12px;
            }
        }

        /* =====================================================
           FORMS — full width on mobile
        ===================================================== */
        .form-group {
            margin-bottom: 14px;
        }

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
            -webkit-appearance: none;
        }

        .form-control:focus {
            outline: none;
            border-color: #6F4E37;
        }

        .form-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: 3px;
        }

        /* =====================================================
           TOOLBAR — wraps on mobile
        ===================================================== */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .toolbar-right {
            margin-left: auto;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        @media (max-width: 600px) {
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar-right {
                margin-left: 0;
            }

            .toolbar .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* =====================================================
           ALERTS
        ===================================================== */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
        }

        /* =====================================================
           NOTIFICATION BELL (topbar)
        ===================================================== */
        .notif-bell {
            position: relative;
            cursor: pointer;
            padding: 6px;
            color: #6b7280;
            font-size: 18px;
            background: none;
            border: none;
        }

        .notif-bell .dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            display: none;
        }

        .notif-bell .dot.show {
            display: block;
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Dark overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-coffee"></i>
            <span>BrewTrack</span>
            <button class="sidebar-close" onclick="closeSidebar()">
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

        <nav style="flex:1; padding:4px 0; overflow-y:auto;">
            @if(auth()->user()->isAdmin())
                <div class="nav-section">MAIN</div>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-receipt"></i> Orders
                </a>

                <div class="nav-section">MANAGEMENT</div>
                <a href="{{ route('admin.inventory.index') }}"
                    class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-boxes"></i> Inventory
                    <span class="nav-badge" id="sidebarBadge" style="display:none;"></span>
                </a>
                <a href="{{ route('admin.menu.index') }}"
                    class="nav-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-utensils"></i> Menu Items
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    onclick="closeSidebar()">
                    <i class="fas fa-tags"></i> Categories
                </a>

                <div class="nav-section">ADMINISTRATION</div>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="{{ route('admin.settings.index') }}"
                    class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-cog"></i> Settings
                </a>

                <div class="nav-section">ACCOUNT</div>
            @else
                <div class="nav-section">MAIN</div>
                <a href="{{ route('cashier.pos') }}"
                    class="nav-link {{ request()->routeIs('cashier.pos') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-cash-register"></i> Point of Sale
                </a>
                <a href="{{ route('cashier.orders.history') }}"
                    class="nav-link {{ request()->routeIs('cashier.orders.*') ? 'active' : '' }}" onclick="closeSidebar()">
                    <i class="fas fa-history"></i> Order History
                </a>
                <div class="nav-section">ACCOUNT</div>
            @endif
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

        <!-- Mobile topbar -->
        <div class="topbar">
            <button class="topbar-menu-btn" onclick="openSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-brand">
                <i class="fas fa-coffee"></i> BrewTrack
            </div>

            @if(auth()->user()->isAdmin())
                {{-- Notification Bell --}}
                <div style="margin-left:auto; position:relative;" id="notifWrap">
                    <button class="notif-bell" onclick="toggleNotifDropdown()" id="notifBell">
                        <i class="fas fa-bell"></i>
                        <span class="dot" id="notifDot"></span>
                    </button>

                    {{-- Dropdown --}}
                    <div id="notifDropdown" style="display:none; position:absolute;
                                    right:0; top:44px; width:300px;
                                    background:white; border-radius:12px;
                                    box-shadow:0 8px 30px rgba(0,0,0,0.15);
                                    z-index:9999; overflow:hidden;
                                    max-height:400px; overflow-y:auto;">

                        <div style="padding:14px 16px;
                                        border-bottom:1px solid #f3f4f6;
                                        display:flex; align-items:center;
                                        justify-content:space-between;">
                            <span style="font-weight:600; font-size:14px;">
                                Stock Alerts
                            </span>
                            <button onclick="markAllRead()" style="background:none; border:none;
                                               font-size:12px; color:#6F4E37;
                                               cursor:pointer; font-family:'Poppins',sans-serif;">
                                Mark all read
                            </button>
                        </div>

                        <div id="notifList">
                            <div style="padding:24px; text-align:center;
                                            color:#9ca3af; font-size:13px;">
                                <i class="fas fa-bell-slash" style="font-size:24px; display:block;
                                              margin-bottom:8px; opacity:0.4;"></i>
                                No new alerts
                            </div>
                        </div>

                        <a href="{{ route('admin.notifications.index') }}" style="display:block; padding:12px 16px;
                                      text-align:center; font-size:13px;
                                      color:#6F4E37; text-decoration:none;
                                      border-top:1px solid #f3f4f6;
                                      font-weight:500;">
                            View all notifications →
                        </a>
                    </div>
                </div>
            @endif
        </div>
        <!-- Flash messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close sidebar on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });
    </script>
    @if(auth()->user()->isAdmin())
        <script>
            // =====================================================
            // NOTIFICATION POLLING — checks every 10 seconds
            // =====================================================

            const POLL_INTERVAL = 10000; // 10 seconds
            const UNREAD_URL = '{{ route("admin.notifications.unread") }}';
            const MARK_ALL_URL = '{{ route("admin.notifications.read-all") }}';
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

            let lastCount = 0;

            // Start polling immediately then every 10 seconds
            checkNotifications();
            setInterval(checkNotifications, POLL_INTERVAL);

            async function checkNotifications() {
                try {
                    const response = await fetch(UNREAD_URL, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    updateBadges(data.count);
                    updateDropdownList(data.notifications);
                    lastCount = data.count;

                } catch (error) {
                    // Silent fail — don't break the page if network fails
                    console.log('Notification check failed:', error);
                }
            }

            function updateBadges(count) {
                // Mobile topbar dot
                const dot = document.getElementById('notifDot');
                if (dot) {
                    dot.classList.toggle('show', count > 0);
                }

                // Sidebar badge
                const sidebarBadge = document.getElementById('sidebarBadge');
                if (sidebarBadge) {
                    if (count > 0) {
                        sidebarBadge.style.display = 'inline-block';
                        sidebarBadge.textContent = count;
                    } else {
                        sidebarBadge.style.display = 'none';
                    }
                }

                // Desktop bell badge
                const desktopBadge = document.getElementById('desktopBadge');
                if (desktopBadge) {
                    if (count > 0) {
                        desktopBadge.style.display = 'inline-block';
                        desktopBadge.textContent = count;
                    } else {
                        desktopBadge.style.display = 'none';
                    }
                }
            }

            function updateDropdownList(notifications) {
                const list = document.getElementById('notifList');
                if (!list) return;

                if (notifications.length === 0) {
                    list.innerHTML = `
                    <div style="padding:24px; text-align:center;
                                color:#9ca3af; font-size:13px;">
                        <i class="fas fa-bell-slash"
                           style="font-size:24px; display:block;
                                  margin-bottom:8px; opacity:0.4;"></i>
                        No new alerts
                    </div>`;
                    return;
                }

                let html = '';
                notifications.forEach(function (n) {
                    var isCritical = n.type === 'critical_stock';
                    var bgColor = isCritical ? '#fff5f5' : '#fffbeb';
                    var badgeColor = isCritical
                        ? 'background:#fee2e2;color:#991b1b;'
                        : 'background:#fef3c7;color:#92400e;';
                    var icon = isCritical
                        ? 'fa-exclamation-triangle'
                        : 'fa-exclamation-circle';
                    var label = isCritical ? 'CRITICAL' : 'LOW STOCK';

                    html += `
                <div style="padding:12px 16px; border-bottom:1px solid #f3f4f6;
                            background:${bgColor};">
                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <i class="fas ${icon}"
                           style="color:${isCritical ? '#ef4444' : '#f59e0b'};
                                  margin-top:2px; font-size:14px;"></i>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:600;
                                        color:#1a1a2e;">
                                ${n.item}
                            </div>
                            <div style="font-size:11px; color:#6b7280;
                                        margin-top:2px;">
                                Stock: ${n.stock} ${n.unit}
                                &nbsp;·&nbsp; ${n.time}
                            </div>
                        </div>
                        <span style="font-size:10px; font-weight:700;
                                     padding:2px 6px; border-radius:4px;
                                     white-space:nowrap; ${badgeColor}">
                            ${label}
                        </span>
                    </div>
                </div>`;
                });

                list.innerHTML = html;
            }

            function toggleNotifDropdown() {
                var dropdown = document.getElementById('notifDropdown');
                if (!dropdown) return;

                if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                    dropdown.style.display = 'block';
                    // Refresh notifications when opening
                    checkNotifications();
                } else {
                    dropdown.style.display = 'none';
                }
            }

            async function markAllRead() {
                try {
                    await fetch(MARK_ALL_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json',
                        }
                    });
                    // Refresh immediately after marking read
                    checkNotifications();
                    document.getElementById('notifDropdown').style.display = 'none';
                } catch (e) {
                    console.log('Mark all read failed:', e);
                }
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                var wrap = document.getElementById('notifWrap');
                var dropdown = document.getElementById('notifDropdown');
                if (wrap && dropdown && !wrap.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        </script>
    @endif
    @stack('scripts')

    {{-- Register Service Worker for PWA --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) {
                    console.log('SW registered:', reg.scope);
                })
                .catch(function(err) {
                    console.log('SW registration failed:', err);
                });
        });
    }
</script>
</body>

</html>