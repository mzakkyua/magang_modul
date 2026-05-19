@extends('layouts.landing')

@section('title', 'Sertifikat')

@section('content')
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">

            {{-- ===================== HEADER ===================== --}}
            <div class="mb-8">
                <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-2 flex items-center gap-2">
                    <i class="bi bi-award"></i> Sertifikat
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Sertifikat Magang</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Setiap periode magang memiliki sertifikat tersendiri.
                </p>
            </div>


            {{-- ===================== LIST PER PERIODE ===================== --}}
            @forelse ($memberRecords as $member)
                @php
                    $vacancy = $member->application->vacancy;
                    $cert = $member->certificate;
                    $app = $member->application;

                    $isTypeMagang = $vacancy->type === 'magang';
                    $accentBg = $isTypeMagang ? 'from-blue-600 to-blue-700' : 'from-violet-600 to-violet-700';

                    $statusLabel = match ($app->status) {
                        'accepted' => [
                            'label' => 'Sedang Berjalan',
                            'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        ],
                        'completed' => ['label' => 'Selesai', 'color' => 'bg-blue-100 text-blue-700 border-blue-200'],
                        'resigned' => [
                            'label' => 'Mengundurkan Diri',
                            'color' => 'bg-red-100 text-red-700 border-red-200',
                        ],
                        default => [
                            'label' => ucfirst($app->status),
                            'color' => 'bg-gray-100 text-gray-600 border-gray-200',
                        ],
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

                    {{-- Header periode --}}
                    <div class="bg-linear-to-r {{ $accentBg }} px-6 py-4 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10"
                            style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;">
                        </div>
                        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest mb-0.5">
                                    {{ ucfirst($vacancy->type) }} • Periode {{ $loop->iteration }}
                                </p>
                                <h2 class="text-sm font-extrabold text-white leading-tight">{{ $vacancy->title }}</h2>
                                <p class="text-xs text-white/75 mt-0.5">{{ $vacancy->division_name }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $statusLabel['color'] }} bg-white/90">
                                    {{ $statusLabel['label'] }}
                                </span>
                                <p class="text-white/60 text-[11px] mt-1.5">
                                    {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M Y') }}
                                    –
                                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-5">
                        @if ($cert)
                            @php
                                $ext = strtolower(pathinfo($cert->file ?? '', PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">

                                {{-- Preview --}}
                                <div class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50 relative"
                                    style="height:260px;">
                                    @if ($isImage)
                                        <img src="{{ $cert->view_url }}" alt="{{ $cert->title }}"
                                            class="w-full h-full object-contain">
                                    @else
                                        <iframe src="{{ $cert->view_url }}" class="w-full h-full border-0"></iframe>
                                    @endif
                                    <div class="absolute top-2.5 right-2.5">
                                        <span
                                            class="inline-flex items-center gap-1 bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                            <i
                                                class="bi bi-{{ $isImage ? 'image' : 'file-earmark-pdf' }} text-[9px] {{ $isImage ? 'text-green-500' : 'text-red-500' }}"></i>
                                            {{ strtoupper($ext) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Info & tombol --}}
                                <div class="flex flex-col justify-between h-full gap-4">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Judul
                                            Sertifikat</p>
                                        <p class="text-base font-extrabold text-gray-800">{{ $cert->title }}</p>

                                        @if ($cert->uploaded_at)
                                            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
                                                <i class="bi bi-calendar3"></i>
                                                Diterbitkan {{ $cert->uploaded_at->format('d F Y') }}
                                            </p>
                                        @endif

                                        @if ($cert->is_replaced)
                                            <span
                                                class="inline-flex items-center gap-1 mt-2 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                                                <i class="bi bi-arrow-repeat text-[10px]"></i>
                                                Diperbarui pada {{ $cert->replaced_at->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <a href="{{ $cert->download_url }}"
                                            class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-md shadow-blue-600/20">
                                            <i class="bi bi-download text-xs"></i> Unduh Sertifikat
                                        </a>
                                        <a href="{{ $cert->view_url }}" target="_blank"
                                            class="w-full inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold px-4 py-2.5 rounded-xl transition">
                                            <i class="bi bi-box-arrow-up-right text-xs"></i> Buka di Tab Baru
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="bi bi-award text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-400">Sertifikat Belum Diterbitkan</p>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs leading-relaxed">
                                    @if (in_array($app->status, ['accepted', 'completed']))
                                        Admin akan menerbitkan sertifikat setelah verifikasi selesai.
                                    @else
                                        Sertifikat tidak diterbitkan untuk periode ini.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                </div>

            @empty

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <i class="bi bi-award text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-gray-600 font-bold text-lg mb-1">Belum Ada Sertifikat</h3>
                    <p class="text-gray-400 text-sm max-w-sm mx-auto leading-relaxed">
                        Sertifikat akan tersedia setelah masa magang kamu selesai dan admin telah menerbitkannya.
                    </p>
                    <a href="{{ route('dashboard.index') }}"
                        class="inline-flex items-center gap-2 mt-6 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-600/20">
                        <i class="bi bi-house text-xs"></i> Kembali ke Dashboard
                    </a>
                </div>

            @endforelse

        </div>
    </div>
@endsection
