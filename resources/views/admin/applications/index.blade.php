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

    {{-- ===================== SUMMARY CARDS ===================== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-gray-800">{{ array_sum($statusCounts) }}</p>
            <p class="text-xs text-gray-400 font-medium">Total</p>
        </div>
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-amber-500">{{ $statusCounts['pending'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Menunggu</p>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-blue-500">{{ $statusCounts['verified'] + $statusCounts['interview'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Diproses</p>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-emerald-500">{{ $statusCounts['accepted'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Diterima</p>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-red-400">{{ $statusCounts['rejected'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Ditolak</p>
        </div>
        <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-orange-400">{{ $statusCounts['resigned'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Mundur</p>
        </div>
        <div class="bg-white rounded-2xl border border-teal-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-teal-500">{{ $statusCounts['completed'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Selesai/Lulus</p>
        </div>

    </div>

    {{-- ===================== TOOLBAR: SEARCH + FILTER DROPDOWN ===================== --}}
    {{--
        Semua filter dikirim via GET form — tidak ada JavaScript submit,
        cukup user tekan Enter di search atau klik opsi dropdown.
        ?search=...&status=... dikirim ke route yang sama.
        withQueryString() di controller memastikan keduanya terbawa ke pagination.
    --}}
    <form method="GET" action="{{ route('admin.applications.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5">

        {{-- ── SEARCH BAR ── --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="bi bi-search text-gray-400 text-sm"></i>
            </div>
            <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Cari nama atau email pelamar..."
                class="w-full pl-10 pr-10 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       outline-none transition-all duration-200
                       border-gray-200 bg-white placeholder:text-gray-300 placeholder:font-normal
                       hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
            {{-- Tombol clear search --}}
            @if ($currentSearch)
                <a href="{{ route('admin.applications.index', array_filter(['status' => $currentStatus])) }}"
                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="bi bi-x-circle-fill text-sm"></i>
                </a>
            @endif
        </div>

        {{-- ── FILTER DROPDOWN ── --}}
        {{--
            Tombol ini toggle dropdown via JavaScript.
            Pilihan filter dikirim sebagai hidden input agar tetap di-submit bareng form search.
        --}}
        <div class="relative shrink-0" id="filterDropdownWrapper">
            <input type="hidden" name="status" id="statusHidden" value="{{ $currentStatus }}">

            <button type="button" id="filterBtn"
                onclick="document.getElementById('filterDropdown').classList.toggle('hidden')"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-bold
                       transition-all duration-150 whitespace-nowrap
                       {{ $currentStatus
                           ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-600/20'
                           : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300' }}">
                <i class="bi {{ $activeOption['icon'] }} text-sm"></i>
                {{ $activeOption['label'] }}
                @if ($currentStatus)
                    {{-- Badge count pada filter aktif --}}
                    <span class="bg-white/25 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">
                        {{ $statusCounts[$currentStatus] ?? 0 }}
                    </span>
                @endif
                <i class="bi bi-chevron-down text-xs ml-1 transition-transform duration-200" id="filterChevron"></i>
            </button>

            {{-- Dropdown panel --}}
            <div id="filterDropdown"
                class="hidden absolute right-0 top-full mt-2 w-52 bg-white border border-gray-100
                        rounded-2xl shadow-xl shadow-gray-200/80 py-2 z-50 overflow-hidden">

                @foreach ($statusOptions as $val => $opt)
                    @php $c = $colorMap[$opt['color']]; @endphp
                    <button type="button" onclick="setFilter('{{ $val }}')"
                        class="flex items-center justify-between w-full px-4 py-2.5 text-sm
                               transition-colors duration-100 text-left
                               {{ $currentStatus === $val
                                   ? 'bg-blue-50 text-blue-700 font-bold'
                                   : 'text-gray-600 font-medium hover:bg-gray-50' }}">
                        <span class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg flex items-center justify-center {{ $c['bg'] }}">
                                <i class="bi {{ $opt['icon'] }} text-xs {{ $c['text'] }}"></i>
                            </span>
                            {{ $opt['label'] }}
                        </span>
                        @if ($val !== '')
                            <span
                                class="text-[10px] font-extrabold {{ $c['text'] }} {{ $c['bg'] }}
                                         px-1.5 py-0.5 rounded-full border {{ $c['border'] }}">
                                {{ $statusCounts[$val] ?? 0 }}
                            </span>
                        @else
                            <span
                                class="text-[10px] font-extrabold text-gray-500 bg-gray-100
                                         px-1.5 py-0.5 rounded-full">
                                {{ array_sum($statusCounts) }}
                            </span>
                        @endif
                    </button>
                @endforeach

            </div>
        </div>

        {{-- Tombol submit tersembunyi — dipicu saat user pilih filter atau tekan Enter --}}
        <button type="submit" id="formSubmitBtn" class="hidden"></button>

    </form>

    {{-- ===================== TABEL UTAMA ===================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-file-earmark-check text-blue-600 text-xs"></i>
                </div>
                <h2 class="font-bold text-gray-800 text-sm">
                    @if ($currentStatus)
                        Lamaran —
                        <span
                            class="text-blue-600 capitalize">{{ $statusOptions[$currentStatus]['label'] ?? ucfirst($currentStatus) }}</span>
                    @else
                        Semua Lamaran
                    @endif
                    @if ($currentSearch)
                        <span class="text-gray-400 font-normal text-xs ml-1">
                            · hasil pencarian "<span class="text-gray-700 font-semibold">{{ $currentSearch }}</span>"
                        </span>
                    @endif
                </h2>
            </div>
            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1 rounded-full font-medium">
                {{ $data->total() }} lamaran
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Pelamar</th>
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Lowongan Dilamar</th>
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Tanggal Masuk</th>
                        <th class="px-6 py-3.5 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3.5 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse($data as $app)
                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- Pelamar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center shrink-0 border border-gray-200">
                                        <i class="bi bi-person-fill text-gray-400 text-base"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 leading-tight">
                                            {{ $app->leader->name ?? 'User Terhapus' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $app->leader->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Lowongan --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900 leading-tight mb-1">
                                    {{ $app->vacancy->title }}
                                </p>
                                <span
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold
                                             text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full">
                                    <i class="bi bi-building text-[10px]"></i>
                                    {{ $app->vacancy->division_name }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">
                                    {{ \Carbon\Carbon::parse($app->submission_date)->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($app->submission_date)->format('H:i') }} WIB
                                </p>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if ($app->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full">
                                        <i class="bi bi-hourglass-split text-[10px]"></i> Menunggu
                                    </span>
                                @elseif ($app->status === 'verified')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-full">
                                        <i class="bi bi-shield-check text-[10px]"></i> Diverifikasi
                                    </span>
                                @elseif ($app->status === 'interview')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-purple-700 bg-purple-50 border border-purple-200 rounded-full">
                                        <i class="bi bi-camera-video text-[10px]"></i> Interview
                                    </span>
                                @elseif ($app->status === 'accepted')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                        <i class="bi bi-check-circle-fill text-[10px]"></i> Diterima
                                    </span>
                                @elseif ($app->status === 'rejected')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-red-600 bg-red-50 border border-red-200 rounded-full">
                                        <i class="bi bi-x-circle-fill text-[10px]"></i> Ditolak
                                    </span>
                                @elseif ($app->status === 'resigned')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-orange-600 bg-orange-50 border border-orange-200 rounded-full">
                                        <i class="bi bi-person-walking text-[10px]"></i> Mundur
                                    </span>
                                @elseif ($app->status === 'completed')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full">
                                        <i class="bi bi-award-fill text-[10px]"></i> Lulus
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.applications.show', $app->id) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold
                                           text-blue-600 border border-blue-200 bg-blue-50 px-3 py-1.5 rounded-xl
                                           hover:bg-blue-600 hover:text-white hover:border-blue-600
                                           transition-all duration-150">
                                    Periksa Berkas
                                    <i class="bi bi-arrow-right text-xs"></i>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="bi bi-inbox text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-semibold text-sm">Tidak ada lamaran</p>
                                <p class="text-gray-400 text-xs mt-1">
                                    @if ($currentSearch)
                                        Tidak ditemukan pelamar dengan nama atau email
                                        "<strong>{{ $currentSearch }}</strong>".
                                    @elseif ($currentStatus)
                                        Tidak ada lamaran dengan status
                                        "{{ $statusOptions[$currentStatus]['label'] ?? $currentStatus }}" saat ini.
                                    @else
                                        Belum ada lamaran masuk untuk divisi Anda.
                                    @endif
                                </p>
                                @if ($currentSearch || $currentStatus)
                                    <a href="{{ route('admin.applications.index') }}"
                                        class="inline-flex items-center gap-1.5 mt-4 text-sm font-semibold text-blue-600
                                              border border-blue-200 bg-blue-50 px-4 py-2 rounded-xl hover:bg-blue-600
                                              hover:text-white transition-all">
                                        <i class="bi bi-arrow-left text-xs"></i> Reset Filter
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
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $data->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        // SECTION: Filter dropdown — set nilai hidden input lalu submit form
        function setFilter(val) {
            document.getElementById('statusHidden').value = val;
            document.getElementById('filterDropdown').classList.add('hidden');
            document.getElementById('formSubmitBtn').click();
        }

        // SECTION: Tutup dropdown jika klik di luar area
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('filterDropdownWrapper');
            const dropdown = document.getElementById('filterDropdown');
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // SECTION: Rotate chevron saat dropdown buka/tutup
        document.getElementById('filterBtn')?.addEventListener('click', function() {
            const chevron = document.getElementById('filterChevron');
            const dropdown = document.getElementById('filterDropdown');
            const isHidden = dropdown.classList.contains('hidden');
            chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        });
    </script>
@endpush
