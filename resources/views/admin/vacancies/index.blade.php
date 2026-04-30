@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

@push('header_actions')
    <a href="{{ route('admin.vacancies.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
               text-white text-sm font-bold px-4 py-2 rounded-xl
               shadow-md shadow-blue-600/25 hover:shadow-blue-600/40
               transition-all duration-200 hover:-translate-y-0.5">
        <i class="bi bi-plus-lg"></i> Buat Lowongan Baru
    </a>
@endpush

@section('content')

    {{-- ========================= STAT CARDS ========================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <i class="bi bi-briefcase text-blue-500 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900 leading-none">{{ $totalVacancies ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Total Lowongan</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-emerald-500 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-emerald-600 leading-none">{{ $openCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Sedang Buka</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <i class="bi bi-x-circle text-red-400 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-red-500 leading-none">{{ $closedCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Ditutup</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <i class="bi bi-archive text-slate-400 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-slate-400 leading-none">{{ $archivedCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Diarsipkan</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-amber-500 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-amber-500 leading-none">{{ $withApplicantsCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Ada Pendaftar</p>
            </div>
        </div>

    </div>

    {{-- ========================= TABEL ========================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Header + Tab Filter --}}
        <div class="px-5 pt-4 border-b border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <span class="text-sm font-extrabold text-gray-900">Daftar Lowongan</span>
                    <span
                        class="text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-0.5 rounded-full">
                        {{ $vacancies->total() }} lowongan
                    </span>
                </div>
                <p class="text-xs text-gray-400 hidden sm:block">Buka / tutup / arsipkan sesuai kondisi</p>
            </div>

            {{-- TAB FILTER --}}
            <div class="flex gap-1">
                <a href="{{ route('admin.vacancies.index', ['tab' => 'active']) }}"
                    class="px-4 py-2 text-[12px] font-bold rounded-t-lg border-b-2 transition-colors
                        {{ $activeTab === 'active'
                            ? 'border-blue-500 text-blue-600 bg-blue-50/50'
                            : 'border-transparent text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                    <i class="bi bi-lightning-charge mr-1"></i>
                    Aktif
                    <span
                        class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full
                        {{ $activeTab === 'active' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        {{ ($openCount ?? 0) + ($closedCount ?? 0) }}
                    </span>
                </a>

                <a href="{{ route('admin.vacancies.index', ['tab' => 'all']) }}"
                    class="px-4 py-2 text-[12px] font-bold rounded-t-lg border-b-2 transition-colors
                        {{ $activeTab === 'all'
                            ? 'border-blue-500 text-blue-600 bg-blue-50/50'
                            : 'border-transparent text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                    <i class="bi bi-list-ul mr-1"></i>
                    Semua
                    <span
                        class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full
                        {{ $activeTab === 'all' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        {{ $totalVacancies ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('admin.vacancies.index', ['tab' => 'archived']) }}"
                    class="px-4 py-2 text-[12px] font-bold rounded-t-lg border-b-2 transition-colors
                        {{ $activeTab === 'archived'
                            ? 'border-slate-400 text-slate-500 bg-slate-50/50'
                            : 'border-transparent text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                    <i class="bi bi-archive mr-1"></i>
                    Arsip
                    <span
                        class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full
                        {{ $activeTab === 'archived' ? 'bg-slate-100 text-slate-500' : 'bg-gray-100 text-gray-400' }}">
                        {{ $archivedCount ?? 0 }}
                    </span>
                </a>
            </div>
        </div>

        {{-- Banner info tab arsip --}}
        @if ($activeTab === 'archived')
            <div class="mx-5 mt-4 mb-1 flex items-start gap-2.5 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                <i class="bi bi-info-circle text-slate-400 mt-0.5 shrink-0"></i>
                <p class="text-[12px] text-slate-500 leading-relaxed">
                    Lowongan yang diarsipkan <strong>tidak tampil di halaman publik</strong> dan
                    <strong>tidak dapat dibuka kembali</strong>. Data lamaran peserta tetap tersimpan dan dapat dilihat di
                    menu Verifikasi.
                </p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Lowongan</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tipe &
                            Mode</th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kuota
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Periode</th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse ($vacancies as $vacancy)
                        <tr
                            class="transition-colors duration-150 group
                            {{ $vacancy->status === 'archived' ? 'bg-slate-50/60 hover:bg-slate-100/60' : 'hover:bg-slate-50/70' }}">

                            {{-- KOLOM LOWONGAN --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-1 h-10 rounded-full shrink-0
                                        {{ $vacancy->status === 'archived'
                                            ? 'bg-slate-300'
                                            : ($vacancy->type === 'penelitian'
                                                ? 'bg-violet-400'
                                                : 'bg-blue-500') }}">
                                    </div>
                                    <div>
                                        <p
                                            class="font-bold text-[13.5px] leading-snug transition-colors duration-150
                                            {{ $vacancy->status === 'archived' ? 'text-gray-400' : 'text-gray-900 group-hover:text-blue-600' }}">
                                            {{ $vacancy->title }}
                                        </p>
                                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                                            <span
                                                class="inline-flex items-center gap-1 text-[10.5px] font-semibold
                                                         bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                                <i class="bi bi-building text-[9px]"></i>
                                                {{ $vacancy->division_name }}
                                            </span>
                                            @if ($vacancy->applications_count > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[10.5px] font-semibold
                                                             bg-amber-50 text-amber-600 border border-amber-200
                                                             px-2 py-0.5 rounded-full">
                                                    <i class="bi bi-people-fill text-[9px]"></i>
                                                    {{ $vacancy->applications_count }} Pendaftar
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM TIPE & MODE --}}
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[11.5px] font-bold capitalize px-2.5 py-1 rounded-lg
                                             {{ $vacancy->status === 'archived'
                                                 ? 'text-gray-400 bg-gray-100 border border-gray-200'
                                                 : ($vacancy->type === 'penelitian'
                                                     ? 'text-violet-600 bg-violet-50 border border-violet-100'
                                                     : 'text-blue-600 bg-blue-50 border border-blue-100') }}">
                                    <i
                                        class="bi {{ $vacancy->type === 'penelitian' ? 'bi-journal-text' : 'bi-person-workspace' }} text-[10px]"></i>
                                    {{ $vacancy->type }}
                                </span>
                                <p class="text-[11px] text-gray-400 capitalize mt-1.5">
                                    <i class="bi bi-people text-[9px] mr-0.5"></i>
                                    {{ $vacancy->registration_mode }}
                                </p>
                            </td>

                            {{-- KOLOM KUOTA --}}
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex items-center gap-1 text-[11.5px] font-bold px-3 py-1 rounded-full
                                             {{ $vacancy->status === 'archived'
                                                 ? 'bg-gray-100 text-gray-400 border border-gray-200'
                                                 : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                    <i class="bi bi-people text-[10px]"></i>
                                    {{ $vacancy->quota_slots }} Slot
                                </span>
                            </td>

                            {{-- KOLOM PERIODE --}}
                            <td class="px-5 py-4 text-center">
                                <p
                                    class="text-[12px] font-semibold {{ $vacancy->status === 'archived' ? 'text-gray-400' : 'text-gray-700' }}">
                                    {{ $vacancy->start_date->format('d M Y') }}
                                </p>
                                <p class="text-[10px] text-gray-300 my-0.5">s/d</p>
                                <p
                                    class="text-[12px] font-semibold {{ $vacancy->status === 'archived' ? 'text-gray-400' : 'text-gray-700' }}">
                                    {{ $vacancy->end_date->format('d M Y') }}
                                </p>
                            </td>

                            {{-- KOLOM STATUS --}}
                            <td class="px-5 py-4 text-center">
                                @if ($vacancy->status === 'open')
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[11px] font-extrabold
                                                 bg-emerald-50 text-emerald-600 border border-emerald-200 px-2.5 py-1 rounded-full">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                                        OPEN
                                    </span>
                                @elseif ($vacancy->status === 'closed')
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[11px] font-extrabold
                                                 bg-gray-100 text-gray-400 border border-gray-200 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                        CLOSED
                                    </span>
                                @elseif ($vacancy->status === 'archived')
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[11px] font-extrabold
                                                 bg-slate-100 text-slate-400 border border-slate-200 px-2.5 py-1 rounded-full">
                                        <i class="bi bi-archive text-[9px]"></i>
                                        ARSIP
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- EDIT --}}
                                    @if ($vacancy->status !== 'archived')
                                        <a href="{{ route('admin.vacancies.edit', $vacancy) }}" title="Edit Lowongan"
                                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white
                                                   flex items-center justify-center text-gray-400
                                                   hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600
                                                   transition-all duration-150">
                                            <i class="bi bi-pencil-square text-[13px]"></i>
                                        </a>
                                    @else
                                        <div title="Lowongan diarsipkan — tidak dapat diedit"
                                            class="w-8 h-8 rounded-lg border border-gray-100 bg-gray-50
                                                   flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-pencil-square text-[13px]"></i>
                                        </div>
                                    @endif

                                    {{-- TOGGLE --}}
                                    @if ($vacancy->status !== 'archived')
                                        <form action="{{ route('admin.vacancies.toggle', $vacancy) }}" method="POST"
                                            class="inline-flex form-toggle" data-title="{{ $vacancy->title }}"
                                            data-status="{{ $vacancy->status }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Buka / Tutup Lowongan"
                                                class="w-8 h-8 rounded-lg border flex items-center justify-center
                                                       transition-all duration-150
                                                       {{ $vacancy->status === 'open'
                                                           ? 'border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100'
                                                           : 'border-gray-200 bg-white text-gray-400 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-500' }}">
                                                <i
                                                    class="bi {{ $vacancy->status === 'open' ? 'bi-toggle-on' : 'bi-toggle-off' }} text-lg leading-none"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div title="Status sudah final — tidak dapat diubah"
                                            class="w-8 h-8 rounded-lg border border-gray-100 bg-gray-50
                                                   flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-lock text-[13px]"></i>
                                        </div>
                                    @endif

                                    {{-- 
                                        ARSIPKAN — amber, hanya muncul saat status = closed
                                        Klik membuka modal konfirmasi (bukan browser confirm)
                                    --}}
                                    @if ($vacancy->status === 'closed')
                                        <button type="button" title="Arsipkan Lowongan"
                                            onclick="openArchiveModal('{{ $vacancy->id }}', '{{ addslashes($vacancy->title) }}')"
                                            class="w-8 h-8 rounded-lg border border-amber-200 bg-amber-50
                                                   flex items-center justify-center text-amber-500
                                                   hover:border-amber-300 hover:bg-amber-100 hover:text-amber-600
                                                   transition-all duration-150">
                                            <i class="bi bi-archive text-[13px]"></i>
                                        </button>
                                    @else
                                        <div class="w-8 h-8"></div>
                                    @endif

                                    {{-- DELETE --}}
                                    @if ($vacancy->status !== 'archived' && $vacancy->applications_count === 0)
                                        <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST"
                                            class="inline-flex form-delete" data-name="{{ $vacancy->title }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Permanen"
                                                class="w-8 h-8 rounded-lg border border-gray-200 bg-white
                                                       flex items-center justify-center text-gray-400
                                                       hover:border-red-200 hover:bg-red-50 hover:text-red-500
                                                       transition-all duration-150">
                                                <i class="bi bi-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div title="{{ $vacancy->status === 'archived' ? 'Lowongan diarsipkan — tidak dapat dihapus' : 'Tidak dapat dihapus — sudah ada pendaftar' }}"
                                            class="w-8 h-8 rounded-lg border border-gray-100 bg-gray-50
                                                   flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-trash text-[13px]"></i>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div
                                        class="w-14 h-14 rounded-2xl {{ $activeTab === 'archived' ? 'bg-slate-100' : 'bg-gray-100' }} flex items-center justify-center">
                                        <i
                                            class="bi {{ $activeTab === 'archived' ? 'bi-archive text-slate-400' : 'bi-briefcase text-gray-400' }} text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">
                                            {{ $activeTab === 'archived' ? 'Belum Ada Lowongan yang Diarsipkan' : 'Belum Ada Lowongan' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $activeTab === 'archived' ? 'Lowongan yang diarsipkan akan muncul di sini.' : 'Buat lowongan pertama untuk mulai menerima pendaftar.' }}
                                        </p>
                                    </div>
                                    @if ($activeTab !== 'archived')
                                        <a href="{{ route('admin.vacancies.create') }}"
                                            class="inline-flex items-center gap-2 bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-blue-700 transition-colors mt-1">
                                            <i class="bi bi-plus-lg"></i> Buat Lowongan Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($vacancies->hasPages())
            <div class="px-5 py-4 border-t border-gray-50 bg-gray-50/50">
                {{ $vacancies->links() }}
            </div>
        @endif

    </div>

    {{-- ========================= MODAL KONFIRMASI ARSIP ========================= --}}
    <div id="modal-archive" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog"
        aria-modal="true">

        {{-- Overlay --}}
        <div id="modal-archive-overlay" onclick="closeArchiveModal()"
            class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        {{-- Panel --}}
        <div id="modal-archive-panel"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-200 scale-95 opacity-0">

            {{-- Icon + judul --}}
            <div class="flex items-center gap-4 p-6 pb-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-archive text-amber-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Arsipkan Lowongan?</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 pb-2">
                {{-- Nama lowongan --}}
                <div class="bg-gray-50 rounded-xl px-4 py-3 mb-4">
                    <p class="text-[11px] font-semibold text-gray-400 mb-1 uppercase tracking-wide">Lowongan yang akan
                        diarsipkan</p>
                    <p id="modal-archive-title" class="text-[14px] font-extrabold text-gray-800"></p>
                </div>

                {{-- Konsekuensi --}}
                <div class="space-y-2.5">
                    <div class="flex items-start gap-2.5">
                        <div
                            class="w-5 h-5 rounded-full bg-red-50 border border-red-200 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-x text-red-400 text-[11px]"></i>
                        </div>
                        <p class="text-[12px] text-gray-500 leading-relaxed">Lowongan <strong>tidak akan tampil</strong> di
                            halaman publik</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div
                            class="w-5 h-5 rounded-full bg-red-50 border border-red-200 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-x text-red-400 text-[11px]"></i>
                        </div>
                        <p class="text-[12px] text-gray-500 leading-relaxed">Status <strong>tidak dapat dibuka
                                kembali</strong> setelah diarsipkan</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div
                            class="w-5 h-5 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-check text-emerald-500 text-[11px]"></i>
                        </div>
                        <p class="text-[12px] text-gray-500 leading-relaxed">Data lamaran peserta <strong>tetap
                                tersimpan</strong> dan bisa dilihat di Verifikasi</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 p-6 pt-5">
                <button type="button" onclick="closeArchiveModal()"
                    class="px-5 py-2.5 text-[13px] font-bold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <form id="form-archive" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-[13px] font-bold
                               bg-amber-500 hover:bg-amber-600 active:scale-[0.97]
                               text-white rounded-xl shadow-md shadow-amber-500/25 transition-all duration-150">
                        <i class="bi bi-archive"></i>
                        Ya, Arsipkan
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/delete-confirm.js'])

    <script>
        const modalEl = document.getElementById('modal-archive');
        const panelEl = document.getElementById('modal-archive-panel');
        const titleEl = document.getElementById('modal-archive-title');
        const formEl = document.getElementById('form-archive');

        function openArchiveModal(vacancyId, vacancyTitle) {
            titleEl.textContent = vacancyTitle;
            formEl.action = `/admin/vacancies/${vacancyId}/archive`;

            modalEl.classList.remove('hidden');
            modalEl.classList.add('flex');

            requestAnimationFrame(() => requestAnimationFrame(() => {
                panelEl.classList.remove('scale-95', 'opacity-0');
                panelEl.classList.add('scale-100', 'opacity-100');
            }));

            document.body.style.overflow = 'hidden';
        }

        function closeArchiveModal() {
            panelEl.classList.remove('scale-100', 'opacity-100');
            panelEl.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modalEl.classList.add('hidden');
                modalEl.classList.remove('flex');
                document.body.style.overflow = '';
            }, 150);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeArchiveModal();
        });
    </script>
@endpush
