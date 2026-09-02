<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DaycareSystem | Where Little Minds Grow</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #FF6B6B;     /* Warm coral/red */
            --primary-dark: #E65A5A;
            --secondary-color: #4ECDC4;   /* Soft teal */
            --accent-color: #FFE66D;      /* Playful yellow */
            --text-dark: #2D3436;         
            --text-muted: #636E72;
            --bg-light: #F7F9FC;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            color: var(--text-dark);
            background-color: #ffffff;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .brand-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        /* Navbar */
        .navbar {
            padding: 1.25rem 0;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            padding: 0.5rem 1.25rem !important;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        .btn-login {
            font-weight: 600;
            color: var(--text-dark);
            background: transparent;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .btn-register {
            font-weight: 600;
            background: var(--primary-color);
            color: white !important;
            border-radius: 50px;
            padding: 0.6rem 1.75rem;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            transition: all 0.3s ease;
            border: none;
        }
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        /* Hero Section */
        .hero-section {
            padding: 7rem 0 5rem 0;
            background: linear-gradient(135deg, #fff9f9 0%, #f0fdfc 100%);
            position: relative;
        }
        .hero-shape {
            position: absolute;
            z-index: 0;
            opacity: 0.6;
        }
        .hero-shape-1 { top: 10%; left: -5%; width: 300px; }
        .hero-shape-2 { bottom: -10%; right: -5%; width: 400px; }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--secondary-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        .hero-title {
            font-size: 3.8rem;
            line-height: 1.15;
            color: #1a202c;
            margin-bottom: 1.5rem;
        }
        .hero-title span {
            color: var(--primary-color);
            position: relative;
        }
        .hero-title span::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 12px;
            background: var(--accent-color);
            z-index: -1;
            opacity: 0.6;
            border-radius: 10px;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.6;
            max-width: 90%;
        }
        
        /* Hero Images & Floating Cards */
        .hero-image-wrapper {
            position: relative;
            z-index: 1;
        }
        .hero-main-img {
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            width: 100%;
            border: 8px solid white;
            object-fit: cover;
            height: 550px;
        }
        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 1.25rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: float 4s ease-in-out infinite;
        }
        .fc-1 {
            top: 40px;
            left: -40px;
            animation-delay: 0s;
        }
        .fc-2 {
            bottom: 80px;
            right: -30px;
            animation-delay: 1.5s;
        }
        .fc-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .fc-icon.green { background: #d1fae5; color: #059669; }
        .fc-icon.orange { background: #ffedd5; color: #ea580c; }
        .fc-text h6 { margin: 0; font-size: 0.95rem; font-weight: 700; color: #1f2937; }
        .fc-text p { margin: 0; font-size: 0.8rem; color: #6b7280; font-weight: 500; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background: #ffffff;
        }
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .section-header h2 {
            font-size: 2.5rem;
            color: #1a202c;
        }
        .feature-card {
            padding: 2.5rem 2rem;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: transparent;
        }
        .feature-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .f-icon-1 { background: #fee2e2; color: #ef4444; }
        .f-icon-2 { background: #e0e7ff; color: #4f46e5; }
        .f-icon-3 { background: #ccfbf1; color: #0d9488; }
        .feature-card h4 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .feature-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Split Section */
        .split-section {
            padding: 5rem 0;
            background: var(--bg-light);
            border-radius: 40px;
            margin: 0 1rem;
        }
        .split-img {
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            width: 100%;
        }
        .check-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }
        .check-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 1rem;
            font-size: 1.05rem;
            font-weight: 500;
            color: #475569;
        }
        .check-list li i {
            color: var(--secondary-color);
            font-size: 1.3rem;
            margin-top: -2px;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            text-align: center;
        }
        .cta-box {
            background: var(--primary-color);
            border-radius: 32px;
            padding: 4rem 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
        }
        .cta-box h2 {
            font-size: 2.8rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .cta-box p {
            font-size: 1.15rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        .cta-btn {
            background: white;
            color: var(--primary-color) !important;
            font-weight: 700;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            text-decoration: none;
            display: inline-block;
        }
        .cta-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .cta-shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .cta-shape-1 { width: 300px; height: 300px; top: -100px; left: -50px; }
        .cta-shape-2 { width: 400px; height: 400px; bottom: -150px; right: -100px; }

        /* Footer */
        footer {
            padding: 3rem 0;
            border-top: 1px solid #f1f5f9;
        }
        .footer-brand {
            font-size: 1.4rem;
            color: var(--primary-color);
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
        }
        .footer-text {
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* SVG Shapes */
        .blob-svg { position: absolute; z-index: -1; }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.8rem; }
            .fc-1 { left: -10px; }
            .fc-2 { right: -10px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-house-heart-fill"></i>
                <span class="brand-text">DaycareSystem</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-2">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#parents">For Parents</a></li>
                    <li class="nav-item"><a class="nav-link" href="#centers">For Centers</a></li>
                </ul>
                <div class="d-flex gap-3 align-items-center mt-3 mt-lg-0">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-register">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login">Sign In</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-register">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section overflow-hidden">
        <svg class="hero-shape hero-shape-1" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <path fill="#FF6B6B" opacity="0.1" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.1,-46.3C90.4,-33.5,96,-18.1,96.3,-2.6C96.6,12.9,91.6,28.5,82.8,41.9C74,55.3,61.4,66.6,47.4,74.1C33.4,81.6,18,85.3,2.6,80.9C-12.8,76.5,-28.3,64,-42.1,52.2C-55.9,40.4,-68.1,29.3,-75.6,15.2C-83.1,1.1,-85.9,-16,-79.8,-29.4C-73.7,-42.8,-58.7,-52.5,-44.6,-59.7C-30.5,-66.9,-17.3,-71.6,-1.7,-68.6C13.9,-65.6,27.9,-54.9,44.7,-76.4Z" transform="translate(100 100)" />
        </svg>
        <svg class="hero-shape hero-shape-2" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <path fill="#4ECDC4" opacity="0.1" d="M51.6,-66.8C65.5,-55.8,74.6,-39.7,78.8,-22.4C83,-5.1,82.3,13.4,75.2,30.3C68.1,47.2,54.6,62.5,38.2,69.5C21.8,76.5,2.5,75.2,-14.7,69.6C-31.9,64,-47,54.1,-58.3,40.7C-69.6,27.3,-77.1,10.4,-77.5,-6.6C-77.9,-23.6,-71.2,-40.7,-59.1,-52C-47,-63.3,-29.5,-68.8,-11.9,-70.7C5.7,-72.6,23.5,-70.9,37.7,-77.8Z" transform="translate(100 100)" />
        </svg>

        <div class="container">
            <div class="row align-items-center g-5 pt-5">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge">
                        <i class="bi bi-stars"></i> The #1 Childcare Platform
                    </div>
                    <h1 class="hero-title">
                        Where Little Minds Grow & Parents Find <span>Peace</span>.
                    </h1>
                    <p class="hero-subtitle">
                        The all-in-one daycare platform bridging the gap between exceptional childcare and modern, stress-free management.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn btn-register px-4 py-3 fs-6 d-flex align-items-center gap-2">
                            Enroll Your Child <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#centers" class="btn btn-login px-4 py-3 fs-6 d-flex align-items-center gap-2">
                            For Center Owners <i class="bi bi-building"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-image-wrapper">
                        <!-- High quality stock image of kids playing / learning -->
                        <img src="https://images.unsplash.com/photo-1543269664-56d74c65a358?q=80&w=1470&auto=format&fit=crop" alt="Children playing in daycare" class="hero-main-img">
                        
                        <!-- Floating Micro-animation Cards -->
                        <div class="floating-card fc-1">
                            <div class="fc-icon green"><i class="bi bi-check-circle-fill"></i></div>
                            <div class="fc-text">
                                <h6>Activity Log</h6>
                                <p>Emma finished her lunch! 🍎</p>
                            </div>
                        </div>
                        
                        <div class="floating-card fc-2">
                            <div class="fc-icon orange"><i class="bi bi-bell-fill"></i></div>
                            <div class="fc-text">
                                <h6>New Update</h6>
                                <p>Nap time started at 1:00 PM 💤</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header">
                <h2>Everything you need. <br>Nothing you don't.</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper f-icon-1">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h4>Safe & Secure</h4>
                        <p>End-to-end encrypted profiles, secure check-in/out systems, and verified parent identities for maximum safety.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper f-icon-2">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <h4>Live Daily Logs</h4>
                        <p>Meals, naps, potty breaks, and learning milestones documented in real-time by staff for parents to see.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper f-icon-3">
                            <i class="bi bi-credit-card-2-front-fill"></i>
                        </div>
                        <h4>Automated Billing</h4>
                        <p>Generate invoices, send payment reminders, and process secure payments online without the paperwork.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Split Section: For Parents -->
    <section id="parents" class="mb-5">
        <div class="split-section container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 order-2 order-lg-1">
                    <img src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=1440&auto=format&fit=crop" alt="Parent and child" class="split-img">
                </div>
                <div class="col-lg-7 order-1 order-lg-2 ps-lg-5">
                    <div class="hero-badge bg-white text-primary border"><i class="bi bi-heart-fill"></i> For Parents</div>
                    <h2 class="mb-4" style="font-size: 2.4rem; color: #1a202c;">Peace of mind in your pocket.</h2>
                    <p class="fs-5 text-muted mb-4">Stay connected to your child's day, no matter where you are. Our platform ensures you never miss a milestone.</p>
                    
                    <ul class="check-list">
                        <li><i class="bi bi-check-circle-fill"></i> Receive real-time photos and activity updates.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Direct messaging with teachers and administration.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Easy online tuition payments and invoice tracking.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Secure profile management for medical and emergency info.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Split Section: For Centers -->
    <section id="centers" class="mb-5 pb-4">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 pe-lg-5">
                    <div class="hero-badge bg-light text-primary border"><i class="bi bi-building"></i> For Daycare Centers</div>
                    <h2 class="mb-4" style="font-size: 2.4rem; color: #1a202c;">Run your center like a pro.</h2>
                    <p class="fs-5 text-muted mb-4">Ditch the paper trails. Manage enrollments, staff, and parent communications from one beautiful dashboard.</p>
                    
                    <ul class="check-list">
                        <li><i class="bi bi-check-circle-fill"></i> Centralized dashboard for total center overview.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Staff scheduling, roles, and permission management.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Automated invoicing and financial reporting.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Digital attendance tracking for children and staff.</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576089172869-4f5f6f315620?q=80&w=1474&auto=format&fit=crop" alt="Daycare classroom" class="split-img">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <div class="cta-shape cta-shape-1"></div>
                <div class="cta-shape cta-shape-2"></div>
                <h2>Ready to join our family?</h2>
                <p>Experience the most intuitive childcare management software today.</p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="cta-btn">Create Your Free Account</a>
                @else
                    <a href="{{ route('login') }}" class="cta-btn">Sign In to Dashboard</a>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="footer-brand">
                    <i class="bi bi-house-heart-fill"></i> DaycareSystem
                </div>
                <div class="footer-text">
                    &copy; {{ date('Y') }} DaycareSystem. All rights reserved.
                </div>
                <div class="d-flex gap-3 fs-5 text-muted">
                    <a href="#" class="text-muted text-decoration-none"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-muted text-decoration-none"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted text-decoration-none"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
