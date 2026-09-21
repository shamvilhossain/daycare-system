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

                {{-- DAILY OPERATIONS --}}
                @php
                    $canAttendance = auth()->user()->can('attendance.view-any') || auth()->user()->can('attendance.view');
                    $canDailyLogs = auth()->user()->can('child-daily-logs.view-any') || auth()->user()->can('child-daily-logs.view');
                    $canActivitySchedule = auth()->user()->can('activity-occurrences.view-any');
                @endphp
                @if ($canAttendance || $canDailyLogs || $canActivitySchedule)
                    <li class="nav-header">DAILY OPERATIONS</li>
                    @if ($canAttendance)
                    <li class="nav-item">
                        <a href="{{ route('admin.attendance.index') }}" class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-check2-circle"></i>
                            <p>Attendance Desk</p>
                        </a>
                    </li>
                    @endif
                    @if ($canDailyLogs)
                    <li class="nav-item">
                        <a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link {{ request()->routeIs('admin.child-daily-logs.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-journal-text"></i>
                            <p>Daily Child Logs</p>
                        </a>
                    </li>
                    @endif
                    @if ($canActivitySchedule)
                    <li class="nav-item">
                        <a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link {{ request()->routeIs('admin.activity-occurrences.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-calendar-check"></i>
                            <p>Daily Schedule & Log</p>
                        </a>
                    </li>
                    @endif
                @endif

                {{-- MANAGEMENT --}}
                @php
                    $canChildren = auth()->user()->can('children.view-any') || auth()->user()->can('children.view');
                    $canStaff = auth()->user()->can('staff.view-any');
                    $canActivities = auth()->user()->can('activities.view-any');
                    $canEnrollments = auth()->user()->can('enrollments.view-any') || auth()->user()->can('enrollments.view');
                    $canInvoices = auth()->user()->can('invoices.view-any') || auth()->user()->can('invoices.view') || auth()->user()->can('payments.view-any');
                    $canAnnouncements = auth()->user()->can('announcements.view-any');
                    $canTherapy = auth()->user()->can('therapy-sessions.view-any') || auth()->user()->can('therapy-sessions.view');
                @endphp
                @if ($canChildren || $canStaff || $canActivities || $canEnrollments || $canInvoices || $canAnnouncements || $canTherapy)
                    <li class="nav-header">MANAGEMENT</li>
                    @if ($canChildren)
                    <li class="nav-item">
                        <a href="{{ route('admin.children.index') }}" class="nav-link {{ request()->routeIs('admin.children.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>Children</p>
                        </a>
                    </li>
                    @endif
                    @if ($canStaff)
                    <li class="nav-item">
                        <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-badge-fill"></i>
                            <p>Staff</p>
                        </a>
                    </li>
                    @endif
                    @if ($canActivities)
                    <li class="nav-item">
                        <a href="{{ route('admin.activities.index') }}" class="nav-link {{ request()->routeIs('admin.activities.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-palette-fill"></i>
                            <p>Activity Catalog</p>
                        </a>
                    </li>
                    @endif
                    @if ($canEnrollments)
                    <li class="nav-item">
                        <a href="{{ route('admin.enrollments.index') }}" class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-clipboard-check-fill"></i>
                            <p>Enrollments</p>
                        </a>
                    </li>
                    @endif
                    @if ($canInvoices)
                    <li class="nav-item">
                        <a href="{{ route('admin.invoices.index') }}" class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-receipt-cutoff"></i>
                            <p>Invoices & Payments</p>
                        </a>
                    </li>
                    @endif
                    @if ($canAnnouncements)
                    <li class="nav-item">
                        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-megaphone-fill"></i>
                            <p>Announcements</p>
                        </a>
                    </li>
                    @endif
                    @if ($canTherapy)
                    <li class="nav-item">
                        <a href="{{ route('admin.therapy-sessions.index') }}" class="nav-link {{ request()->routeIs('admin.therapy-sessions.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-heart-pulse-fill"></i>
                            <p>Therapy Sessions</p>
                        </a>
                    </li>
                    @endif
                @endif

                {{-- ADMIN --}}
                @php
                    $canPrograms = auth()->user()->can('programs.view-any') || auth()->user()->can('programs.view');
                    $isAdmin = auth()->user()->hasRole('admin');
                @endphp
                @if ($isAdmin || $canPrograms)
                    <li class="nav-header">ADMIN</li>
                    @if ($canPrograms)
                    <li class="nav-item">
                        <a href="{{ route('admin.programs.index') }}" class="nav-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-book-half"></i>
                            <p>Programs</p>
                        </a>
                    </li>
                    @endif
                    @if ($isAdmin)
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
                    @endif
                @endif

                {{-- REPORTS --}}
                @php
                    $canReportBilling = auth()->user()->can('reports.billing');
                    $canReportCalendar = auth()->user()->can('reports.view');
                @endphp
                @if ($canReportBilling || $canReportCalendar)
                    <li class="nav-header">REPORTS</li>
                    <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-file-earmark-bar-graph-fill"></i>
                            <p>
                                Reports
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if ($canReportBilling)
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.billing-revenue') }}" class="nav-link {{ request()->routeIs('admin.reports.billing-revenue') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-receipt"></i>
                                    <p>Billing / Revenue</p>
                                </a>
                            </li>
                            @endif
                            @if ($canReportCalendar)
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.activity-calendar') }}" class="nav-link {{ request()->routeIs('admin.reports.activity-calendar') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-calendar3"></i>
                                    <p>Activity Calendar</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- SETTINGS --}}
                @can('settings.manage')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-gear-fill"></i>
                        <p>Settings</p>
                    </a>
                </li>
                @endcan
            </ul>
        </nav>
    </div>
</aside>
