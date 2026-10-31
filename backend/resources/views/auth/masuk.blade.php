<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk / Daftar Akun | Masjid Bukit Gading Cisoka</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#E8F5E9',
                            100: '#C8E6C9',
                            500: '#2E7D32',
                            600: '#1B5E20',
                            700: '#144617',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .hero-pattern {
            background-color: #144617;
            background-image: radial-gradient(#1e6b25 1.5px, transparent 1.5px), radial-gradient(#1e6b25 1.5px, #144617 1.5px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans min-h-screen flex flex-col justify-between selection:bg-primary-500 selection:text-white">

    <!-- Header Sederhana -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3 group">
                <img src="/images/logo.png" alt="Logo Masjid BGC" class="w-11 h-11 rounded-full object-cover shadow-md shadow-emerald-900/20 group-hover:scale-105 transition-transform border border-emerald-500/30">
                <div>
                    <span class="text-lg font-bold text-gray-900 tracking-tight block">Masjid BGC</span>
                    <span class="text-xs text-gray-500 -mt-1 block">Kembali ke Halaman Utama</span>
                </div>
            </a>
            <a href="/" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Beranda
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            
            <!-- Banner Header -->
            <div class="hero-pattern p-6 text-white text-center relative">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-3 border border-white/20">
                    <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold tracking-tight">Portal Akses Masjid BGC</h2>
                <p class="text-xs text-emerald-100/90 mt-1">Satu pintu masuk untuk Jamaah, Marbot, Admin & Super Admin</p>
            </div>

            <!-- Tab Switcher -->
            <div class="p-6 pb-2">
                <div class="flex p-1 bg-gray-100 rounded-2xl">
                    <button type="button" id="tab-login-btn" onclick="switchTab('login')" class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all shadow-sm bg-white text-primary-700">
                        Masuk Akun
                    </button>
                    <button type="button" id="tab-register-btn" onclick="switchTab('register')" class="flex-1 py-2.5 text-sm font-semibold rounded-xl transition-all text-gray-500 hover:text-gray-900">
                        Daftar Jamaah
                    </button>
                </div>
            </div>

            <!-- Pesan Sukses / Error -->
            <div class="px-6 pt-2">
                @if (session('success'))
                    <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Form Container -->
            <div class="p-6 pt-2">
                
                <!-- 1. FORM LOGIN -->
                <form id="form-login" action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login_email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="login_email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                        </div>
                    </div>

                    <div>
                        <label for="login_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="login_password" required placeholder="••••••••" class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="text-gray-600">Ingat Saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-primary-600 hover:bg-primary-700 active:scale-[0.99] text-white font-bold text-sm transition shadow-lg shadow-emerald-800/20 flex items-center justify-center gap-2">
                        <span>Masuk ke Akun</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>

                    <div class="pt-2 text-center text-xs text-gray-500">
                        Belum punya akun? <a href="javascript:void(0)" onclick="switchTab('register')" class="text-primary-600 font-bold hover:underline">Daftar sebagai Jamaah</a>
                    </div>
                </form>

                <!-- 2. FORM REGISTER -->
                <form id="form-register" action="{{ route('daftar.submit') }}" method="POST" class="space-y-4 hidden">
                    @csrf
                    <div>
                        <label for="reg_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="reg_name" value="{{ old('name') }}" required placeholder="Nama sesuai KTP/Panggilan" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                    </div>

                    <div>
                        <label for="reg_email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" name="email" id="reg_email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                    </div>

                    <div>
                        <label for="reg_phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP (Opsional)</label>
                        <input type="tel" name="phone" id="reg_phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                    </div>

                    <div>
                        <label for="reg_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <input type="password" name="password" id="reg_password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                    </div>

                    <div>
                        <label for="reg_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Ulangi Kata Sandi</label>
                        <input type="password" name="password_confirmation" id="reg_password_confirmation" required placeholder="Ketik ulang kata sandi" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-[11px] text-emerald-800 flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pendaftaran publik otomatis terdaftar sebagai <strong>Akun Jamaah</strong>. Setelah berhasil, Anda akan dialihkan kembali ke formulir Masuk.</span>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-primary-600 hover:bg-primary-700 active:scale-[0.99] text-white font-bold text-sm transition shadow-lg shadow-emerald-800/20 flex items-center justify-center gap-2">
                        <span>Daftarkan Akun Jamaah</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </button>

                    <div class="pt-2 text-center text-xs text-gray-500">
                        Sudah punya akun? <a href="javascript:void(0)" onclick="switchTab('login')" class="text-primary-600 font-bold hover:underline">Masuk Sekarang</a>
                    </div>
                </form>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-gray-500 border-t border-gray-200 bg-white">
        &copy; {{ date('Y') }} DKM Masjid Bukit Gading Cisoka. Seluruh hak cipta dilindungi.
    </footer>

    <script>
        function switchTab(mode) {
            const formLogin = document.getElementById('form-login');
            const formRegister = document.getElementById('form-register');
            const btnLogin = document.getElementById('tab-login-btn');
            const btnRegister = document.getElementById('tab-register-btn');

            if (mode === 'login') {
                formLogin.classList.remove('hidden');
                formRegister.classList.add('hidden');
                
                btnLogin.className = "flex-1 py-2.5 text-sm font-bold rounded-xl transition-all shadow-sm bg-white text-primary-700";
                btnRegister.className = "flex-1 py-2.5 text-sm font-semibold rounded-xl transition-all text-gray-500 hover:text-gray-900";
            } else {
                formLogin.classList.add('hidden');
                formRegister.classList.remove('hidden');

                btnRegister.className = "flex-1 py-2.5 text-sm font-bold rounded-xl transition-all shadow-sm bg-white text-primary-700";
                btnLogin.className = "flex-1 py-2.5 text-sm font-semibold rounded-xl transition-all text-gray-500 hover:text-gray-900";
            }
        }

        // Jika terjadi error pada validasi pendaftaran, arahkan langsung ke tab register
        @if ($errors->has('name') || $errors->has('password_confirmation') || old('name'))
            switchTab('register');
        @endif
    </script>
</body>
</html>
