<!DOCTYPE html>
<html lang="en" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - ChickenPOS Enterprise</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .login-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            width: 100%;
            max-width: 960px;
            height: 540px; /* Optimized fixed height to ensure it fits perfectly inside small laptop screens without scrolling */
            display: flex;
        }

        .login-branding-section {
            background: #0f172a;
            color: white;
            width: 45%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid #1e293b;
        }

        .login-branding-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f59e0b' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
        }

        .login-form-section {
            width: 55%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
        }

        .branding-icon {
            background: rgba(245, 158, 11, 0.08);
            color: #d97706;
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            border: 1px solid rgba(245, 158, 11, 0.15);
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            font-size: 14px;
            transition: all 150ms ease;
            background: #f8fafc;
        }

        .form-group input:focus {
            outline: none;
            border-color: #d97706;
            background: white;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.12);
        }

        /* Error input states */
        .form-group input.border-rose-500 {
            border-color: #f43f5e;
            background: #fff1f2;
        }
        .form-group input.border-rose-500:focus {
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.12);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #1e293b;
            color: #ffffff;
            border: none;
            border-radius: 0.375rem;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 150ms ease;
        }

        .login-btn:hover {
            background: #0f172a;
        }

        @media (max-width: 768px) {
            body { padding: 16px; }
            .login-container { height: auto; max-width: 420px; }
            .login-branding-section { display: none; }
            .login-form-section { width: 100%; padding: 32px 24px; gap: 24px; }
        }
    </style>
</head>
<body class="overflow-hidden">

    <div class="login-container">
        <!-- Left Section: Premium Corporate Identity Layout -->
        <div class="login-branding-section">
            <div class="flex items-center gap-3 z-10">
                <div class="branding-icon">
                    <i class="fas fa-drumstick-bite"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-white">ChickenPOS</h2>
                    <p class="text-[11px] text-amber-500 font-medium uppercase tracking-wider">Management Terminal</p>
                </div>
            </div>

            <div class="z-10 my-auto">
                <h3 class="text-xl font-semibold text-slate-100 mb-2.5 leading-snug">Unified operations control ecosystem.</h3>
                <p class="text-slate-400 text-xs leading-relaxed max-w-xs">
                    Access localized commerce utilities, manage system tracking data channels, and review transactional metrics securely.
                </p>
            </div>

            <div class="flex items-center gap-4 border-t border-slate-800 pt-5 z-10 text-slate-500 text-[11px]">
                <span class="flex items-center gap-1.5"><i class="fas fa-circle text-[6px] text-emerald-500"></i> Gateway Active</span>
                <span>•</span>
                <span>v2.4.0</span>
            </div>
        </div>

        <!-- Right Section: Clean Professional Input Form -->
        <div class="login-form-section">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight mb-1">System Authentication</h1>
                <p class="text-xs text-slate-500">Sign in with your workplace credentials to proceed.</p>
            </div>

            <form id="loginForm" method="POST" action="/login" class="flex flex-col gap-4">
                @csrf

                <!-- Email Form Unit -->
                <div class="form-group">
                    <label for="email" class="block text-xs font-medium text-slate-600 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="employee@company.com" required autocomplete="email" class="@error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-[11px] text-rose-600 mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Form Unit -->
                <div class="form-group">
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="text-xs font-medium text-slate-600">Password</label>
                        {{-- <a href="/forgot-password" class="text-[11px] text-amber-600 font-medium hover:underline focus:outline-none">Forgot password?</a> --}}
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" class="@error('password') border-rose-500 @enderror">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs focus:outline-none" aria-label="Toggle password view state">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-600 mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Persistence Config Checkbox -->
                <div class="flex items-center text-xs mt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" id="remember" name="remember" value="1" class="rounded border-slate-300 text-slate-800 focus:ring-slate-800 w-3.5 h-3.5">
                        <span>Remember me</span>
                    </label>
                </div>

                <!-- Primary Action Executable -->
                <button type="submit" class="login-btn flex justify-center items-center mt-2">
                    Log In
                </button>
            </form>

            <!-- Global Architecture Regulations Footer -->
            <div class="border-t border-slate-100 pt-4 flex items-center justify-between text-[10px] text-slate-400">
                <span>© 2026 ChickenPOS Architecture.</span>
                <div class="flex gap-2">
                    <a href="#" class="hover:text-slate-600">Security Architecture</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        window.addEventListener('load', () => {
            // Keep focus management logical if no specific error state is present
            if (!document.querySelector('.border-rose-500')) {
                document.getElementById('email').focus();
            }
        });
    </script>
</body>
</html>
