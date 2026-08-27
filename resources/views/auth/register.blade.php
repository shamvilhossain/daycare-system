<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create a new Daycare System account to manage your daycare operations, children, and staff.">
    <title>Register | Daycare System</title>

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
            flex: 0 0 45%;
            background: linear-gradient(135deg, var(--gradient-end) 0%, var(--gradient-mid) 50%, var(--gradient-start) 100%);
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
            max-width: 380px;
        }

        .brand-illustration {
            width: 260px;
            height: 260px;
            margin: 0 auto 2rem;
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
            font-size: 2.25rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .brand-content p {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.6;
            font-weight: 400;
        }

        .brand-features {
            margin-top: 2.5rem;
            text-align: left;
        }

        .brand-features .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.6rem 0;
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }

        .brand-features .feature i {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* ===== RIGHT PANEL (FORM) ===== */
        .auth-form-panel {
            flex: 0 0 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem 3rem;
            background: var(--surface);
            overflow-y: auto;
        }

        .auth-form-container {
            width: 100%;
            max-width: 480px;
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
            margin-bottom: 2rem;
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
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
        }

        .auth-subheading {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-bottom: 1.75rem;
        }

        /* ===== ALERT ===== */
        .auth-alert {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: var(--radius);
            padding: 0.875rem 1rem;
            margin-bottom: 1.25rem;
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
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
            font-size: 1.05rem;
            transition: var(--transition);
            pointer-events: none;
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.7rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.925rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: var(--surface);
            transition: var(--transition);
            outline: none;
            appearance: none;
            -webkit-appearance: none;
        }

        .input-wrapper select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M6 8L1 3h10L6 8z' fill='%239ca3af'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 2.5rem;
        }

        .input-wrapper input::placeholder,
        .input-wrapper select option[disabled] {
            color: var(--text-muted);
        }

        .input-wrapper input:hover,
        .input-wrapper select:hover {
            border-color: #d1d5db;
            background: var(--surface-hover);
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
            background: var(--surface);
        }

        .input-wrapper input:focus ~ .input-icon,
        .input-wrapper select:focus ~ .input-icon {
            color: var(--primary);
        }

        .input-wrapper input.is-invalid,
        .input-wrapper select.is-invalid {
            border-color: var(--error);
        }

        .input-wrapper input.is-invalid:focus,
        .input-wrapper select.is-invalid:focus {
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
            font-size: 1.05rem;
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

        /* ===== PASSWORD STRENGTH ===== */
        .password-strength {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .password-strength .bar {
            flex: 1;
            height: 4px;
            border-radius: 2px;
            background: var(--border);
            transition: var(--transition);
        }

        .password-strength.strength-1 .bar:nth-child(1) { background: #ef4444; }
        .password-strength.strength-2 .bar:nth-child(-n+2) { background: #f59e0b; }
        .password-strength.strength-3 .bar:nth-child(-n+3) { background: #10b981; }
        .password-strength.strength-4 .bar { background: #059669; }

        .strength-text {
            font-size: 0.75rem;
            margin-top: 4px;
            color: var(--text-muted);
        }

        /* ===== SUBMIT BUTTON ===== */
        .btn-auth {
            width: 100%;
            padding: 0.8rem 1.5rem;
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
            margin-top: 1.5rem;
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
            margin-top: 1.5rem;
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
            margin: 1.25rem 0;
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

        /* ===== TERMS ===== */
        .auth-terms {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: 1rem;
            line-height: 1.5;
        }

        .auth-terms a {
            color: var(--primary);
            text-decoration: none;
        }

        .auth-terms a:hover {
            text-decoration: underline;
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

        @media (max-width: 520px) {
            .auth-form-panel {
                padding: 2rem 1.5rem;
            }
            .auth-heading {
                font-size: 1.4rem;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
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
                        <circle cx="200" cy="190" r="160" fill="rgba(255,255,255,0.12)"/>
                        <circle cx="200" cy="190" r="120" fill="rgba(255,255,255,0.08)"/>

                        {{-- Stars / sparkles --}}
                        <circle cx="80" cy="70" r="4" fill="rgba(255,255,255,0.6)"/>
                        <circle cx="320" cy="90" r="3" fill="rgba(255,255,255,0.5)"/>
                        <circle cx="340" cy="180" r="3.5" fill="rgba(255,255,255,0.4)"/>
                        <circle cx="60" cy="200" r="2.5" fill="rgba(255,255,255,0.5)"/>

                        {{-- Rainbow arc --}}
                        <path d="M100 240 Q200 100 300 240" stroke="rgba(255,255,255,0.2)" stroke-width="40" fill="none" stroke-linecap="round"/>
                        <path d="M115 240 Q200 120 285 240" stroke="rgba(255,255,255,0.12)" stroke-width="20" fill="none" stroke-linecap="round"/>

                        {{-- Person 1 (adult) --}}
                        <circle cx="160" cy="215" r="22" fill="rgba(255,255,255,0.9)"/>
                        <rect x="142" y="240" width="36" height="50" rx="14" fill="rgba(255,255,255,0.85)"/>
                        {{-- Face --}}
                        <circle cx="153" cy="212" r="2.5" fill="#A78BFA"/>
                        <circle cx="167" cy="212" r="2.5" fill="#A78BFA"/>
                        <path d="M154 222 Q160 228 166 222" stroke="#A78BFA" stroke-width="2" fill="none" stroke-linecap="round"/>

                        {{-- Person 2 (child, smaller) --}}
                        <circle cx="210" cy="235" r="16" fill="#FCD34D"/>
                        <rect x="197" y="253" width="26" height="35" rx="10" fill="#FDE68A"/>
                        {{-- Face --}}
                        <circle cx="204" cy="232" r="2" fill="#92400E"/>
                        <circle cx="216" cy="232" r="2" fill="#92400E"/>
                        <path d="M206 240 Q210 244 214 240" stroke="#92400E" stroke-width="1.5" fill="none" stroke-linecap="round"/>

                        {{-- Person 3 (child) --}}
                        <circle cx="255" cy="230" r="18" fill="#6EE7B7"/>
                        <rect x="240" y="250" width="30" height="40" rx="12" fill="#A7F3D0"/>
                        {{-- Face --}}
                        <circle cx="249" cy="227" r="2" fill="#065F46"/>
                        <circle cx="261" cy="227" r="2" fill="#065F46"/>
                        <path d="M251 235 Q255 240 259 235" stroke="#065F46" stroke-width="1.5" fill="none" stroke-linecap="round"/>

                        {{-- Hearts --}}
                        <path d="M185 185 C185 179, 178 176, 178 182 C178 187, 185 193, 185 193 C185 193, 192 187, 192 182 C192 176, 185 179, 185 185Z" fill="#FB7185" opacity="0.8"/>
                        <path d="M235 195 C235 191, 230 189, 230 193 C230 196, 235 200, 235 200 C235 200, 240 196, 240 193 C240 189, 235 191, 235 195Z" fill="#FB7185" opacity="0.6"/>

                        {{-- Ground --}}
                        <ellipse cx="200" cy="305" rx="140" ry="12" fill="rgba(255,255,255,0.15)"/>

                        {{-- Blocks --}}
                        <rect x="100" cy="275" width="22" height="22" rx="4" fill="#60A5FA" opacity="0.7"/>
                        <rect x="290" y="270" width="20" height="20" rx="4" fill="#F472B6" opacity="0.7"/>
                    </svg>
                </div>
                <h1>Join Our Family</h1>
                <p>Create your account and start managing your daycare with ease.</p>

                <div class="brand-features">
                    <div class="feature">
                        <i class="bi bi-shield-check"></i>
                        <span>Secure & encrypted data</span>
                    </div>
                    <div class="feature">
                        <i class="bi bi-people"></i>
                        <span>Manage children & staff</span>
                    </div>
                    <div class="feature">
                        <i class="bi bi-calendar-check"></i>
                        <span>Track attendance & activities</span>
                    </div>
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

                <h1 class="auth-heading">Create Account</h1>
                <p class="auth-subheading">Fill in your details to get started</p>

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

                <form action="{{ route('register') }}" method="POST" id="register-form">
                    @csrf

                    {{-- First & Last Name in a row --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <div class="input-wrapper">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text"
                                       name="first_name"
                                       id="first_name"
                                       class="@error('first_name') is-invalid @enderror"
                                       placeholder="John"
                                       value="{{ old('first_name') }}"
                                       required
                                       autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <div class="input-wrapper">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text"
                                       name="last_name"
                                       id="last_name"
                                       class="@error('last_name') is-invalid @enderror"
                                       placeholder="Doe"
                                       value="{{ old('last_name') }}"
                                       required>
                            </div>
                        </div>
                    </div>

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
                                   required>
                        </div>
                    </div>

                    {{-- Phone & Role in a row --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone <span style="color: var(--text-muted); font-weight: 400;">(optional)</span></label>
                            <div class="input-wrapper">
                                <i class="bi bi-telephone input-icon"></i>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       class="@error('phone') is-invalid @enderror"
                                       placeholder="+1 (555) 000-0000"
                                       value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <div class="input-wrapper">
                                <i class="bi bi-people input-icon"></i>
                                <select name="role" id="role" class="@error('role') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role</option>
                                    <option value="parent" {{ old('role') === 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
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
                                   placeholder="Min. 8 characters"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                                <i class="bi bi-eye"></i>
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength">
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="strength-text" id="strength-text"></div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   placeholder="Re-enter your password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')" aria-label="Toggle confirm password visibility">
                                <i class="bi bi-eye"></i>
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-auth" id="btn-register">
                        <i class="bi bi-person-plus"></i>
                        <span>Create Account</span>
                    </button>
                </form>

                <p class="auth-terms">
                    By registering, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </p>

                <div class="auth-divider">or</div>

                <p class="auth-footer">
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('strength-text');

        passwordInput.addEventListener('input', function() {
            const val = this.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            strengthBar.className = 'password-strength';
            strengthText.textContent = '';

            if (val.length > 0) {
                strengthBar.classList.add('strength-' + score);
                const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
                strengthText.textContent = labels[score] || '';
            }
        });
    </script>
</body>
</html>
