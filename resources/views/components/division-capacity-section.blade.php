@if ($divisionCapacity->isNotEmpty())
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6 flex flex-col font-sans">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shadow-inner">
                    <i class="bi bi-pie-chart-fill text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm tracking-tight">Monitoring Kapasitas Divisi</h3>
                    <p class="text-xs text-gray-500">Ringkasan ketersediaan slot magang</p>
                </div>
            </div>

            @if (isset($isSuperAdmin) && $isSuperAdmin)
                <a href="{{ route('admin.division-settings.index') }}"
                    class="group flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors">
                    Atur Kuota
                    <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            @endif
        </div>

        {{-- Tabel Kapasitas --}}
        <div class="divide-y divide-gray-100">
            @foreach ($divisionCapacity as $item)
                @php
                    $isFull = $item['is_full'];
                    $pct = $item['fill_percentage'] ?? 0;
                @endphp

                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
                    {{-- Info Divisi & Progress --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $item['division_name'] }}</p>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                                @if ($item['max_slots'] !== null)
                                    {{ $item['filled_slots'] }} dari {{ $item['max_slots'] }} Slot Terisi
                                @else
                                    {{ $item['filled_slots'] }} Peserta Aktif
                                @endif
                            </span>
                        </div>

                        @if ($item['max_slots'] !== null)
                            <div class="w-full max-w-md h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div @class([
                                    'h-full rounded-full transition-all duration-700 ease-out',
                                    'bg-red-500' => $isFull,
                                    'bg-amber-500' => !$isFull && $pct >= 80,
                                    'bg-emerald-500' => !$isFull && $pct < 80,
                                ]) style="width: {{ $pct }}%">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Status Badge & Timeline Info --}}
                    <div class="shrink-0 flex flex-col sm:items-end justify-center mt-2 sm:mt-0 gap-1.5">
                        @if ($isFull)
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide bg-red-50 text-red-700 border border-red-200 px-3 py-1 rounded-full">
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> Penuh
                            </span>
                        @elseif ($item['max_slots'] === null)
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1 rounded-full">
                                <i class="bi bi-infinity"></i> Unlimited
                            </span>
                        @else
                            <span @class([
                                'inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full border',
                                'bg-amber-50 text-amber-700 border-amber-200' => !$isFull && $pct >= 80,
                                'bg-emerald-50 text-emerald-700 border-emerald-200' =>
                                    !$isFull && $pct < 80,
                            ])>
                                <span @class([
                                    'w-2 h-2 rounded-full',
                                    'bg-amber-500' => !$isFull && $pct >= 80,
                                    'bg-emerald-500' => !$isFull && $pct < 80,
                                ])></span>
                                Sisa {{ $item['available_slots'] }} Slot
                            </span>
                        @endif

                        {{-- Data Selesai & Estimasi Buka --}}
                        <div class="flex flex-col sm:items-end text-xs text-gray-500 font-medium mt-1">
                            @if ($item['last_batch_end'])
                                <p>Terakhir Selesai: <span
                                        class="text-gray-900 font-semibold">{{ $item['last_batch_end'] }}</span></p>
                            @else
                                <p class="text-gray-400 italic">Belum ada riwayat magang</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
            <p class="text-xs text-gray-500 flex items-center gap-2">
                <i class="bi bi-info-circle text-gray-400"></i>
                Data dihitung secara *real-time* berdasarkan status penerimaan lamaran saat ini.
            </p>
        </div>
    </div>
@endif
