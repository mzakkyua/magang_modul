@extends('layouts.landing')

@section('title', 'Unduh Nilai dan Sertifikat')

@section('content')
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ===================== PAGE HEADER ===================== --}}
            <div class="mb-10">
                <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-2 flex items-center gap-2">
                    <i class="bi bi-award"></i> Sertifikat
                </p>
                <h1 class="text-3xl font-extrabold text-gray-900">Sertifikat & Unduhan</h1>
                <p class="text-gray-500 text-sm mt-1">Unduh sertifikat keikutsertaan magang kamu di sini.</p>
            </div>

            {{-- ===================== KONTEN SERTIFIKAT ===================== --}}
            @forelse($certificates as $cert)
                @php
                    // STEP: Tentukan tipe file untuk preview
                    $extension = strtolower(pathinfo($cert->file, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

                    {{-- Card Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            {{-- Nomor urut --}}
                            <div
                                class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                <span class="text-blue-600 text-xs font-extrabold">{{ $loop->iteration }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $cert->title }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5 uppercase tracking-wide">
                                    {{ strtoupper($extension) }} • Sertifikat Magang
                                </p>
                            </div>
                        </div>

                        {{-- Tombol Download --}}
                        <a href="{{ route('certificates.download', $cert->id) }}"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm shadow-blue-600/20 shrink-0">
                            <i class="bi bi-download text-xs"></i>
                            Unduh
                        </a>
                    </div>

                    {{-- Preview Area --}}
                    <div class="p-5">
                        <div class="w-full bg-gray-50 border border-gray-100 rounded-xl overflow-hidden relative"
                            style="height: 480px;">

                            @if ($isImage)
                                {{-- Preview Gambar --}}
                                <img src="{{ route('certificates.view', $cert->id) }}" alt="{{ $cert->title }}"
                                    class="w-full h-full object-contain">
                            @else
                                {{-- Preview PDF via iframe --}}
                                <iframe src="{{ route('certificates.view', $cert->id) }}" class="w-full h-full border-0">
                                </iframe>
                            @endif

                            {{-- Badge tipe file di pojok --}}
                            <div class="absolute top-3 right-3">
                                <span
                                    class="inline-flex items-center gap-1 bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                    <i
                                        class="bi bi-{{ $isImage ? 'image' : 'file-earmark-pdf' }} text-[9px]
                                    {{ $isImage ? 'text-green-500' : 'text-red-500' }}"></i>
                                    {{ strtoupper($extension) }}
                                </span>
                            </div>

                        </div>

                        {{-- Footer card: link buka di tab baru --}}
                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xs text-gray-400">
                                <i class="bi bi-info-circle mr-1"></i>
                                Klik unduh untuk menyimpan sertifikat ke perangkat kamu.
                            </p>
                            <a href="{{ route('certificates.view', $cert->id) }}" target="_blank"
                                class="text-xs text-blue-500 hover:text-blue-700 font-semibold flex items-center gap-1 transition">
                                <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                Buka di tab baru
                            </a>
                        </div>
                    </div>

                </div>

            @empty

                {{-- ===================== EMPTY STATE ===================== --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <i class="bi bi-award text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-gray-600 font-bold text-lg mb-1">Belum Ada Sertifikat</h3>
                    <p class="text-gray-400 text-sm max-w-sm mx-auto leading-relaxed">
                        Sertifikat akan tersedia setelah masa magang kamu selesai dan admin telah menerbitkannya.
                    </p>
                    <a href="{{ route('dashboard.index') }}"
                        class="inline-flex items-center gap-2 mt-6 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-600/20">
                        <i class="bi bi-house text-xs"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            @endforelse

        </div>
    </div>
@endsection
