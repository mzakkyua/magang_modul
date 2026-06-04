@extends('layouts.admin')

@section('title', 'Verifikasi Pelamar')

@section('content')

    @php
        $currentStatus = request('status');
        $currentSearch = request('search');

        // Map status ke label & warna untuk dropdown
        $statusOptions = [
            '' => ['label' => 'Semua Status', 'color' => 'gray', 'icon' => 'bi-grid-3x3-gap'],
            'pending' => ['label' => 'Menunggu', 'color' => 'amber', 'icon' => 'bi-hourglass-split'],
            'verified' => ['label' => 'Diverifikasi', 'color' => 'blue', 'icon' => 'bi-shield-check'],
            'interview' => ['label' => 'Interview', 'color' => 'purple', 'icon' => 'bi-camera-video'],
            'accepted' => ['label' => 'Diterima', 'color' => 'emerald', 'icon' => 'bi-check-circle'],
            'rejected' => ['label' => 'Ditolak', 'color' => 'red', 'icon' => 'bi-x-circle'],
            'resigned' => ['label' => 'Mundur', 'color' => 'orange', 'icon' => 'bi-person-walking'],
            'completed' => ['label' => 'Selesai / Lulus', 'color' => 'teal', 'icon' => 'bi-award-fill'],
        ];

        $activeOption = $statusOptions[$currentStatus] ?? $statusOptions[''];

        $colorMap = [
            'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200'],
            'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200'],
            'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
            'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
            'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
            'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'border' => 'border-red-200'],
            'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'border' => 'border-orange-200'],
            'teal' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-600', 'border' => 'border-teal-200'],
        ];
    @endphp

    {{-- ===================== SUMMARY CARDS (COMPACT & BOLD) ===================== --}}
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-2 mb-5">

        <div
            class="bg-white rounded-xl border border-gray-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-gray-800 leading-none">{{ array_sum($statusCounts) }}</p>
            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mt-1">Total</p>
        </div>
        <div
            class="bg-amber-50/50 rounded-xl border border-amber-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-amber-600 leading-none">{{ $statusCounts['pending'] ?? 0 }}</p>
            <p class="text-[9px] font-bold text-amber-600/70 uppercase tracking-widest mt-1">Menunggu</p>
        </div>
        <div
            class="bg-blue-50/50 rounded-xl border border-blue-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-blue-600 leading-none">
                {{ ($statusCounts['verified'] ?? 0) + ($statusCounts['interview'] ?? 0) }}</p>
            <p class="text-[9px] font-bold text-blue-600/70 uppercase tracking-widest mt-1">Diproses</p>
        </div>
        <div
            class="bg-emerald-50/50 rounded-xl border border-emerald-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-emerald-600 leading-none">{{ $statusCounts['accepted'] ?? 0 }}</p>
            <p class="text-[9px] font-bold text-emerald-600/70 uppercase tracking-widest mt-1">Diterima</p>
        </div>
        <div
            class="bg-red-50/50 rounded-xl border border-red-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-red-500 leading-none">{{ $statusCounts['rejected'] ?? 0 }}</p>
            <p class="text-[9px] font-bold text-red-500/70 uppercase tracking-widest mt-1">Ditolak</p>
        </div>
        <div
            class="bg-orange-50/50 rounded-xl border border-orange-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-orange-500 leading-none">{{ $statusCounts['resigned'] ?? 0 }}</p>
            <p class="text-[9px] font-bold text-orange-500/70 uppercase tracking-widest mt-1">Mundur</p>
        </div>
        <div
            class="bg-teal-50/50 rounded-xl border border-teal-200 shadow-sm p-2.5 flex flex-col items-center justify-center text-center">
            <p class="text-xl font-black text-teal-600 leading-none">{{ $statusCounts['completed'] ?? 0 }}</p>
            <p class="text-[9px] font-bold text-teal-600/70 uppercase tracking-widest mt-1">Lulus</p>
        </div>

    </div>

    {{-- ===================== TOOLBAR: SEARCH + FILTER ===================== --}}
    <form method="GET" action="{{ route('admin.applications.index') }}" class="flex flex-col sm:flex-row gap-2 mb-4">

        {{-- SEARCH BAR --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-search text-gray-400 text-xs"></i>
            </div>
            <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Cari nama atau email pelamar..."
                class="w-full pl-8 pr-8 py-2 rounded-lg border text-xs font-bold text-gray-800
                       outline-none transition-all duration-200 border-gray-200 bg-white 
                       placeholder:text-gray-400 placeholder:font-medium hover:border-blue-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @if ($currentSearch)
                <a href="{{ route('admin.applications.index', array_filter(['status' => $currentStatus])) }}"
                    class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-700 transition-colors">
                    <i class="bi bi-x-circle-fill text-xs"></i>
                </a>
            @endif
        </div>

        {{-- FILTER DROPDOWN --}}
        <div class="relative shrink-0" id="filterDropdownWrapper">
            <input type="hidden" name="status" id="statusHidden" value="{{ $currentStatus }}">

            <button type="button" id="filterBtn"
                onclick="document.getElementById('filterDropdown').classList.toggle('hidden')"
                class="flex items-center gap-1.5 px-3 py-2 rounded-lg border text-xs font-extrabold transition-all duration-150 whitespace-nowrap
                       {{ $currentStatus ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                <i class="bi {{ $activeOption['icon'] }} text-xs"></i>
                {{ $activeOption['label'] }}
                @if ($currentStatus)
                    <span class="bg-white/20 text-white text-[9px] px-1.5 py-0.5 rounded-md ml-1">
                        {{ $statusCounts[$currentStatus] ?? 0 }}
                    </span>
                @endif
                <i class="bi bi-chevron-down text-[10px] ml-0.5 transition-transform duration-200" id="filterChevron"></i>
            </button>

            {{-- Dropdown panel --}}
            <div id="filterDropdown"
                class="hidden absolute right-0 top-full mt-1.5 w-48 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 z-50 overflow-hidden">
                @foreach ($statusOptions as $val => $opt)
                    @php $c = $colorMap[$opt['color']]; @endphp
                    <button type="button" onclick="setFilter('{{ $val }}')"
                        class="flex items-center justify-between w-full px-3 py-2 text-xs transition-colors duration-100 text-left
                               {{ $currentStatus === $val ? 'bg-blue-50 text-blue-700 font-extrabold' : 'text-gray-700 font-bold hover:bg-gray-50' }}">
                        <span class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center {{ $c['bg'] }}">
                                <i class="bi {{ $opt['icon'] }} text-[10px] {{ $c['text'] }}"></i>
                            </span>
                            {{ $opt['label'] }}
                        </span>
                        @if ($val !== '')
                            <span
                                class="text-[9px] font-black {{ $c['text'] }} {{ $c['bg'] }} px-1.5 py-0.5 rounded-md">
                                {{ $statusCounts[$val] ?? 0 }}
                            </span>
                        @else
                            <span class="text-[9px] font-black text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-md">
                                {{ array_sum($statusCounts) }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
        <button type="submit" id="formSubmitBtn" class="hidden"></button>
    </form>

    {{-- ===================== TABEL UTAMA (COMPACT) ===================== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-200 bg-gray-50/50">
            <div class="flex items-center gap-2">
                <i class="bi bi-inbox-fill text-blue-600 text-sm"></i>
                <h2 class="font-extrabold text-gray-800 text-xs uppercase tracking-wide">
                    @if ($currentStatus)
                        Lamaran <span
                            class="text-blue-600">{{ $statusOptions[$currentStatus]['label'] ?? ucfirst($currentStatus) }}</span>
                    @else
                        Semua Lamaran
                    @endif
                    @if ($currentSearch)
                        <span class="text-gray-400 font-bold ml-1 tracking-normal capitalize">
                            (Pencarian: "{{ $currentSearch }}")
                        </span>
                    @endif
                </h2>
            </div>
            <span class="text-[10px] font-extrabold text-gray-500 bg-gray-200/50 px-2 py-0.5 rounded-md">
                {{ $data->total() }} Data
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Pelamar</th>
                        <th class="px-4 py-2 text-left text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Lowongan</th>
                        <th class="px-4 py-2 text-left text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Waktu Masuk</th>
                        <th
                            class="px-4 py-2 text-center text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Status</th>
                        <th
                            class="px-4 py-2 text-center text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $app)
                        <tr class="hover:bg-blue-50/30 transition-colors">

                            {{-- Pelamar --}}
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 border border-gray-200">
                                        <i class="bi bi-person-fill text-gray-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-gray-900 leading-tight">
                                            {{ $app->leader?->profile?->full_name ?? ($app->leader?->name ?? 'Tidak Diketahui') }}
                                        </p>
                                        <p class="text-[10px] font-medium text-gray-500 mt-0.5">
                                            {{ $app->leader?->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Lowongan --}}
                            <td class="px-4 py-2.5">
                                <p class="text-xs font-extrabold text-gray-900 leading-tight mb-0.5 truncate max-w-[200px]">
                                    {{ $app->vacancy->title }}
                                </p>
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-blue-700">
                                    <i class="bi bi-building"></i> {{ $app->vacancy->division_name }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-2.5">
                                <p class="text-xs font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($app->created_at)->format('d M Y') }}
                                </p>
                                <p class="text-[10px] font-medium text-gray-500">
                                    {{ \Carbon\Carbon::parse($app->created_at)->format('H:i') }} WIB
                                </p>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-2.5 text-center">
                                @if ($app->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                                        <i class="bi bi-hourglass-split"></i> Menunggu
                                    </span>
                                @elseif ($app->status === 'verified')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-blue-700 bg-blue-50 border border-blue-200 rounded-md">
                                        <i class="bi bi-shield-check"></i> Diverifikasi
                                    </span>
                                @elseif ($app->status === 'interview')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-purple-700 bg-purple-50 border border-purple-200 rounded-md">
                                        <i class="bi bi-camera-video"></i> Interview
                                    </span>
                                @elseif ($app->status === 'accepted')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md">
                                        <i class="bi bi-check-circle-fill"></i> Diterima
                                    </span>
                                @elseif ($app->status === 'rejected')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-red-600 bg-red-50 border border-red-200 rounded-md">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </span>
                                @elseif ($app->status === 'resigned')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-orange-600 bg-orange-50 border border-orange-200 rounded-md">
                                        <i class="bi bi-person-walking"></i> Mundur
                                    </span>
                                @elseif ($app->status === 'completed')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold text-teal-700 bg-teal-50 border border-teal-200 rounded-md">
                                        <i class="bi bi-award-fill"></i> Lulus
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-2.5 text-center">
                                <a href="{{ route('admin.applications.show', $app->id) }}"
                                    class="inline-flex items-center gap-1 text-[11px] font-extrabold text-blue-700 border border-blue-200 bg-white px-2.5 py-1 rounded-md hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm">
                                    Periksa <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div
                                    class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                    <i class="bi bi-inbox text-xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-800 font-extrabold text-xs">Tidak ada data ditemukan</p>
                                <p class="text-gray-400 text-[10px] mt-0.5 max-w-xs mx-auto">
                                    @if ($currentSearch)
                                        Pencarian "{{ $currentSearch }}" tidak membuahkan hasil.
                                    @elseif ($currentStatus)
                                        Tidak ada lamaran berstatus
                                        "{{ $statusOptions[$currentStatus]['label'] ?? $currentStatus }}".
                                    @else
                                        Belum ada pelamar.
                                    @endif
                                </p>
                                @if ($currentSearch || $currentStatus)
                                    <a href="{{ route('admin.applications.index') }}"
                                        class="inline-block mt-3 text-[10px] font-extrabold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-md hover:bg-blue-600 hover:text-white transition-colors">
                                        Reset Filter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($data->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50/30">
                {{ $data->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function setFilter(val) {
            document.getElementById('statusHidden').value = val;
            document.getElementById('filterDropdown').classList.add('hidden');
            document.getElementById('formSubmitBtn').click();
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('filterDropdownWrapper');
            const dropdown = document.getElementById('filterDropdown');
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        document.getElementById('filterBtn')?.addEventListener('click', function() {
            const chevron = document.getElementById('filterChevron');
            const dropdown = document.getElementById('filterDropdown');
            const isHidden = dropdown.classList.contains('hidden');
            chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        });
    </script>
@endpush
