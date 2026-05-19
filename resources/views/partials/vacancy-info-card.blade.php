<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">
        Ringkasan Lowongan
    </h3>

    <div class="flex flex-col">
        {{-- Batas Lamaran --}}
        <div class="info-row">
            <div class="info-icon bg-red-50/50 border border-red-100/50">
                <i class="bi bi-clock-history text-red-500 text-base"></i>
            </div>
            <div>
                <div class="info-label">Batas Lamaran</div>
                <div class="info-value text-red-600">
                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d F Y') }}
                </div>
            </div>
        </div>

        {{-- Periode Magang --}}
        <div class="info-row">
            <div class="info-icon bg-blue-50/50 border border-blue-100/50">
                <i class="bi bi-calendar2-range text-blue-500 text-base"></i>
            </div>
            <div>
                <div class="info-label">Periode Berlangsung</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M') }}
                    &mdash;
                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                </div>
            </div>
        </div>

        {{-- Sisa Kuota --}}
        <div class="info-row">
            <div class="info-icon bg-emerald-50/50 border border-emerald-100/50">
                <i class="bi bi-person-check text-emerald-500 text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-end mb-1">
                    <div class="info-label mb-0">Ketersediaan Kuota</div>
                    <div class="text-[10px] font-bold text-gray-400">{{ $pct }}% Terisi</div>
                </div>
                <div class="info-value">
                    <span style="color:{{ $accentColor }}">{{ $vacancy->getSisaKuota() }}</span>
                    <span class="text-gray-400 font-medium text-xs"> / {{ $vacancy->quota_slots }} slot</span>
                </div>
                <div class="quota-bar-wrap">
                    <div class="quota-bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Mode Pendaftaran --}}
        <div class="info-row">
            <div class="info-icon bg-purple-50/50 border border-purple-100/50">
                <i class="bi bi-ui-radios text-purple-500 text-base"></i>
            </div>
            <div>
                <div class="info-label">Tipe Pendaftaran</div>
                <div class="info-value capitalize">{{ $vacancy->registration_mode }}</div>
            </div>
        </div>

        {{-- Jumlah Anggota --}}
        <div class="info-row">
            <div class="info-icon bg-indigo-50/50 border border-indigo-100/50">
                <i class="bi bi-people text-indigo-500 text-base"></i>
            </div>
            <div>
                <div class="info-label">Syarat Anggota</div>
                <div class="info-value">
                    @if ($vacancy->registration_mode === 'individu')
                        Individu (1 Orang)
                    @else
                        {{ $vacancy->min_members }} &ndash; {{ $vacancy->max_members }} Orang per Tim
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
