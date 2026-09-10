{{-- Sidebar --}}
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <i class="bi bi-house-heart-fill brand-image" style="font-size:1.4rem;"></i>
            <span class="brand-text fw-light"><b>Kinder</b>Care</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                <li class="nav-header">MAIN</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-grid-1x2-fill"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">DAILY OPERATIONS</li>
                <li class="nav-item">
                    <a href="{{ route('admin.attendance.index') }}" class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-check2-circle"></i>
                        <p>Attendance Desk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link {{ request()->routeIs('admin.child-daily-logs.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-journal-text"></i>
                        <p>Daily Child Logs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link {{ request()->routeIs('admin.activity-occurrences.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>Daily Schedule & Log</p>
                    </a>
                </li>

                <li class="nav-header">MANAGEMENT</li>
                <li class="nav-item">
                    <a href="{{ route('admin.children.index') }}" class="nav-link {{ request()->routeIs('admin.children.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Children</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge-fill"></i>
                        <p>Staff</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.activities.index') }}" class="nav-link {{ request()->routeIs('admin.activities.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-palette-fill"></i>
                        <p>Activity Catalog</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.enrollments.index') }}" class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clipboard-check-fill"></i>
                        <p>Enrollments</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.invoices.index') }}" class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-receipt-cutoff"></i>
                        <p>Invoices & Payments</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-megaphone-fill"></i>
                        <p>Announcements</p>
                    </a>
                </li>

                @role('admin')
                <li class="nav-header">ADMIN</li>
                <li class="nav-item">
                    <a href="{{ route('admin.programs.index') }}" class="nav-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-book-half"></i>
                        <p>Programs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-lines-fill"></i>
                        <p>Users & Accounts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.role-permissions.index') }}" class="nav-link {{ request()->routeIs('admin.role-permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-shield-lock-fill"></i>
                        <p>Role Permissions</p>
                    </a>
                </li>

                <li class="nav-header">REPORTS</li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports.activity-calendar') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar3"></i>
                        <p>Activity Calendar</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-bar-chart-line-fill"></i>
                        <p>Analytics</p>
                    </a>
                </li>
                @endrole
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
