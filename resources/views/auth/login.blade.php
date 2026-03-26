<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - ConcreteERP</title>
    <meta name="description" content="نظام إدارة الخرسانة الجاهزة">

    {{-- Favicon: صفحة تسجيل الدخول --}}
    @php
        $fav = 'assets/favicons/login.svg';
        try {
            $favAbs = public_path($fav);
            $favVer = is_file($favAbs) ? (string) filemtime($favAbs) : (string) time();
        } catch (\Throwable $e) {
            $favVer = (string) time();
        }
        $favUrl = asset($fav) . '?v=' . $favVer;
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $favUrl }}">
    <link rel="icon" type="image/svg+xml" href="{{ $favUrl }}">
    <link rel="shortcut icon" href="{{ $favUrl }}">
    <style>
        /* ===== Light Theme (Default) ===== */
        :root {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-accent: #667eea;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --input-bg: #f1f5f9;
            --input-border: #e2e8f0;
            --input-focus: #667eea;
            --btn-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --btn-hover: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            --accent: #667eea;
            --focus-ring: rgba(102, 126, 234, 0.2);
        }

        /* ===== Dark Theme ===== */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-accent: #4f46e5;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --input-bg: #334155;
            --input-border: #475569;
            --input-focus: #818cf8;
            --btn-primary: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --btn-hover: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            --accent: #818cf8;
            --focus-ring: rgba(129, 140, 248, 0.25);
        }

        /* ===== Reset & Base ===== */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            transition: background 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }

        /* ===== Background (static, lightweight) ===== */
        .bg-shape {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
        }

        /* ===== Login Container ===== */
        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
        }

        /* ===== Theme Switcher ===== */
        .theme-switcher {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            display: flex;
            gap: 0.5rem;
            z-index: 100;
            background: var(--bg-secondary);
            padding: 0.5rem;
            border-radius: 2rem;
            box-shadow: var(--card-shadow);
        }

        .theme-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .theme-btn:hover {
            transform: scale(1.1);
        }

        .theme-btn.active {
            border-color: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }

        .theme-btn[data-theme="light"] {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        }

        .theme-btn[data-theme="dark"] {
            background: linear-gradient(135deg, #1e293b, #0f172a);
        }

        /* ===== Login Card ===== */
        .login-card {
            background: var(--bg-secondary);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--btn-primary);
        }

        /* ===== Logo/Brand ===== */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 70px;
            height: 70px;
            background: var(--btn-primary);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px -10px var(--accent);
        }

        .brand-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .brand h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .brand p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* ===== Alert Messages ===== */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .alert-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }

        .alert-content h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .alert-content p {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .alert-note {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            font-size: 0.75rem;
        }

        /* ===== Form Styles ===== */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 3rem 0.9rem 3rem;
            background: var(--input-bg);
            border: 2px solid var(--input-border);
            border-radius: 0.75rem;
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
            outline: none;
            text-align: right;
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 4px var(--focus-ring);
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--text-muted);
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--input-focus);
        }

        .password-toggle {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 0.25rem;
            transition: color 0.3s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--accent);
        }

        /* ===== Submit Button ===== */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--btn-primary);
            border: none;
            border-radius: 0.75rem;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-submit:hover {
            background: var(--btn-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px var(--accent);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-submit .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 0.5rem;
        }

        .btn-submit.loading .spinner {
            display: inline-block;
        }

        .btn-submit.loading .btn-text {
            opacity: 0.7;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ===== Footer ===== */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        /* ===== Responsive ===== */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .login-card {
                padding: 1.5rem;
                border-radius: 1rem;
            }

            .brand h1 {
                font-size: 1.5rem;
            }

            .theme-switcher {
                top: 0.75rem;
                left: 0.75rem;
                padding: 0.35rem;
            }

            .theme-btn {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
            }
        }

        @media (min-width: 768px) {
            .login-card {
                padding: 3rem;
            }
        }

        @media (min-width: 1024px) {
            .login-container {
                max-width: 460px;
            }
        }

        /* ===== Animations ===== */
        .fade-in {
            animation: fadeIn 0.35s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== Focus Visible ===== */
        *:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <div class="bg-shape" aria-hidden="true"></div>

    <div class="theme-switcher">
        <button class="theme-btn active" data-theme="light" title="الوضع الفاتح" aria-label="Light theme">☀️</button>
        <button class="theme-btn" data-theme="dark" title="الوضع الداكن" aria-label="Dark theme">🌙</button>
    </div>

    <!-- Login Container -->
    <div class="login-container fade-in">
        <div class="login-card">
            <!-- Brand -->
            <div class="brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 2.18l6.9 3.45L12 11.09 5.1 7.63 12 4.18zM4 8.82l7 3.5v7.36l-7-3.5V8.82zm9 10.86v-7.36l7-3.5v7.36l-7 3.5z" />
                    </svg>
                </div>
                <h1>ConcreteERP</h1>
                <p>نظام إدارة الخرسانة الجاهزة</p>
            </div>

            <!-- Success Alert -->
            @if (session('success'))
                <div class="alert alert-success">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="alert-content">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Alert -->
            @if (session('error'))
                <div class="alert alert-error">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="alert-content">
                        <h4>⚠️ تنبيه</h4>
                        <p>{{ session('error') }}</p>
                        @if (str_contains(session('error'), 'معطل') || str_contains(session('error'), 'إيقاف'))
                            <div class="alert-note">📞 للاستفسار يرجى التواصل مع الإدارة</div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Username Field -->
                <div class="form-group">
                    <label class="form-label" for="username">اسم المستخدم</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <input type="text" id="username" name="username" class="form-input"
                            placeholder="أدخل اسم المستخدم" required autocomplete="username"
                            value="{{ old('username') }}">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label" for="password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input type="password" id="password" name="password" class="form-input"
                            placeholder="أدخل كلمة المرور" required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()"
                            aria-label="إظهار/إخفاء كلمة المرور">
                            <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">تسجيل الدخول</span>
                    <span class="spinner"></span>
                </button>

            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p>© {{ date('Y') }} ConcreteERP - جميع الحقوق محفوظة</p>
                <p style="margin-top: 0.5rem;">
                    <a href="{{ route('system-benefits') }}" style="color: var(--accent); text-decoration: none;">
                        ما هي فوائد النظام؟
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var theme = localStorage.getItem('login-theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);

            document.querySelector('.theme-switcher').addEventListener('click', function(e) {
                var btn = e.target.closest('.theme-btn');
                if (!btn) return;
                theme = btn.dataset.theme;
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('login-theme', theme);
                document.querySelectorAll('.theme-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
            });

            document.querySelectorAll('.theme-btn').forEach(function(b) {
                b.classList.toggle('active', b.dataset.theme === theme);
            });

            window.togglePassword = function() {
                var el = document.getElementById('password');
                var icon = document.getElementById('eyeIcon');
                if (el.type === 'password') {
                    el.type = 'text';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
                } else {
                    el.type = 'password';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                }
            };

            document.getElementById('loginForm').addEventListener('submit', function() {
                var btn = document.getElementById('submitBtn');
                btn.classList.add('loading');
                btn.disabled = true;
            });
        })();
    </script>
</body>

</html>
