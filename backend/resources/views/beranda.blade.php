<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda Jamaah | Masjid Bukit Gading Cisoka</title>
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
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans min-h-screen flex flex-col selection:bg-primary-500 selection:text-white">

    <!-- Navbar Jamaah -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3">
                <img src="/images/logo.png" alt="Logo Masjid BGC" class="w-12 h-12 rounded-full object-cover shadow-md shadow-emerald-900/20 border border-emerald-500/30">
                <div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight block">Masjid BGC</span>
                    <span class="text-xs text-emerald-600 font-semibold -mt-1 block">Portal Jamaah</span>
                </div>
            </a>

            <!-- User Menu & Logout -->
            <div class="flex items-center space-x-4">
                <div class="hidden sm:block text-right">
                    <span class="block text-sm font-bold text-gray-900">{{ $user->name }}</span>
                    <span class="block text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
                </div>
                <form action="{{ route('keluar') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full space-y-8">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-primary-700 to-emerald-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10 max-w-2xl space-y-2">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold text-emerald-100">
                    Ahlan wa Sahlan
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Assalamu'alaikum, {{ $user->name }}!
                </h1>
                <p class="text-emerald-100 text-sm leading-relaxed">
                    Terima kasih telah menjadi bagian dari keluarga besar Jamaah Masjid Bukit Gading Cisoka. Berikut ringkasan kegiatan, transparansi kas, dan program amanah umat.
                </p>
            </div>
        </div>

        <!-- Grid Fitur Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- 1. Warta & Pengumuman Terbaru -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="p-2 bg-emerald-100 text-primary-600 rounded-xl">📢</span>
                            Warta Pengumuman
                        </h2>
                    </div>
                    @if($announcements->isEmpty())
                        <p class="text-xs text-gray-400 py-6 text-center">Belum ada pengumuman baru.</p>
                    @else
                        <ul class="divide-y divide-gray-100 space-y-3">
                            @foreach($announcements as $ann)
                                <li class="pt-2">
                                    <h3 class="text-xs font-bold text-gray-800 line-clamp-1">{{ $ann->title }}</h3>
                                    <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5">{{ $ann->content }}</p>
                                    <span class="text-[10px] text-gray-400 mt-1 block">{{ $ann->published_at ? \Carbon\Carbon::parse($ann->published_at)->isoFormat('D MMMM Y') : 'Baru saja' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- 2. Agenda & Kajian Terdekat -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="p-2 bg-emerald-100 text-primary-600 rounded-xl">📅</span>
                            Agenda & Kajian
                        </h2>
                    </div>
                    @if($events->isEmpty())
                        <p class="text-xs text-gray-400 py-6 text-center">Belum ada jadwal kajian mendatang.</p>
                    @else
                        <ul class="divide-y divide-gray-100 space-y-3">
                            @foreach($events as $ev)
                                <li class="pt-2">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-xs font-bold text-gray-800 line-clamp-1">{{ $ev->title }}</h3>
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                                            {{ $ev->capacity ? ($ev->capacity - $ev->registrations()->count()) . ' Kuota' : 'Umum' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">📍 {{ $ev->location }}</p>
                                    <span class="text-[10px] text-primary-600 font-semibold mt-1 block">
                                        🗓️ {{ \Carbon\Carbon::parse($ev->event_date)->isoFormat('dddd, D MMMM Y') }} • {{ substr($ev->event_time, 0, 5) }} WIB
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- 3. Ringkasan Kas & Jejak Amanah -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="p-2 bg-emerald-100 text-primary-600 rounded-xl">🛡️</span>
                            Transparansi Kas Masjid
                        </h2>
                    </div>
                    @if($latestSummary)
                        <div class="space-y-3">
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <span class="text-[11px] text-gray-500 block">Periode Laporan</span>
                                <span class="text-xs font-bold text-gray-800">{{ $latestSummary->period_label }}</span>
                            </div>
                            <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                                <span class="text-[11px] text-emerald-700 block font-medium">Saldo Kas Terverifikasi</span>
                                <span class="text-lg font-black text-emerald-900">
                                    Rp {{ number_format($latestSummary->opening_balance + $latestSummary->transactions()->where('type', 'income')->where('status', 'verified')->sum('amount') - $latestSummary->transactions()->where('type', 'expense')->where('status', 'verified')->sum('amount'), 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500">
                                Seluruh laporan keuangan diverifikasi bendahara dan dapat diakses secara transparan oleh jamaah.
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 py-6 text-center">Laporan kas belum dipublikasikan.</p>
                    @endif
                </div>
            </div>

        </div>

        @if(isset($mySocialApplications) && $mySocialApplications->isNotEmpty())
        <!-- Riwayat Pengajuan Bantuan Sosial Saya -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="p-2 bg-amber-100 text-amber-600 rounded-xl">🤝</span>
                Pengajuan Bantuan Sosial Saya
            </h2>
            <div class="divide-y divide-gray-100">
                @foreach($mySocialApplications as $app)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900">{{ $app->program->title ?? 'Program Bansos' }}</span>
                                @if($app->status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Review</span>
                                @elseif($app->status === 'approved')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui Pengurus</span>
                                @elseif($app->status === 'rejected')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Ditolak</span>
                                @elseif($app->status === 'distributed')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Telah Disalurkan</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600"><span class="font-semibold">Alasan:</span> {{ $app->reason }}</p>
                            @if($app->notes)
                                <p class="text-xs text-amber-800 bg-amber-50/60 p-2 rounded-lg border border-amber-100"><span class="font-bold">Catatan Pengurus:</span> {{ $app->notes }}</p>
                            @endif
                            <span class="text-[11px] text-gray-400 block">Diajukan pada: {{ $app->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="text-left sm:text-right shrink-0">
                            @if($app->requested_amount)
                                <span class="text-xs text-gray-500 block">Nominal Diajukan</span>
                                <span class="text-sm font-bold text-emerald-700">Rp {{ number_format($app->requested_amount, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Program Donasi Aktif -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="p-2 bg-rose-100 text-rose-600 rounded-xl">❤️</span>
                Program Donasi & Infaq Aktif
            </h2>
            @if($campaigns->isEmpty())
                <p class="text-xs text-gray-400 py-8 text-center">Belum ada kampanye donasi aktif saat ini.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($campaigns as $camp)
                        <div class="border border-gray-100 rounded-2xl p-5 hover:border-emerald-200 transition space-y-3">
                            <h3 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $camp->title }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2">{{ $camp->description }}</p>
                            
                            @php
                                $pct = $camp->target_amount > 0 ? min(100, round(($camp->collected_amount / $camp->target_amount) * 100)) : 100;
                            @endphp
                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-gray-600 mb-1">
                                    <span>Terkumpul: Rp {{ number_format($camp->collected_amount, 0, ',', '.') }}</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary-600 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            <div class="pt-2">
                                <a href="/download-app" class="block w-full text-center py-2 px-3 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-bold transition">
                                    Donasi via Aplikasi HP
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-gray-500 border-t border-gray-200 bg-white">
        &copy; {{ date('Y') }} DKM Masjid Bukit Gading Cisoka. Seluruh hak cipta dilindungi.
    </footer>

</body>
</html>
