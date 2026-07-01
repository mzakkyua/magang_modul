@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

@push('header_actions')
    <a href="{{ route('admin.vacancies.create') }}"
        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
               text-white text-xs font-extrabold px-3.5 py-2 rounded-lg
               shadow-sm hover:shadow-md transition-all duration-200">
        <i class="bi bi-plus-lg"></i> Buat Lowongan Baru
    </a>
@endpush

@section('content')

    {{-- ========================= STAT CARDS (COMPACT & BOLD) ========================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-2.5 mb-5">

        <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-50/80 flex items-center justify-center shrink-0 border border-blue-100">
                <i class="bi bi-briefcase-fill text-blue-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-xl font-black text-gray-900 leading-none">{{ $totalVacancies ?? 0 }}</p>
                <p class="text-[9px] font-extrabold text-gray-500 uppercase tracking-widest mt-0.5">Total</p>
            </div>
        </div>

        <div class="bg-emerald-50/40 rounded-xl p-3 border border-emerald-100 shadow-sm flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-lg bg-emerald-100/50 flex items-center justify-center shrink-0 border border-emerald-200">
                <i class="bi bi-check-circle-fill text-emerald-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-xl font-black text-emerald-600 leading-none">{{ $openCount ?? 0 }}</p>
                <p class="text-[9px] font-extrabold text-emerald-600/70 uppercase tracking-widest mt-0.5">Sedang Buka</p>
            </div>
        </div>

        <div class="bg-red-50/40 rounded-xl p-3 border border-red-100 shadow-sm flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-red-100/50 flex items-center justify-center shrink-0 border border-red-200">
                <i class="bi bi-x-circle-fill text-red-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-xl font-black text-red-600 leading-none">{{ $closedCount ?? 0 }}</p>
                <p class="text-[9px] font-extrabold text-red-600/70 uppercase tracking-widest mt-0.5">Ditutup</p>
            </div>
        </div>

        <div class="bg-slate-50/50 rounded-xl p-3 border border-slate-200 shadow-sm flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-lg bg-slate-200/50 flex items-center justify-center shrink-0 border border-slate-300">
                <i class="bi bi-archive-fill text-slate-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-xl font-black text-slate-600 leading-none">{{ $archivedCount ?? 0 }}</p>
                <p class="text-[9px] font-extrabold text-slate-500/80 uppercase tracking-widest mt-0.5">Diarsipkan</p>
            </div>
        </div>

        <div class="bg-amber-50/40 rounded-xl p-3 border border-amber-100 shadow-sm flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-lg bg-amber-100/50 flex items-center justify-center shrink-0 border border-amber-200">
                <i class="bi bi-people-fill text-amber-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-xl font-black text-amber-600 leading-none">{{ $withApplicantsCount ?? 0 }}</p>
                <p class="text-[9px] font-extrabold text-amber-600/70 uppercase tracking-widest mt-0.5">Ada Pendaftar</p>
            </div>
        </div>

    </div>

    {{-- ========================= TABEL (COMPACT) ========================= --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header + Tab Filter --}}
        <div class="px-4 pt-3 border-b border-gray-200 bg-gray-50/50">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <i class="bi bi-ui-radios-grid text-blue-600 text-sm"></i>
                    <span class="text-xs font-extrabold text-gray-900 uppercase tracking-wide">Daftar Lowongan</span>
                </div>
                <span class="text-[9px] font-extrabold bg-gray-200/60 text-gray-500 px-2.5 py-1 rounded-md tracking-wider">
                    {{ $vacancies->total() }} DATA
                </span>
            </div>

            {{-- TAB FILTER (COMPACT) --}}
            <div class="flex gap-1 mt-3">
                <a href="{{ route('admin.vacancies.index', ['tab' => 'active']) }}"
                    class="px-3 py-1.5 text-[10px] font-extrabold rounded-t-lg border-b-2 transition-colors uppercase tracking-widest flex items-center gap-1.5
                        {{ $activeTab === 'active'
                            ? 'border-blue-600 text-blue-700 bg-blue-50/80'
                            : 'border-transparent text-gray-400 hover:text-gray-700 hover:bg-gray-100/50' }}">
                    <i class="bi bi-lightning-charge-fill"></i> Aktif
                    <span
                        class="text-[9px] font-black px-1.5 py-0.5 rounded-md leading-none
                        {{ $activeTab === 'active' ? 'bg-blue-200/50 text-blue-700' : 'bg-gray-200/50 text-gray-500' }}">
                        {{ ($openCount ?? 0) + ($closedCount ?? 0) }}
                    </span>
                </a>

                <a href="{{ route('admin.vacancies.index', ['tab' => 'all']) }}"
                    class="px-3 py-1.5 text-[10px] font-extrabold rounded-t-lg border-b-2 transition-colors uppercase tracking-widest flex items-center gap-1.5
                        {{ $activeTab === 'all'
                            ? 'border-blue-600 text-blue-700 bg-blue-50/80'
                            : 'border-transparent text-gray-400 hover:text-gray-700 hover:bg-gray-100/50' }}">
                    <i class="bi bi-list-ul"></i> Semua
                    <span
                        class="text-[9px] font-black px-1.5 py-0.5 rounded-md leading-none
                        {{ $activeTab === 'all' ? 'bg-blue-200/50 text-blue-700' : 'bg-gray-200/50 text-gray-500' }}">
                        {{ $totalVacancies ?? 0 }}
                    </span>
                </a>

                <a href="{{ route('admin.vacancies.index', ['tab' => 'archived']) }}"
                    class="px-3 py-1.5 text-[10px] font-extrabold rounded-t-lg border-b-2 transition-colors uppercase tracking-widest flex items-center gap-1.5
                        {{ $activeTab === 'archived'
                            ? 'border-slate-500 text-slate-700 bg-slate-100'
                            : 'border-transparent text-gray-400 hover:text-gray-700 hover:bg-gray-100/50' }}">
                    <i class="bi bi-archive-fill"></i> Arsip
                    <span
                        class="text-[9px] font-black px-1.5 py-0.5 rounded-md leading-none
                        {{ $activeTab === 'archived' ? 'bg-slate-300 text-slate-700' : 'bg-gray-200/50 text-gray-500' }}">
                        {{ $archivedCount ?? 0 }}
                    </span>
                </a>
            </div>
        </div>

        {{-- Banner info tab arsip --}}
        @if ($activeTab === 'archived')
            <div class="flex items-center gap-2 bg-slate-50 border-b border-slate-200 px-4 py-2">
                <i class="bi bi-info-circle-fill text-slate-400 text-xs shrink-0"></i>
                <p class="text-[10px] font-bold text-slate-500 tracking-wide uppercase">
                    Lowongan arsip <span class="text-slate-700">tidak tampil</span> di publik & <span
                        class="text-slate-700">tidak dapat dibuka kembali</span>.
                </p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Lowongan</th>
                        <th class="px-4 py-2 text-left text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Tipe & Mode</th>
                        <th
                            class="px-4 py-2 text-center text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Kuota</th>
                        <th
                            class="px-4 py-2 text-center text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Periode</th>
                        <th
                            class="px-4 py-2 text-center text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Status</th>
                        <th
                            class="px-4 py-2 text-center text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($vacancies as $vacancy)
                        <tr
                            class="transition-colors duration-150 group {{ $vacancy->status === 'archived' ? 'bg-slate-50/60' : 'hover:bg-blue-50/30' }}">

                            {{-- KOLOM LOWONGAN --}}
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-1 h-8 rounded-full shrink-0
                                        {{ $vacancy->status === 'archived' ? 'bg-slate-300' : ($vacancy->type === 'penelitian' ? 'bg-violet-400' : 'bg-blue-500') }}">
                                    </div>
                                    <div>
                                        <p
                                            class="font-extrabold text-xs leading-tight mb-0.5 truncate max-w-62.5
                                            {{ $vacancy->status === 'archived' ? 'text-gray-400' : 'text-gray-900 group-hover:text-blue-600' }}">
                                            {{ $vacancy->title }}
                                        </p>
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-gray-500">
                                                <i class="bi bi-building"></i> {{ $vacancy->division_name }}
                                            </span>
                                            @if ($vacancy->applications_count > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded-md">
                                                    <i class="bi bi-people-fill"></i> {{ $vacancy->applications_count }}
                                                    Pendaftar
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM TIPE & MODE --}}
                            <td class="px-4 py-2.5">
                                <span
                                    class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 rounded-md
                                    {{ $vacancy->status === 'archived'
                                        ? 'text-gray-400 bg-gray-100 border border-gray-200'
                                        : ($vacancy->type === 'penelitian'
                                            ? 'text-violet-700 bg-violet-50 border border-violet-200'
                                            : 'text-blue-700 bg-blue-50 border border-blue-200') }}">
                                    <i
                                        class="bi {{ $vacancy->type === 'penelitian' ? 'bi-journal-text' : 'bi-person-workspace' }}"></i>
                                    {{ $vacancy->type }}
                                </span>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wide mt-1">
                                    <i class="bi bi-people mr-0.5"></i> {{ $vacancy->registration_mode }}
                                </p>
                            </td>

                            {{-- KOLOM KUOTA --}}
                            <td class="px-4 py-2.5 text-center">
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-md
                                    {{ $vacancy->status === 'archived' ? 'bg-gray-100 text-gray-400 border border-gray-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $vacancy->quota_slots }} <span
                                        class="font-bold text-[8px] uppercase tracking-widest">Slot</span>
                                </span>
                            </td>

                            {{-- KOLOM PERIODE --}}
                            <td class="px-4 py-2.5 text-center">
                                <p
                                    class="text-[10px] font-extrabold {{ $vacancy->status === 'archived' ? 'text-gray-400' : 'text-gray-800' }}">
                                    {{ $vacancy->start_date->format('d M Y') }}
                                </p>
                                <p class="text-[8px] font-bold text-gray-400 uppercase my-0.5 tracking-widest">S/D</p>
                                <p
                                    class="text-[10px] font-extrabold {{ $vacancy->status === 'archived' ? 'text-gray-400' : 'text-gray-800' }}">
                                    {{ $vacancy->end_date->format('d M Y') }}
                                </p>
                            </td>

                            {{-- KOLOM STATUS --}}
                            <td class="px-4 py-2.5 text-center">
                                @if ($vacancy->status === 'open')
                                    <span
                                        class="inline-flex items-center gap-1 text-[9px] font-black tracking-widest uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 rounded-md shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> OPEN
                                    </span>
                                @elseif ($vacancy->status === 'closed')
                                    <span
                                        class="inline-flex items-center gap-1 text-[9px] font-black tracking-widest uppercase bg-gray-100 text-gray-500 border border-gray-200 px-2 py-1 rounded-md shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> CLOSED
                                    </span>
                                @elseif ($vacancy->status === 'archived')
                                    <span
                                        class="inline-flex items-center gap-1 text-[9px] font-black tracking-widest uppercase bg-slate-100 text-slate-500 border border-slate-200 px-2 py-1 rounded-md shadow-sm">
                                        <i class="bi bi-archive-fill"></i> ARSIP
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="px-4 py-2.5">
                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- EDIT --}}
                                    @if ($vacancy->status !== 'archived')
                                        <a href="{{ route('admin.vacancies.edit', $vacancy) }}" title="Edit Lowongan"
                                            class="w-7 h-7 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-500 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm">
                                            <i class="bi bi-pencil-square text-[11px]"></i>
                                        </a>
                                    @else
                                        <div title="Diarsipkan — tidak dapat diedit"
                                            class="w-7 h-7 rounded-md border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-pencil-square text-[11px]"></i>
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
                                                class="w-7 h-7 rounded-md border flex items-center justify-center transition-all shadow-sm
                                                    {{ $vacancy->status === 'open' ? 'border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'border-gray-200 bg-white text-gray-400 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600' }}">
                                                <i
                                                    class="bi {{ $vacancy->status === 'open' ? 'bi-toggle-on' : 'bi-toggle-off' }} text-[14px] leading-none"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div title="Status final — tidak dapat diubah"
                                            class="w-7 h-7 rounded-md border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-lock-fill text-[11px]"></i>
                                        </div>
                                    @endif

                                    {{-- ARSIPKAN --}}
                                    @if ($vacancy->status === 'closed')
                                        <button type="button" title="Arsipkan Lowongan"
                                            onclick="openArchiveModal('{{ $vacancy->id }}', '{{ addslashes($vacancy->title) }}')"
                                            class="w-7 h-7 rounded-md border border-amber-200 bg-amber-50 flex items-center justify-center text-amber-500 hover:border-amber-300 hover:bg-amber-100 hover:text-amber-600 transition-all shadow-sm">
                                            <i class="bi bi-archive-fill text-[11px]"></i>
                                        </button>
                                    @else
                                        <div class="w-7 h-7"></div>
                                    @endif

                                    {{-- DELETE --}}
                                    @if ($vacancy->status !== 'archived' && $vacancy->applications_count === 0)
                                        <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST"
                                            class="inline-flex form-delete" data-name="{{ $vacancy->title }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Permanen"
                                                class="w-7 h-7 rounded-md border border-gray-200 bg-white flex items-center justify-center text-gray-500 hover:border-red-300 hover:bg-red-50 hover:text-red-600 transition-all shadow-sm">
                                                <i class="bi bi-trash3-fill text-[11px]"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div title="{{ $vacancy->status === 'archived' ? 'Lowongan diarsipkan' : 'Sudah ada pendaftar' }}"
                                            class="w-7 h-7 rounded-md border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-trash3-fill text-[11px]"></i>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div
                                    class="w-12 h-12 rounded-xl {{ $activeTab === 'archived' ? 'bg-slate-100' : 'bg-gray-100' }} flex items-center justify-center mx-auto mb-2 border border-gray-200">
                                    <i
                                        class="bi {{ $activeTab === 'archived' ? 'bi-archive-fill text-slate-300' : 'bi-briefcase-fill text-gray-300' }} text-xl"></i>
                                </div>
                                <p class="text-xs font-extrabold text-gray-800">
                                    {{ $activeTab === 'archived' ? 'Arsip Kosong' : 'Belum Ada Lowongan' }}
                                </p>
                                <p class="text-[10px] font-bold text-gray-400 mt-0.5">
                                    {{ $activeTab === 'archived' ? 'Belum ada data lowongan yang diarsipkan.' : 'Buat lowongan pertama untuk menerima pendaftar.' }}
                                </p>
                                @if ($activeTab !== 'archived')
                                    <a href="{{ route('admin.vacancies.create') }}"
                                        class="inline-flex items-center gap-1.5 bg-blue-600 text-white text-[10px] font-extrabold px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors mt-3">
                                        <i class="bi bi-plus-lg"></i> Buat Lowongan
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($vacancies->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50/30">
                {{ $vacancies->links() }}
            </div>
        @endif

    </div>

    {{-- ========================= MODAL KONFIRMASI ARSIP (COMPACT) ========================= --}}
    <div id="modal-archive" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog"
        aria-modal="true">
        <div id="modal-archive-overlay" onclick="closeArchiveModal()"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div id="modal-archive-panel"
            class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm transform transition-all duration-200 scale-95 opacity-0">

            <div class="p-5 pb-3 flex items-start gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-archive-fill text-amber-500 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">Arsipkan Lowongan?</h3>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mt-0.5">Tindakan Permanen</p>
                </div>
            </div>

            <div class="px-5 pb-4">
                <div class="bg-gray-50 rounded-lg border border-gray-200 px-3 py-2 mb-3">
                    <p class="text-[9px] font-extrabold text-gray-400 mb-0.5 uppercase tracking-widest">Target Arsip</p>
                    <p id="modal-archive-title" class="text-xs font-black text-gray-800 leading-tight"></p>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-start gap-2">
                        <i class="bi bi-x-circle-fill text-red-500 text-[11px] mt-0.5"></i>
                        <p class="text-[10px] font-bold text-gray-600 leading-tight">Tidak akan tampil di halaman publik
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="bi bi-x-circle-fill text-red-500 text-[11px] mt-0.5"></i>
                        <p class="text-[10px] font-bold text-gray-600 leading-tight">Status tidak dapat dibuka kembali</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-emerald-500 text-[11px] mt-0.5"></i>
                        <p class="text-[10px] font-bold text-gray-600 leading-tight">Data pelamar tetap tersimpan aman</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 pt-3 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closeArchiveModal()"
                    class="px-4 py-1.5 text-[11px] font-extrabold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <form id="form-archive" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 text-[11px] font-extrabold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm transition-colors">
                        <i class="bi bi-archive-fill"></i> Ya, Arsipkan
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
