<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to your Daycare System account to manage children, staff, and daily activities.">
    <title>Sign In | Daycare System</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gradient-start: #f97066;
            --gradient-mid: #c061cb;
            --gradient-end: #7c3aed;
            --primary: #7c3aed;
            --primary-hover: #6d28d9;
            --surface: #ffffff;
            --surface-hover: #f8f7ff;
            --text-primary: #1e1b4b;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --border-focus: #c4b5fd;
            --error: #ef4444;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 25px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 50px -12px rgba(0,0,0,0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: #f0f0f5;
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        /* ===== LAYOUT ===== */
        .auth-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ===== LEFT PANEL ===== */
        .auth-brand-panel {
            flex: 0 0 50%;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-mid) 50%, var(--gradient-end) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .auth-brand-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            animation: floatBubble 8s ease-in-out infinite;
        }

        .auth-brand-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            animation: floatBubble 10s ease-in-out infinite reverse;
        }

        @keyframes floatBubble {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 420px;
        }

        .brand-illustration {
            width: 280px;
            height: 280px;
            margin: 0 auto 2.5rem;
            animation: gentleFloat 6s ease-in-out infinite;
        }

        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .brand-illustration svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.15));
        }

        .brand-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .brand-content p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.6;
            font-weight: 400;
        }

        .brand-dots {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 2rem;
        }

        .brand-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
        }

        .brand-dots span.active {
            width: 28px;
            border-radius: 4px;
            background: rgba(255,255,255,0.9);
        }

        /* ===== RIGHT PANEL (FORM) ===== */
        .auth-form-panel {
            flex: 0 0 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: var(--surface);
        }

        .auth-form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeSlideUp 0.6s ease-out;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2.5rem;
        }

        .auth-logo-icon {
            width: 42px;
            height: 42px;
            border-radius: var(--radius);
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
        }

        .auth-logo span {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .auth-logo span em {
            font-style: normal;
            font-weight: 400;
            color: var(--text-secondary);
        }

        .auth-heading {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .auth-subheading {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        /* ===== ALERT ===== */
        .auth-alert {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: var(--radius);
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            animation: shakeIn 0.4s ease;
        }

        @keyframes shakeIn {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        .auth-alert ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .auth-alert li {
            font-size: 0.85rem;
            color: var(--error);
            padding: 2px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .auth-alert li::before {
            content: '\F33A';
            font-family: 'bootstrap-icons';
            font-size: 0.8rem;
        }

        /* ===== FORM FIELDS ===== */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: var(--transition);
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: var(--surface);
            transition: var(--transition);
            outline: none;
        }

        .input-wrapper input::placeholder {
            color: var(--text-muted);
        }

        .input-wrapper input:hover {
            border-color: #d1d5db;
            background: var(--surface-hover);
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
            background: var(--surface);
        }

        .input-wrapper input:focus ~ .input-icon {
            color: var(--primary);
        }

        .input-wrapper input.is-invalid {
            border-color: var(--error);
        }

        .input-wrapper input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 4px;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        .input-wrapper input[type="password"] ~ .password-toggle .bi-eye { display: none; }
        .input-wrapper input[type="password"] ~ .password-toggle .bi-eye-slash { display: inline; }
        .input-wrapper input[type="text"] ~ .password-toggle .bi-eye { display: inline; }
        .input-wrapper input[type="text"] ~ .password-toggle .bi-eye-slash { display: none; }

        /* ===== REMEMBER ME ROW ===== */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .custom-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .custom-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
            border-radius: 4px;
        }

        .custom-check span {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* ===== SUBMIT BUTTON ===== */
        .btn-auth {
            width: 100%;
            padding: 0.85rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-mid), var(--gradient-end));
            background-size: 200% 200%;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--gradient-end), var(--gradient-mid), var(--gradient-start));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .btn-auth:hover::before {
            opacity: 1;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.35);
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .btn-auth span, .btn-auth i {
            position: relative;
            z-index: 1;
        }

        /* ===== FOOTER LINK ===== */
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .auth-footer a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* ===== DIVIDER ===== */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .auth-brand-panel {
                display: none;
            }
            .auth-form-panel {
                flex: 1;
            }
        }

        @media (max-width: 480px) {
            .auth-form-panel {
                padding: 2rem 1.5rem;
            }
            .auth-heading {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">

        {{-- Left Brand Panel --}}
        <div class="auth-brand-panel">
            <div class="brand-content">
                <div class="brand-illustration">
                    <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Background circle --}}
                        <circle cx="200" cy="200" r="170" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="200" cy="200" r="130" fill="rgba(255,255,255,0.08)"/>

                        {{-- Sun --}}
                        <circle cx="310" cy="90" r="35" fill="#FFD43B"/>
                        <circle cx="310" cy="90" r="25" fill="#FCC419"/>
                        <line x1="310" y1="45" x2="310" y2="55" stroke="#FFD43B" stroke-width="4" stroke-linecap="round"/>
                        <line x1="310" y1="125" x2="310" y2="135" stroke="#FFD43B" stroke-width="4" stroke-linecap="round"/>
                        <line x1="265" y1="90" x2="275" y2="90" stroke="#FFD43B" stroke-width="4" stroke-linecap="round"/>
                        <line x1="345" y1="90" x2="355" y2="90" stroke="#FFD43B" stroke-width="4" stroke-linecap="round"/>
                        {{-- Sun face --}}
                        <circle cx="302" cy="86" r="3" fill="#F59F00"/>
                        <circle cx="318" cy="86" r="3" fill="#F59F00"/>
                        <path d="M303 96 Q310 102 317 96" stroke="#F59F00" stroke-width="2.5" fill="none" stroke-linecap="round"/>

                        {{-- Cloud --}}
                        <ellipse cx="120" cy="100" rx="45" ry="22" fill="rgba(255,255,255,0.6)"/>
                        <ellipse cx="140" cy="90" rx="30" ry="18" fill="rgba(255,255,255,0.6)"/>
                        <ellipse cx="100" cy="95" rx="25" ry="15" fill="rgba(255,255,255,0.6)"/>

                        {{-- Building / House --}}
                        <rect x="130" y="200" width="140" height="120" rx="8" fill="rgba(255,255,255,0.9)"/>
                        <path d="M120 210 L200 155 L280 210" fill="rgba(255,255,255,0.95)" stroke="rgba(255,255,255,0.95)" stroke-width="2" stroke-linejoin="round"/>
                        <rect x="175" y="265" width="50" height="55" rx="4" fill="#C084FC"/>
                        <circle cx="215" cy="292" r="4" fill="#A855F7"/>
                        {{-- Windows --}}
                        <rect x="150" y="225" width="30" height="25" rx="4" fill="#C4B5FD"/>
                        <rect x="220" y="225" width="30" height="25" rx="4" fill="#C4B5FD"/>
                        <line x1="165" y1="225" x2="165" y2="250" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
                        <line x1="150" y1="237" x2="180" y2="237" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
                        <line x1="235" y1="225" x2="235" y2="250" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
                        <line x1="220" y1="237" x2="250" y2="237" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>

                        {{-- Heart --}}
                        <path d="M195 170 C195 162, 185 158, 185 166 C185 172, 195 180, 195 180 C195 180, 205 172, 205 166 C205 158, 195 162, 195 170Z" fill="#FB7185"/>

                        {{-- Stars --}}
                        <circle cx="85" cy="160" r="4" fill="rgba(255,255,255,0.7)"/>
                        <circle cx="320" cy="170" r="3" fill="rgba(255,255,255,0.5)"/>
                        <circle cx="290" cy="140" r="3.5" fill="rgba(255,255,255,0.6)"/>
                        <circle cx="100" cy="260" r="2.5" fill="rgba(255,255,255,0.4)"/>

                        {{-- Blocks --}}
                        <rect x="85" y="290" width="30" height="30" rx="4" fill="#34D399" opacity="0.9"/>
                        <rect x="95" y="275" width="25" height="25" rx="4" fill="#60A5FA" opacity="0.9" transform="rotate(10,107,287)"/>
                        <rect x="280" y="280" width="28" height="28" rx="4" fill="#FBBF24" opacity="0.9"/>
                        <circle cx="310" cy="270" r="14" fill="#F472B6" opacity="0.85"/>

                        {{-- Ground line --}}
                        <ellipse cx="200" cy="335" rx="150" ry="12" fill="rgba(255,255,255,0.15)"/>
                    </svg>
                </div>
                <h1>Welcome Back!</h1>
                <p>Simplify your daycare, enrich every child's journey. Manage everything in one place.</p>
                <div class="brand-dots">
                    <span class="active"></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        {{-- Right Form Panel --}}
        <div class="auth-form-panel">
            <div class="auth-form-container">

                {{-- Logo --}}
                <div class="auth-logo">
                    <div class="auth-logo-icon">
                        <i class="bi bi-house-heart-fill"></i>
                    </div>
                    <span>Daycare<em>System</em></span>
                </div>

                <h1 class="auth-heading">Sign In</h1>
                <p class="auth-subheading">Enter your credentials to access your account</p>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="auth-alert" id="auth-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="login-form">
                    @csrf

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="@error('email') is-invalid @enderror"
                                   placeholder="you@example.com"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="@error('password') is-invalid @enderror"
                                   placeholder="Enter your password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                                <i class="bi bi-eye"></i>
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="form-options">
                        <label class="custom-check">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>
                        {{-- <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a> --}}
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-auth" id="btn-signin">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="auth-divider">or</div>

                {{-- Footer --}}
                <p class="auth-footer">
                    Don't have an account? <a href="{{ route('register') }}">Create one now</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
