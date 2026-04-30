<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancy->title }} - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pages/vacancy-detail.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* =============================================
           DESCRIPTION RENDERER — Smart Text Formatting
           ============================================= */

        .desc-body {
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* Heading / Judul Section */
        .desc-section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-bottom: 8px;
            border-bottom: 2px solid #f3f4f6;
        }

        .desc-section-header:first-child {
            margin-top: 0;
        }

        .desc-section-header .dsh-icon {
            width: 24px;
            height: 24px;
            background: #eff6ff;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .desc-section-header .dsh-icon i {
            font-size: 12px;
            color: #3b82f6;
        }

        /* List items */
        .desc-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .desc-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .desc-list li::before {
            content: '\F26A';
            /* bootstrap icon check */
            font-family: 'bootstrap-icons';
            color: #3b82f6;
            font-size: 14px;
            line-height: 1.5;
            flex-shrink: 0;
        }

        /* Numbered list */
        .desc-numlist {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            counter-reset: desc-counter;
        }

        .desc-numlist li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            counter-increment: desc-counter;
        }

        .desc-numlist li::before {
            content: counter(desc-counter) ".";
            color: #3b82f6;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Paragraph */
        .desc-para {
            margin-bottom: 1rem;
        }

        /* Quote / highlight block */
        .desc-quote {
            border-left: 4px solid #3b82f6;
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 1rem;
            color: #475569;
            font-style: italic;
        }

        /* Divider */
        .desc-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 1.5rem 0;
        }

        /* Empty placeholder */
        .desc-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            text-align: center;
            color: #9ca3af;
            gap: 12px;
        }

        .desc-empty i {
            font-size: 2.5rem;
            color: #d1d5db;
        }

        /* =============================================
           INFO CARD STYLES
           ============================================= */

        .info-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f2937;
        }

        /* Progress bar */
        .quota-bar-wrap {
            background: #f3f4f6;
            border-radius: 100px;
            height: 6px;
            overflow: hidden;
            margin-top: 8px;
        }

        .quota-bar-fill {
            height: 100%;
            border-radius: 100px;
            transition: width 0.8s ease;
        }
    </style>
</head>

