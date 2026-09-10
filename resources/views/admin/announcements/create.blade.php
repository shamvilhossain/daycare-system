<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #8b5cf6 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner::after {
            content: ''; position: absolute; bottom: -30px; left: 50%;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .form-label { font-weight: 600; font-size: 0.88rem; color: #374151; }
        .required::after { content: ' *'; color: #ef4444; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-dropdown="toggle">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button type="submit" class="btn btn-default btn-flat float-end">Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-megaphone-fill me-2"></i>New Announcement</h2>
                            <p>Broadcast updates to parents, staff, or public welcome page</p>
                        </div>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-light fw-bold shadow-sm" style="position:relative;z-index:1;">
                            <i class="bi bi-arrow-left me-1"></i> Back to Announcements
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please correct the errors below:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <form action="{{ route('admin.announcements.store') }}" method="POST">
                                @csrf
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-white border-bottom py-3">
                                        <h5 class="card-title mb-0 fw-bold text-dark">Announcement Information</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        {{-- Title --}}
                                        <div class="mb-3">
                                            <label for="title" class="form-label required">Announcement Title</label>
                                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Summer Camp 2026 Registration Now Open!" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">This title will display on the welcome page scrolling ticker if the audience is Parents or All.</div>
                                        </div>

                                        {{-- Audience --}}
                                        <div class="mb-3">
                                            <label for="audience" class="form-label required">Target Audience</label>
                                            <select class="form-select form-select-lg @error('audience') is-invalid @enderror" id="audience" name="audience" required>
                                                <option value="" disabled {{ old('audience') ? '' : 'selected' }}>Select target audience</option>
                                                <option value="all" {{ old('audience') == 'all' ? 'selected' : '' }}>All (Public / Welcome Page & Everyone)</option>
                                                <option value="parents" {{ old('audience') == 'parents' ? 'selected' : '' }}>Parents (Welcome Page & Parents)</option>
                                                <option value="staff" {{ old('audience') == 'staff' ? 'selected' : '' }}>Staff Only (Internal notice)</option>
                                            </select>
                                            @error('audience')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Announcements targeted to <strong>All</strong> or <strong>Parents</strong> will be displayed on the public welcome page ticker.</div>
                                        </div>

                                        {{-- Content --}}
                                        <div class="mb-3">
                                            <label for="content" class="form-label required">Announcement Content</label>
                                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" placeholder="Write full details of the announcement here..." required>{{ old('content') }}</textarea>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Shown in the popup modal when someone clicks the title on the welcome page or views details.</div>
                                        </div>

                                        <div class="row g-3">
                                            {{-- Published At --}}
                                            <div class="col-md-6">
                                                <label for="published_at" class="form-label">Published At</label>
                                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                                                @error('published_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Defaults to current time. Can be set to a future date to schedule publication.</div>
                                            </div>

                                            {{-- Expires At --}}
                                            <div class="col-md-6">
                                                <label for="expires_at" class="form-label">Expires At</label>
                                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d\TH:i')) }}">
                                                @error('expires_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Date after which the announcement will stop showing on the welcome page ticker.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary fw-bold px-4" style="background-color: #6366f1; border-color: #6366f1;">
                                            <i class="bi bi-check-lg me-1"></i> Publish Announcement
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
