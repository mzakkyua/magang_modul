@extends('layouts.admin')

@section('title', 'Kelola Divisi')

@section('content')

    {{-- ===================== HEADER ===================== --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-extrabold text-gray-800">
                Master Divisi Magang
            </h1>

            <p class="text-sm text-gray-500 mt-0.5">
                Kelola source of truth seluruh nama divisi magang.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
            class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-1 transition">
            <i class="bi bi-arrow-left text-xs"></i>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- ===================== FLASH MESSAGE ===================== --}}
    @if (session('success'))
        <div
            class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4">
            <i class="bi bi-check-circle-fill text-emerald-500 shrink-0 mt-0.5"></i>

            <p class="text-sm font-semibold">
                {{ session('success') }}
            </p>
        </div>
    @endif

    @if (session('info'))
        <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-5 py-4">
            <i class="bi bi-info-circle-fill text-blue-500 shrink-0 mt-0.5"></i>

            <p class="text-sm font-semibold">
                {{ session('info') }}
            </p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4">
            <i class="bi bi-exclamation-circle-fill text-red-500 shrink-0 mt-0.5"></i>

            <div>
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-semibold">
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===================== TABEL DIVISI ===================== --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">

                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-diagram-3 text-blue-600 text-xs"></i>
                </div>

                <h3 class="font-bold text-gray-800 text-sm">
                    Daftar Divisi
                </h3>

                <span class="ml-auto text-xs text-gray-400">
                    {{ $divisions->total() }} divisi
                </span>
            </div>

            @if ($divisions->isEmpty())

                <div class="py-16 text-center">

                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-folder-x text-gray-400 text-xl"></i>
                    </div>

                    <p class="text-gray-500 font-semibold text-sm">
                        Belum ada divisi
                    </p>

                    <p class="text-gray-400 text-xs mt-1">
                        Tambahkan divisi pertama melalui panel kanan.
                    </p>

                </div>
            @else
                <div class="divide-y divide-gray-50">

                    @foreach ($divisions as $division)
                        <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/50 transition group">

                            {{-- INFO DIVISI --}}
                            <div class="flex-1 min-w-0">

                                <div class="flex items-center gap-2">

                                    <p class="font-semibold text-sm text-gray-800">
                                        {{ $division->name }}
                                    </p>

                                    @if ($division->is_active)
                                        <span
                                            class="text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded-full">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 px-2 py-0.5 rounded-full">
                                            Nonaktif
                                        </span>
                                    @endif

                                </div>

                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $division->active_vacancies_count }} lowongan aktif
                                </p>

                            </div>

                            {{-- FORM EDIT --}}
                            <form action="{{ route('admin.divisions.update', $division) }}" method="POST"
                                class="flex items-center gap-2 shrink-0">

                                @csrf
                                @method('PUT')

                                <input type="text" name="name" value="{{ $division->name }}" maxlength="100"
                                    class="w-44 text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition">

                                <button type="submit"
                                    class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-100 px-3 py-1.5 rounded-lg transition">
                                    Simpan
                                </button>

                            </form>

                            {{-- TOGGLE --}}
                            <form action="{{ route('admin.divisions.toggle-active', $division) }}" method="POST">

                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition
                                        {{ $division->is_active
                                            ? 'text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-100'
                                            : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100' }}">
                                    {{ $division->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                            </form>

                            {{-- DELETE --}}
                            <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST"
                                onsubmit="return confirm('Hapus divisi {{ $division->name }} ?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="text-xs text-gray-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100 p-1.5 rounded-lg hover:bg-red-50">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </form>

                        </div>
                    @endforeach

                </div>

                {{-- PAGINATION --}}
                @if ($divisions->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $divisions->links() }}
                    </div>
                @endif

            @endif

        </div>

        {{-- ===================== PANEL KANAN ===================== --}}
        <div class="space-y-5">

            {{-- TAMBAH DIVISI --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">

                    <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                        <i class="bi bi-plus-circle text-emerald-600 text-xs"></i>
                    </div>

                    <h3 class="font-bold text-gray-800 text-sm">
                        Tambah Divisi
                    </h3>

                </div>

                <form action="{{ route('admin.divisions.store') }}" method="POST" class="p-5 space-y-4">

                    @csrf

                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Nama Divisi
                        </label>

                        <input type="text" name="name" value="{{ old('name') }}" maxlength="100"
                            placeholder="Contoh: Teknologi Informasi"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition">

                        <p class="text-[11px] text-gray-400 mt-1">
                            Nama divisi akan muncul di seluruh dropdown sistem.
                        </p>

                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-xl transition shadow-sm shadow-blue-200">
                        <i class="bi bi-plus-circle mr-1.5"></i>
                        Tambah Divisi
                    </button>

                </form>

            </div>

            {{-- INFO --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">

                <div class="flex items-start gap-2">

                    <i class="bi bi-lightbulb-fill text-blue-400 shrink-0 mt-0.5"></i>

                    <div>

                        <p class="text-xs font-bold text-blue-800 mb-1">
                            Tentang Master Divisi
                        </p>

                        <ul class="text-xs text-blue-700 space-y-1.5 leading-relaxed">
                            <li>· Semua dropdown divisi mengambil data dari sini</li>
                            <li>· Nama divisi tidak lagi diketik manual</li>
                            <li>· Mengurangi typo & inkonsistensi data</li>
                            <li>· Divisi nonaktif tidak muncul di form lowongan</li>
                            <li>· Perubahan nama otomatis sinkron ke seluruh sistem</li>
                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