<body class="bg-slate-50 text-gray-800">

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
            @if (Auth::guard('web')->check())
                <a href="{{ route('landing.index') }}"
                    class="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 transition font-semibold group">
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                        <i class="bi bi-arrow-left text-sm"></i>
                    </span>
                    Kembali
                </a>
            @elseif(Auth::guard('magang')->check())
                <a href="{{ route('dashboard.index') }}"
                    class="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 transition font-semibold group">
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                        <i class="bi bi-arrow-left text-sm"></i>
                    </span>
                    Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('landing.index') }}"
                    class="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 transition font-semibold group">
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                        <i class="bi bi-arrow-left text-sm"></i>
                    </span>
                    Kembali ke Daftar
                </a>
            @endif

            <span class="font-extrabold text-blue-600 text-xl tracking-tight">SINAKERTRANS</span>
        </div>
    </nav>


    {{-- ===================== HERO ===================== --}}
    @php
        $isTypeMagang = $vacancy->type === 'magang';
        $heroBg = $isTypeMagang
            ? 'from-blue-900 via-blue-800 to-blue-700'
            : 'from-violet-900 via-purple-800 to-violet-700';
        $badgeBg = $isTypeMagang
            ? 'bg-blue-500/20 text-blue-200 border-blue-400/30'
            : 'bg-purple-500/20 text-purple-200 border-purple-400/30';
        $accentColor = $isTypeMagang ? '#3b82f6' : '#8b5cf6';

        $filled = $vacancy->quota_slots - $vacancy->getSisaKuota();
        $pct = $vacancy->quota_slots > 0 ? ($filled / $vacancy->quota_slots) * 100 : 0;
        $barColor = $pct >= 80 ? '#ef4444' : ($pct >= 50 ? '#f59e0b' : '#3b82f6');
    @endphp

    <section class="bg-linear-to-br {{ $heroBg }} py-10 relative overflow-hidden">
        {{-- Decorative dots --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image:radial-gradient(circle, #fff 1px, transparent 1px); background-size:24px 24px;">
        </div>

        <div class="max-w-5xl mx-auto px-6 relative z-10">
            <span
                class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-full border {{ $badgeBg }} mb-4 uppercase tracking-widest">
                <i class="bi bi-{{ $isTypeMagang ? 'briefcase-fill' : 'journal-text' }}"></i>
                {{ ucfirst($vacancy->type) }}
            </span>

            <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight mb-4">
                {{ $vacancy->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-blue-100">
                <span class="flex items-center gap-2">
                    <i class="bi bi-building opacity-70"></i>
                    {{ $vacancy->division_name }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="bi bi-calendar3 opacity-70"></i>
                    {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M') }}
                    &mdash;
                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="bi bi-people opacity-70"></i>
                    {{ $vacancy->getSisaKuota() }} slot tersisa
                </span>
            </div>
        </div>
    </section>


    {{-- ===================== MAIN ===================== --}}
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Grid responsif: 1 kolom di mobile, 3 kolom di desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ========== KOLOM KIRI: DESKRIPSI ========== --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Card Header --}}
                    <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4 bg-gray-50/50">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center bg-white shadow-sm border border-gray-100">
                            <i class="bi bi-file-earmark-text text-lg" style="color:{{ $accentColor }}"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-gray-800 leading-none">Deskripsi & Persyaratan</h2>
                            <p class="text-xs text-gray-500 mt-1">Informasi lengkap terkait posisi ini</p>
                        </div>
                    </div>

                    {{-- Description Body --}}
                    <div class="p-6 md:p-8 desc-body">
                        @if ($vacancy->description)
                            {!! \App\Helpers\DescriptionFormatter::render($vacancy->description) !!}
                        @else
                            <div class="desc-empty">
                                <i class="bi bi-file-earmark-x"></i>
                                <div>
                                    <p class="font-bold text-gray-500 text-base">Belum ada deskripsi</p>
                                    <p class="text-gray-400 text-sm mt-1">Admin belum menambahkan detail untuk lowongan
                                        ini.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Admin Notice --}}
                @if (Auth::guard('web')->check())
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                            <i class="bi bi-shield-exclamation text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-amber-800 text-sm">Mode Pratinjau Admin</p>
                            <p class="text-amber-700 text-xs mt-0.5">Anda sedang login sebagai Admin. Tombol lamaran
                                tidak tersedia.</p>
                        </div>
                    </div>
                @endif

                {{-- Guest CTA --}}
                @if (!Auth::guard('web')->check() && !Auth::guard('magang')->check())
                    <div
                        class="bg-linear-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-center shadow-lg shadow-blue-600/20">
                        <div class="w-12 h-12 bg-white/15 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-person-lock text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-white text-lg mb-2">Tertarik dengan posisi ini?</h3>
                        <p class="text-blue-100 text-sm mb-6">Login atau daftar akun untuk mulai mengirimkan lamaran
                            Anda.</p>
                        <div class="flex justify-center gap-3 flex-wrap">
                            <a href="{{ route('login') }}"
                                class="px-6 py-2.5 border border-white/40 text-white font-semibold text-sm rounded-xl hover:bg-white/10 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-6 py-2.5 bg-white text-blue-700 font-bold text-sm rounded-xl hover:bg-blue-50 transition shadow-md">
                                Daftar Sekarang
                            </a>
                        </div>
                    </div>
                @endif

            </div>


            {{-- ========== KOLOM KANAN: SIDEBAR INFO ========== --}}
            {{-- Wrapper ini sekarang akan otomatis turun ke bawah di mode mobile, dan di kanan pas desktop --}}
            <div class="space-y-6">

                {{-- INCLUDE PARTIAL INFO CARD --}}
                @include('partials.vacancy-info-card', [
                    'vacancy' => $vacancy,
                    'accentColor' => $accentColor,
                    'isTypeMagang' => $isTypeMagang,
                    'pct' => $pct,
                    'barColor' => $barColor,
                ])

                {{-- Tombol Lamar --}}
                @if (Auth::guard('magang')->check())
                    @if ($vacancy->getSisaKuota() > 0)
                        <button type="button" data-modal-target="applicationModal" data-modal-toggle="applicationModal"
                            class="w-full font-bold py-4 px-6 rounded-2xl shadow-lg transition-all duration-200 hover:-translate-y-1 flex items-center justify-center gap-2 text-sm text-white"
                            style="background: linear-gradient(135deg, {{ $accentColor }}, {{ $isTypeMagang ? '#1d4ed8' : '#7c3aed' }}); box-shadow: 0 8px 24px {{ $isTypeMagang ? 'rgba(59,130,246,.3)' : 'rgba(139,92,246,.3)' }}">
                            <i class="bi bi-send-fill text-base"></i>
                            Kirim Lamaran Sekarang
                        </button>
                    @else
                        <div
                            class="w-full bg-gray-100 border border-gray-200 text-gray-500 font-bold py-4 px-6 rounded-2xl text-center text-sm flex items-center justify-center gap-2">
                            <i class="bi bi-slash-circle text-base"></i> Kuota Sudah Penuh
                        </div>
                    @endif
                @endif

                {{-- Share card --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Bagikan Lowongan</p>
                    <button onclick="copyLink()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition text-sm text-gray-600 font-semibold group border border-gray-200/60">
                        <i
                            class="bi bi-link-45deg text-gray-400 group-hover:text-blue-500 transition text-lg leading-none"></i>
                        <span id="copy-label">Salin Tautan</span>
                    </button>
                </div>

            </div>

        </div>
    </div>

    {{-- ===================== MODAL LAMARAN ===================== --}}
    @include('partials.vacancy-modal', [
        'vacancy' => $vacancy,
        'accentColor' => $accentColor,
        'isTypeMagang' => $isTypeMagang,
    ])


    {{-- ===================== SCRIPTS ===================== --}}
    <script>
        window.vacancyPage = {
            id: {{ $vacancy->id }},
            updatedAt: "{{ $vacancy->updated_at?->toDateTimeString() }}",
            maxMembers: {{ $vacancy->max_members ?? 1 }},
            snapshotUrl: "{{ route('vacancies.snapshot', $vacancy->id) }}",
            dashboardUrl: "{{ route('dashboard.index') }}",
            successApply: @json(session('success_apply')),
            successMessage: @json(session('success')),
            errorMessage: @json(session('error')),
        };

        /* Copy link */
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                var label = document.getElementById('copy-label');
                if (label) {
                    label.textContent = 'Tautan Tersalin ✓';
                    setTimeout(function() {
                        label.textContent = 'Salin Tautan';
                    }, 2000);
                }
            });
        }
    </script>
</body>

</html>
