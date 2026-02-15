<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Agent SubDomain') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            *,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;line-height:1.5;font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"}body{line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:1em}small{font-size:80%}sub,sup{vertical-align:baseline;font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,select,textarea{font:inherit;letter-spacing:inherit;color:inherit;background-color:transparent}::-webkit-search-decoration{-webkit-appearance:none}::placeholder{opacity:1;color:color-mix(in oklab,currentColor 50%,transparent)}textarea{resize:vertical}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@4/lib/index.min.css" />
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .smooth-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }
        .step-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-950 via-gray-900 to-gray-900 text-white">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 backdrop-blur-md bg-gray-950/50 border-b border-purple-500/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <!-- Logo SVG -->
                <svg width="32" height="32" viewBox="0 0 32 32" class="text-purple-400">
                    <circle cx="16" cy="16" r="14" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 8v16M8 16h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="16" cy="16" r="3" fill="currentColor"/>
                </svg>
                <span class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Agent SubDomain
                </span>
            </div>
            <div class="flex gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-300 hover:text-white transition">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg transition">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center justify-center pt-20 relative overflow-hidden">
        <!-- Background elements -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative z-10 text-center px-4 sm:px-6 max-w-4xl">
            <h1 class="text-5xl sm:text-7xl font-bold mb-6 leading-tight">
                Kelola Subdomain dengan Mudah
            </h1>
            <p class="text-lg sm:text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
                Platform terpadu untuk request, review, dan deployment subdomain dengan sistem approval berbasis role.
                Aman, cepat, dan mudah digunakan.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="btn-primary px-8 py-4 rounded-lg font-semibold text-white inline-block">
                    Mulai Sekarang
                </a>
                <a href="#features" class="btn-secondary px-8 py-4 rounded-lg font-semibold text-white inline-block">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-900">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16">Cara Kerja Sistem</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Step 1: Request -->
                <div class="text-center">
                    <div class="flex justify-center mb-6">
                        <div class="step-circle">1</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Request Subdomain</h3>
                    <p class="text-gray-400">
                        User mengajukan request subdomain baru dengan mengisi form sederhana. Sistem otomatis memvalidasi format dan batasan kuota.
                    </p>
                </div>

                <!-- Step 2: Review -->
                <div class="text-center">
                    <div class="flex justify-center mb-6">
                        <div class="step-circle">2</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Review oleh Admin</h3>
                    <p class="text-gray-400">
                        Admin mereview setiap request, dapat menyetujui atau menolak berdasarkan kebijakan bisnis yang ada.
                    </p>
                </div>

                <!-- Step 3: Deploy -->
                <div class="text-center">
                    <div class="flex justify-center mb-6">
                        <div class="step-circle">3</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Deploy Otomatis</h3>
                    <p class="text-gray-400">
                        Setelah approval, subdomain langsung di-deploy ke sistem DNS dan siap digunakan dalam hitungan detik.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-950">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16">Fitur Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8 bg-gray-900 rounded-2xl">
                    <div class="text-4xl mb-4">⚡</div>
                    <h3 class="text-2xl font-bold mb-3">Lightning Fast</h3>
                    <p class="text-gray-400">
                        Deployment subdomain hanya butuh beberapa detik. Sistem yang dioptimalkan untuk performa maksimal.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card p-8 bg-gray-900 rounded-2xl">
                    <div class="text-4xl mb-4">🔒</div>
                    <h3 class="text-2xl font-bold mb-3">Aman & Terpercaya</h3>
                    <p class="text-gray-400">
                        Sistem berbasis role dengan approval workflow yang ketat. Setiap aksi ter-log untuk audit trail.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card p-8 bg-gray-900 rounded-2xl">
                    <div class="text-4xl mb-4">👥</div>
                    <h3 class="text-2xl font-bold mb-3">Role Based Access</h3>
                    <p class="text-gray-400">
                        Kontrol penuh dengan sistem role. Admin dapat mengelola user dan menetapkan batasan kuota per user.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card p-8 bg-gray-900 rounded-2xl">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold mb-3">Dashboard Analytics</h3>
                    <p class="text-gray-400">
                        Dashboard interaktif dengan statistik lengkap. Monitor kelola semua subdomain dari satu tempat.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card p-8 bg-gray-900 rounded-2xl">
                    <div class="text-4xl mb-4">📝</div>
                    <h3 class="text-2xl font-bold mb-3">Activity Logging</h3>
                    <p class="text-gray-400">
                        Setiap aktivitas tercatat lengkap. History requests, approvals, dan changes tersimpan untuk referensi.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card p-8 bg-gray-900 rounded-2xl">
                    <div class="text-4xl mb-4">☁️</div>
                    <h3 class="text-2xl font-bold mb-3">Cloud Integrated</h3>
                    <p class="text-gray-400">
                        Terintegrasi dengan sistem DNS Radnet. Skalabel dan siap untuk production environment.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-900">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-5xl font-bold text-transparent bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text mb-2">
                        100+
                    </div>
                    <p class="text-gray-400">Subdomains Deployed</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold text-transparent bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text mb-2">
                        50+
                    </div>
                    <p class="text-gray-400">Active Users</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold text-transparent bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text mb-2">
                        99.9%
                    </div>
                    <p class="text-gray-400">Uptime</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-purple-600/20 to-pink-600/20 border-y border-purple-500/20">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-6">Siap Memulai?</h2>
            <p class="text-xl text-gray-300 mb-8">
                Bergabunglah dengan ratusan developer yang sudah menggunakan Agent SubDomain untuk mengelola subdomain mereka.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn-primary px-8 py-4 rounded-lg font-semibold text-white inline-block">
                    Daftar Sekarang
                </a>
                <a href="mailto:support@unnar.id" class="btn-secondary px-8 py-4 rounded-lg font-semibold text-white inline-block">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-purple-500/10 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <svg width="28" height="28" viewBox="0 0 32 32" class="text-purple-400">
                            <circle cx="16" cy="16" r="14" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 8v16M8 16h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="16" cy="16" r="3" fill="currentColor"/>
                        </svg>
                        <span class="font-bold">Agent SubDomain</span>
                    </div>
                    <p class="text-gray-400 text-sm">Platform manajemen subdomain terpadu untuk bisnis modern.</p>
                </div>

                <!-- Product -->
                <div>
                    <h4 class="font-bold mb-4">Produk</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Fitur</a></li>
                        <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition">Security</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="font-bold mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Agent SubDomain. Semua hak dilindungi.
                </p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition">Twitter</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">GitHub</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>

    @if (Route::has('login') && !Auth::check())
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>
</html>
