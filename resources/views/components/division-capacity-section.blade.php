{{--
    =========================================================
    PARTIAL: division-capacity-section.blade.php
    =========================================================

    Menampilkan section kapasitas divisi.

    Props yang dibutuhkan dari controller:
    - $divisionCapacity  Collection  hasil DivisionCapacityService

    Digunakan di:
    - landing/index.blade.php (tampilan guest)
    - admin/dashboard/index.blade.php (tampilan admin)

    Jika $divisionCapacity kosong, section ini tidak tampil.
    =========================================================
--}}

@if ($divisionCapacity->isNotEmpty())
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden mb-6">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-bar-chart-line text-blue-600 text-xs"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-sm">Kapasitas Lowongan per Divisi</h3>
            </div>

            {{-- Link kelola kuota — hanya tampil untuk superadmin di dashboard --}}
            @if (isset($isSuperAdmin) && $isSuperAdmin)
                <a href="{{ route('admin.division-settings.index') }}"
                    class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline transition flex items-center gap-1">
                    Kelola Kuota <i class="bi bi-arrow-right text-[10px]"></i>
                </a>
            @endif
        </div>

        {{-- Tabel Kapasitas --}}
        <div class="divide-y divide-gray-50">
            @foreach ($divisionCapacity as $item)
                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50/60 transition">

                    {{-- Nama Divisi --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $item['division_name'] }}</p>

                        {{-- Progress bar --}}
                        @if ($item['max_slots'] !== null)
                            <div class="mt-1.5 w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300
                                    @if ($item['is_full']) bg-red-400
                                    @elseif ($item['fill_percentage'] >= 70)
                                    @else @endif"
                                    style="width: {{ $item['fill_percentage'] }}%">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Slot info --}}
                    <div class="text-right shrink-0">
                        @if ($item['max_slots'] !== null)
                            <p class="text-xs font-semibold text-gray-500">
                                {{ $item['filled_slots'] }} / {{ $item['max_slots'] }}
                            </p>
                        @else
                            <p class="text-xs font-semibold text-gray-400">{{ $item['filled_slots'] }} aktif</p>
                        @endif
                    </div>

                    {{-- Status badge --}}
                    <div class="shrink-0 w-28 text-right">
                        @if ($item['is_full'])
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-bold bg-red-50 text-red-600 border border-red-100 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                Penuh
                            </span>
                            @if ($item['estimated_open'])
                                <p class="text-[10px] text-gray-400 mt-1">≈ {{ $item['estimated_open'] }}</p>
                            @endif
                        @elseif ($item['max_slots'] === null)
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-1 rounded-full">
                                Unlimited
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                {{ $item['available_slots'] }} slot
                            </span>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Footer note --}}
        <div class="px-5 py-3 border-t border-gray-50 bg-gray-50/50">
            <p class="text-[11px] text-gray-400">
                <i class="bi bi-info-circle mr-1"></i>
                Slot dihitung dari lowongan berstatus <strong>aktif</strong> (open/closed).
                Estimasi buka berdasarkan tanggal berakhir lowongan terpanjang.
            </p>
        </div>
    </div>
@endif
