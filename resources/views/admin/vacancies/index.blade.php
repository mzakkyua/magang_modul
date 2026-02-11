@extends('layouts.admin')
{{-- 
    Layout utama admin.
    Semua halaman admin akan mewarisi header, sidebar, dan footer dari layout ini.
--}}

@section('title', 'Manajemen Lowongan Magang')
{{-- 
    Judul halaman.
    Biasanya dipakai di <title> browser dan header halaman.
--}}

@section('content')
    {{-- 
    =========================
    KONTEN UTAMA HALAMAN
    =========================
--}}

    {{-- =========================
    HEADER HALAMAN + ACTION
    ========================= --}}
    <div class="flex justify-between items-center mb-6">
        {{-- Deskripsi singkat halaman --}}
        <p class="text-gray-600">
            Kelola daftar lowongan magang yang tersedia.
            <span class="text-sm text-gray-400">(Buka / Tutup / Edit sesuai kondisi)</span>
        </p>

        {{-- Tombol menuju halaman CREATE lowongan --}}
        <a href="{{ route('admin.vacancies.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow flex items-center">
            <i class="bi bi-plus-lg mr-2"></i> Buat Lowongan Baru
        </a>
    </div>

    {{-- =========================
    TABEL DATA LOWONGAN
    ========================= --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            {{-- Header kolom tabel --}}
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-500">Judul & Divisi</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-500">Tipe & Mode</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-500">Kuota</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-500">Periode</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-500">Status</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>

            {{-- Body tabel --}}
            <tbody class="divide-y divide-gray-100">
                {{-- Loop data lowongan --}}
                @forelse ($vacancies as $vacancy)
                    <tr class="hover:bg-gray-50">

                        {{-- =========================
                        KOLOM JUDUL & DIVISI
                        ========================= --}}
                        <td class="px-6 py-4">
                            {{-- Judul lowongan --}}
                            <div class="font-semibold text-gray-900">
                                {{ $vacancy->title }}
                            </div>

                            {{-- Badge divisi & indikator pendaftar --}}
                            <div class="mt-1 flex gap-2 text-xs">
                                <span class="bg-gray-100 px-2 py-0.5 rounded">
                                    {{ $vacancy->division_name }}
                                </span>

                                {{-- Indikator jika sudah ada pendaftar --}}
                                @if ($vacancy->applications_count > 0)
                                    <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded">
                                        Ada Pendaftar
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- =========================
                        KOLOM TIPE & MODE
                        ========================= --}}
                        <td class="px-6 py-4">
                            <div class="capitalize font-medium">
                                {{ $vacancy->type }}
                            </div>
                            <div class="text-xs text-gray-500 capitalize">
                                Mode: {{ $vacancy->registration_mode }}
                            </div>
                        </td>

                        {{-- =========================
                        KOLOM KUOTA
                        ========================= --}}
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                {{ $vacancy->quota_slots }} Slot
                            </span>
                        </td>

                        {{-- =========================
                        KOLOM PERIODE MAGANG
                        ========================= --}}
                        <td class="px-6 py-4 text-center text-gray-600">
                            {{ $vacancy->start_date->format('d M Y') }}
                            <div class="text-xs text-gray-400">s/d</div>
                            {{ $vacancy->end_date->format('d M Y') }}
                        </td>

                        {{-- =========================
                        KOLOM STATUS LOWONGAN
                        ========================= --}}
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2 py-0.5 text-xs font-semibold rounded-full
                                {{ $vacancy->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ strtoupper($vacancy->status) }}
                            </span>
                        </td>

                        {{-- =========================
                        KOLOM AKSI (EDIT / TOGGLE / DELETE)
                        ========================= --}}
                        <td class="px-6 py-4 text-center space-x-3">

                            {{-- Aksi EDIT --}}
                            <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="text-indigo-600"
                                title="Edit Lowongan">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            {{-- Aksi TOGGLE STATUS (open / closed) --}}
                            <form action="{{ route('admin.vacancies.toggle', $vacancy) }}" method="POST"
                                class="inline-block form-toggle" data-title="{{ $vacancy->title }}"
                                data-status="{{ $vacancy->status }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit" title="Buka / Tutup Lowongan">
                                    <i
                                        class="bi {{ $vacancy->status === 'open' ? 'bi-toggle-on text-green-600' : 'bi-toggle-off text-gray-400' }} text-xl"></i>
                                </button>
                            </form>

                            {{-- Aksi DELETE (hanya jika belum ada pendaftar) --}}
                            @if ($vacancy->applications_count === 0)
                                <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST"
                                    class="inline-block form-delete" data-name="{{ $vacancy->title }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus Permanen">
                                        <i class="bi bi-trash text-lg"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Icon disabled jika tidak boleh dihapus --}}
                                <i class="bi bi-trash text-gray-300 cursor-not-allowed"
                                    title="Tidak dapat dihapus karena sudah ada pendaftar"></i>
                            @endif
                        </td>
                    </tr>

                    {{-- Kondisi jika tidak ada data --}}
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            Belum ada lowongan dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- =========================
        PAGINATION
        ========================= --}}
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $vacancies->links() }}
        </div>
    </div>

@endsection
