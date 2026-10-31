<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masjid Bukit Gading Cisoka | Beranda Resmi</title>
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
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans min-h-screen flex flex-col selection:bg-primary-500 selection:text-white">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3 group">
                <img src="/images/logo.png" alt="Logo Masjid BGC" class="w-12 h-12 rounded-full object-cover shadow-md shadow-emerald-900/20 group-hover:scale-105 transition-transform border border-emerald-500/30">
                <div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight block">Masjid BGC</span>
                    <span class="text-xs text-gray-500 -mt-1 block">Bukit Gading Cisoka, Tangerang</span>
                </div>
            </a>

            <!-- Nav Links & Auth Button -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <a href="#transparansi" class="text-sm font-semibold text-gray-600 hover:text-primary-600 hidden md:inline-block">
                    Transparansi Kas
                </a>
                <a href="#kegiatan" class="text-sm font-semibold text-gray-600 hover:text-primary-600 hidden md:inline-block">
                    Agenda Kajian
                </a>
                <a href="/download-app" class="text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3.5 py-2 rounded-xl transition hidden sm:inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993 0 .5511-.4483.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5802 8.3615 13.8441 8 12 8s-3.5802.3615-5.1328.9497L4.8449 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.152 5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396" />
                    </svg>
                    Download APK
                </a>

                @auth
                    <!-- Sudah login -->
                    <a href="{{ auth()->user()->role === 'jamaah' ? route('beranda') : url('/admin') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 active:scale-95 transition shadow-lg shadow-emerald-700/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard ({{ auth()->user()->name }})</span>
                    </a>
                @else
                    <!-- Belum login -> Halaman Tunggal Masuk / Daftar -->
                    <a href="{{ route('masuk') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 active:scale-95 transition shadow-lg shadow-emerald-700/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Masuk / Daftar</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-pattern text-white py-16 sm:py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Kiri: Penjelasan -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-semibold backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Portal Resmi Masjid Bukit Gading Cisoka
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                        Transparansi Kas & <span class="text-emerald-300">Amanah Umat</span>
                    </h1>

                    <p class="text-base sm:text-lg text-emerald-100/90 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        Pusat peribadatan, dakwah, dan layanan sosial kemasyarakatan. Pantau laporan kas keuangan, kegiatan kajian, program infaq jariyah, zakat fitrah & maal secara terbuka dan real-time.
                    </p>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('masuk') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-white text-primary-700 hover:bg-emerald-50 active:scale-95 transition-all shadow-xl shadow-black/20 font-bold text-base">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Masuk / Daftar Akun</span>
                        </a>

                        <a href="/download-app" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-primary-700/80 hover:bg-primary-700 text-white font-semibold text-sm border border-emerald-400/30 transition">
                            <svg class="w-5 h-5 text-emerald-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993 0 .5511-.4483.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5802 8.3615 13.8441 8 12 8s-3.5802.3615-5.1328.9497L4.8449 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.152 5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396" />
                            </svg>
                            <span>Unduh Aplikasi Mobile (APK)</span>
                        </a>
                    </div>
                </div>

                <!-- Kanan: Card Kas Real-Time Dinamis -->
                <div class="lg:col-span-5 flex flex-col items-center">
                    <div class="bg-white rounded-3xl p-6 shadow-2xl text-gray-900 w-full max-w-sm border border-emerald-100">
                        <div class="text-center pb-4 border-b border-gray-100">
                            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full mb-1">
                                Transparansi Kas Terkini
                            </span>
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $latestSummary ? $latestSummary->period_label : 'Periode Berjalan' }}
                            </h3>
                        </div>

                        <div class="py-5 space-y-4">
                            <!-- Saldo Akhir -->
                            <div class="text-center p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <span class="text-xs font-medium text-emerald-700 block">Saldo Kas Bersih</span>
                                <span class="text-2xl font-black text-emerald-900 block mt-0.5">
                                    Rp {{ number_format($closingBalance, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Pemasukan & Pengeluaran -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Pemasukan</span>
                                    <span class="text-xs font-bold text-emerald-700 block mt-0.5">
                                        +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Pengeluaran</span>
                                    <span class="text-xs font-bold text-rose-700 block mt-0.5">
                                        -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('masuk') }}" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-md shadow-emerald-700/20">
                            <span>Masuk untuk Detail Lengkap</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Section 1: Pengumuman & Warta Dinamis -->
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary-600 block mb-1">Warta DKM</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Pengumuman & Informasi Terbaru</h2>
                </div>
            </div>

            @if($announcements->isEmpty())
                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-2xl">
                    Belum ada pengumuman baru yang dipublikasikan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($announcements as $ann)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-emerald-200 hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-semibold text-primary-600 block mb-2">
                                    {{ $ann->published_at ? \Carbon\Carbon::parse($ann->published_at)->isoFormat('dddd, D MMMM Y') : 'Terbaru' }}
                                </span>
                                <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">{{ $ann->title }}</h3>
                                <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">{{ $ann->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Section 2: Jadwal Kegiatan & Kajian Dinamis -->
    <section id="kegiatan" class="py-16 bg-gray-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-primary-600 block mb-1">Majelis Ilmu</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Agenda Kegiatan & Kajian Rutin</h2>
                <p class="text-xs text-gray-500 mt-2">Daftar majelis ta'lim dan pelatihan fardhu kifayah di Masjid Bukit Gading Cisoka.</p>
            </div>

            @if($events->isEmpty())
                <div class="p-8 text-center text-gray-400 bg-white rounded-2xl border border-gray-100">
                    Belum ada agenda kajian mendatang.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($events as $ev)
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-emerald-300 transition flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-2 mb-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $ev->organizer ?? 'DKM BGC' }}
                                    </span>
                                    <span class="text-xs font-bold text-gray-500">
                                        {{ substr($ev->event_time, 0, 5) }} WIB
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mb-2">{{ $ev->title }}</h3>
                                <p class="text-xs text-gray-600 line-clamp-2 mb-4">{{ $ev->description }}</p>
                            </div>
                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                                <span class="text-gray-500">📍 {{ $ev->location }}</span>
                                <span class="font-bold text-primary-600">
                                    {{ \Carbon\Carbon::parse($ev->event_date)->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Section 3: Jejak Amanah (Alokasi & Realisasi) Dinamis -->
    <section id="transparansi" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-primary-600 block mb-1">Transparansi Dana</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Jejak Amanah Umat</h2>
                <p class="text-xs text-gray-500 mt-2">Pencatatan terbuka alokasi dana donasi dan realisasi fisik belanja kebutuhan masjid.</p>
            </div>

            @if($transparency->isEmpty())
                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-2xl">
                    Data realisasi program belum dipublikasikan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($transparency as $item)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 flex flex-col justify-between">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-100 text-primary-800 uppercase">
                                    {{ $item->status }}
                                </span>
                                <h3 class="text-base font-bold text-gray-900 mt-2 mb-1">{{ $item->title }}</h3>
                                <p class="text-xs text-gray-600 mb-4">{{ $item->description }}</p>
                            </div>
                            <div class="p-4 bg-white rounded-xl border border-gray-100 space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Total Alokasi:</span>
                                    <span class="font-bold text-gray-800">Rp {{ number_format($item->total_allocated, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Terealisasi:</span>
                                    <span class="font-bold text-emerald-600">Rp {{ number_format($item->total_realized, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:flex sm:justify-between sm:items-center">
            <div class="mb-4 sm:mb-0 text-left">
                <span class="text-white font-bold text-lg block">DKM Masjid Bukit Gading Cisoka</span>
                <span class="text-xs text-gray-400">Kec. Cisoka, Kabupaten Tangerang, Banten</span>
            </div>
            <div class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Masjid BGC. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

</body>
</html>
