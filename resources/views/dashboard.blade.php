<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daycare System Dashboard — manage children, staff, and daily activities at a glance.">
    <title>Dashboard | Daycare System</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- AdminLTE 4 via Vite --}}
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ===== Welcome Banner ===== */
        .welcome-banner {
            background: linear-gradient(135deg, #f97066 0%, #c061cb 50%, #7c3aed 100%);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -40px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: 80px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .welcome-banner h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        .welcome-banner p {
            font-size: 0.95rem;
            opacity: 0.9;
            max-width: 520px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        .welcome-date {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 0.4rem 0.85rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 1rem;
            position: relative;
            z-index: 1;
        }

        /* ===== Stat Cards ===== */
        .stat-card {
            border: none;
            border-radius: 14px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .stat-card.border-purple::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
        .stat-card.border-pink::after { background: linear-gradient(90deg, #ec4899, #f472b6); }
        .stat-card.border-emerald::after { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card.border-amber::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .stat-icon.bg-purple { background: #f3f0ff; color: #7c3aed; }
        .stat-icon.bg-pink { background: #fdf2f8; color: #ec4899; }
        .stat-icon.bg-emerald { background: #ecfdf5; color: #10b981; }
        .stat-icon.bg-amber { background: #fffbeb; color: #f59e0b; }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
            color: #1e1b4b;
        }
        .stat-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.35rem;
        }
        .stat-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }

        /* ===== Activity list ===== */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 0.85rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }
        .activity-dot.dot-purple { background: #7c3aed; }
        .activity-dot.dot-emerald { background: #10b981; }
        .activity-dot.dot-amber { background: #f59e0b; }

        /* ===== Quick actions ===== */
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #1e1b4b;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }
        .quick-action-btn:hover {
            background: #f8f7ff;
            border-color: #7c3aed;
            transform: translateX(4px);
            color: #1e1b4b;
        }
        .quick-action-btn .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .action-icon.bg-purple { background: #f3f0ff; color: #7c3aed; }
        .action-icon.bg-pink { background: #fdf2f8; color: #ec4899; }
        .action-icon.bg-emerald { background: #ecfdf5; color: #10b981; }
        .action-icon.bg-amber { background: #fffbeb; color: #f59e0b; }
        .action-icon.bg-blue { background: #eff6ff; color: #3b82f6; }

        .quick-action-btn .bi-chevron-right {
            margin-left: auto;
            color: #9ca3af;
            font-size: 0.75rem;
        }

        /* ===== Card polish ===== */
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #f3f4f6;
        }
        .card-title i { color: #7c3aed; }

        /* ===== Sidebar tweaks ===== */
        .app-sidebar {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-brand .brand-text {
            font-weight: 700;
        }

        /* ===== Animations ===== */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in {
            animation: fadeSlideUp 0.5s ease-out backwards;
        }
        .animate-delay-1 { animation-delay: 0.05s; }
        .animate-delay-2 { animation-delay: 0.1s; }
        .animate-delay-3 { animation-delay: 0.15s; }
        .animate-delay-4 { animation-delay: 0.2s; }
        .animate-delay-5 { animation-delay: 0.25s; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <span class="nav-link text-muted" style="font-size:0.9rem;">
                            Good <span id="timeGreeting">day</span>, <strong>{{ explode('@', Auth::user()->email)[0] }}</strong>
                        </span>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" title="Notifications">
                            <i class="bi bi-bell"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-muted" style="font-size:0.85rem;">{{ Auth::user()->email }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <i class="bi bi-house-heart-fill brand-image" style="font-size:1.4rem;"></i>
                    <span class="brand-text fw-light"><b>Daycare</b>System</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link active">
                                <i class="nav-icon bi bi-grid-1x2-fill"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Children</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-person-badge-fill"></i>
                                <p>Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-calendar-event-fill"></i>
                                <p>Activities</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-check-fill"></i>
                                <p>Enrollments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-credit-card-2-front-fill"></i>
                                <p>Payments</p>
                            </a>
                        </li>

                        @role('admin')
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.role-permissions.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-shield-lock-fill"></i>
                                <p>Role Permissions</p>
                            </a>
                        </li>
                        @endrole

                        <li class="nav-header">REPORTS</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-bar-chart-line-fill"></i>
                                <p>Analytics</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    {{-- Welcome Banner --}}
                    <div class="welcome-banner animate-in">
                        <h2>Welcome back, {{ explode('@', Auth::user()->email)[0] }}! 👋</h2>
                        <p>Here's what's happening at your daycare today. Stay on top of enrollments, activities, and daily operations.</p>
                        <div class="welcome-date">
                            <i class="bi bi-calendar3"></i>
                            <span id="currentDate"></span>
                        </div>
                    </div>

                    {{-- Stat Cards --}}
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                            <div class="card stat-card border-purple animate-in animate-delay-1">
                                <div class="card-body d-flex align-items-start gap-3">
                                    <div class="stat-icon bg-purple">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">Total Children</div>
                                        <div class="stat-value">{{ $totalChildren ?? 0 }}</div>
                                        <span class="stat-badge bg-success-subtle text-success mt-1 d-inline-block">
                                            <i class="bi bi-arrow-up-short"></i> Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                            <div class="card stat-card border-pink animate-in animate-delay-2">
                                <div class="card-body d-flex align-items-start gap-3">
                                    <div class="stat-icon bg-pink">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">Staff Members</div>
                                        <div class="stat-value">{{ $totalStaff ?? 0 }}</div>
                                        <span class="stat-badge bg-success-subtle text-success mt-1 d-inline-block">
                                            <i class="bi bi-arrow-up-short"></i> On duty
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                            <div class="card stat-card border-emerald animate-in animate-delay-3">
                                <div class="card-body d-flex align-items-start gap-3">
                                    <div class="stat-icon bg-emerald">
                                        <i class="bi bi-clipboard-check-fill"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">Enrollments</div>
                                        <div class="stat-value">{{ $totalEnrollments ?? 0 }}</div>
                                        <span class="stat-badge bg-success-subtle text-success mt-1 d-inline-block">
                                            <i class="bi bi-arrow-up-short"></i> Current
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                            <div class="card stat-card border-amber animate-in animate-delay-4">
                                <div class="card-body d-flex align-items-start gap-3">
                                    <div class="stat-icon bg-amber">
                                        <i class="bi bi-calendar-event-fill"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">Activities</div>
                                        <div class="stat-value">{{ $totalActivities ?? 0 }}</div>
                                        <span class="stat-badge bg-success-subtle text-success mt-1 d-inline-block">
                                            <i class="bi bi-arrow-up-short"></i> Scheduled
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Content Grid --}}
                    <div class="row animate-in animate-delay-5">
                        {{-- Recent Activity --}}
                        <div class="col-lg-8 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="bi bi-activity me-2"></i>Recent Activity</h3>
                                    <div class="card-tools">
                                        <span class="text-muted" style="font-size:0.8rem;">Today</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="activity-item">
                                        <span class="activity-dot dot-purple"></span>
                                        <div>
                                            <div style="font-size:0.875rem; color:#1e1b4b;">You logged in to the dashboard</div>
                                            <div style="font-size:0.75rem; color:#9ca3af; margin-top:2px;">Just now</div>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <span class="activity-dot dot-emerald"></span>
                                        <div>
                                            <div style="font-size:0.875rem; color:#1e1b4b;">System is running smoothly</div>
                                            <div style="font-size:0.75rem; color:#9ca3af; margin-top:2px;">All services operational</div>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <span class="activity-dot dot-amber"></span>
                                        <div>
                                            <div style="font-size:0.875rem; color:#1e1b4b;">Welcome to Daycare System</div>
                                            <div style="font-size:0.75rem; color:#9ca3af; margin-top:2px;">Your account role: {{ Auth::user()->role ?? 'User' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="col-lg-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h3>
                                </div>
                                <div class="card-body">
                                    <a href="#" class="quick-action-btn">
                                        <span class="action-icon bg-purple"><i class="bi bi-person-plus-fill"></i></span>
                                        <span>Add Child</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                    <a href="#" class="quick-action-btn">
                                        <span class="action-icon bg-pink"><i class="bi bi-person-badge"></i></span>
                                        <span>Add Staff</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                    <a href="#" class="quick-action-btn">
                                        <span class="action-icon bg-emerald"><i class="bi bi-journal-plus"></i></span>
                                        <span>New Enrollment</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                    <a href="#" class="quick-action-btn">
                                        <span class="action-icon bg-amber"><i class="bi bi-calendar-plus"></i></span>
                                        <span>Schedule Activity</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                    <a href="#" class="quick-action-btn">
                                        <span class="action-icon bg-blue"><i class="bi bi-cash-stack"></i></span>
                                        <span>Record Payment</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">AdminLTE 4</div>
            <strong>&copy; {{ date('Y') }} Daycare System.</strong> All rights reserved.
        </footer>
    </div>

    <script>
        // Time-based greeting
        (function() {
            const hour = new Date().getHours();
            let greeting = 'day';
            if (hour < 12) greeting = 'morning';
            else if (hour < 17) greeting = 'afternoon';
            else greeting = 'evening';
            const el = document.getElementById('timeGreeting');
            if (el) el.textContent = greeting;

            const dateEl = document.getElementById('currentDate');
            if (dateEl) {
                dateEl.textContent = new Date().toLocaleDateString('en-US', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                });
            }
        })();
    </script>
</body>
</html>
