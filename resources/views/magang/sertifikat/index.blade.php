@extends('layouts.landing')
@section('title', 'Unduh Nilai dan Sertifikat')
@section('content')
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Judul --}}
            <div class="text-center mb-12">
                <p class="text-blue-600 font-bold uppercase tracking-widest text-sm mb-2">Unduh Sertifikat</p>
                <h1 class="text-4xl font-extrabold text-[#37517e]">Riwayat</h1>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#2d3748] text-white">
                            <th class="px-6 py-4 font-bold uppercase text-xs">No.</th>
                            <th class="px-6 py-4 font-bold uppercase text-xs">Posisi Magang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($certificates as $cert)
                            <tr class="hover:bg-gray-50">

                                <!-- NO -->
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>

                                <!-- JUDUL -->
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">
                                        {{ $cert->title }}
                                    </div>

                                    <!-- PREVIEW -->
                                    <div
                                        class="mt-3 w-full h-64 bg-gray-100 border border-gray-200 rounded-lg overflow-hidden flex items-center justify-center relative">
                                        @php
                                            // Ambil ekstensi file (pdf, jpg, png, dll)
                                            $extension = strtolower(pathinfo($cert->file, PATHINFO_EXTENSION));
                                        @endphp

                                        @if (in_array($extension, ['jpg', 'jpeg', 'png']))
                                            {{-- Jika Gambar: Gunakan tag img dengan object-contain agar pas di tengah tanpa terpotong --}}
                                            <img src="{{ route('certificate.view', $cert->id) }}" alt="{{ $cert->title }}"
                                                class="w-full h-full object-contain">
                                        @else
                                            {{-- Jika PDF: Gunakan iframe --}}
                                            <iframe src="{{ route('certificate.view', $cert->id) }}"
                                                class="w-full h-full border-0">
                                            </iframe>
                                        @endif
                                    </div>
                                </td>

                                <!-- AKSI -->
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('certificate.download', $cert->id) }}"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        Download
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-6 text-gray-500">
                                    Belum ada sertifikat
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
