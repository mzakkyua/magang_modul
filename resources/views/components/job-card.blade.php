{{-- ===================== JOB CARD COMPONENT ===================== --}}
<div
    class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-blue-100 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full overflow-hidden">

    {{-- Top accent bar berdasarkan type --}}
    <div
        class="h-1 w-full {{ $job->type == 'magang' ? 'bg-linear-to-r from-blue-500 to-blue-400' : 'bg-linear-to-r from-purple-500 to-violet-400' }}">
    </div>

    <div class="p-5 flex flex-col h-full grow">

        {{-- KONTEN ATAS --}}
        <div class="grow">

            {{-- Header: Judul + Badge Type --}}
            <div class="flex justify-between items-start gap-3 mb-3">
                <div class="min-w-0">
                    <h3
                        class="text-base font-bold text-gray-900 leading-snug mb-1.5 group-hover:text-blue-700 transition-colors">
                        {{ $job->title }}
                    </h3>
                    <span
                        class="inline-flex items-center gap-1 text-[11px] text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full font-semibold">
                        <i class="bi bi-building text-[10px]"></i>
                        {{ $job->division_name }}
                    </span>
                </div>

                {{-- Badge Type --}}
                <span
                    class="shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide
                    {{ $job->type == 'magang'
                        ? 'bg-blue-100 text-blue-700 border border-blue-200'
                        : 'bg-purple-100 text-purple-700 border border-purple-200' }}">
                    {{ strtoupper($job->type) }}
                </span>
            </div>

            {{-- Deskripsi --}}
            <p class="text-sm text-gray-500 leading-relaxed mb-4 min-h-12">
                {{ $job->description ? Str::limit($job->description, 100) : 'Tidak ada deskripsi tersedia.' }}
            </p>

            {{-- Info: Kuota & Periode --}}
            <div class="space-y-2 mb-4">

                {{-- Kuota dengan progress bar --}}
                <div>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1">
                            <i class="bi bi-people text-blue-500"></i> Sisa Kuota
                        </span>
                        <span>
                            <strong class="{{ $job->getSisaKuota() > 0 ? 'text-blue-600' : 'text-red-500' }}">
                                {{ $job->getSisaKuota() }}
                            </strong>
                            <span class="text-gray-400">/ {{ $job->quota_slots }} slot</span>
                        </span>
                    </div>
                    @php
                        $filled = $job->quota_slots - $job->getSisaKuota();
                        $percentage = $job->quota_slots > 0 ? ($filled / $job->quota_slots) * 100 : 0;
                        $barColor =
                            $percentage >= 80 ? 'bg-red-400' : ($percentage >= 50 ? 'bg-yellow-400' : 'bg-blue-500');
                    @endphp
                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $barColor }} transition-all duration-500"
                            style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                {{-- Periode --}}
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <i class="bi bi-calendar3 text-blue-500"></i>
                    <span>
                        {{ \Carbon\Carbon::parse($job->start_date)->format('d M') }}
                        <span class="text-gray-300 mx-1">—</span>
                        {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}
                    </span>
                </div>

            </div>
        </div>

        {{-- TOMBOL AKSI — selalu di bawah --}}
        <div class="mt-auto pt-4 border-t border-gray-50">
            @php
                // BUSINESS LOGIC: Tentukan rute dinamis berdasarkan status login
                $detailRoute = Auth::guard('magang')->check()
                    ? route('dashboard.show', $job->id)
                    : route('landing.show', $job->id);
            @endphp

            @if ($job->getSisaKuota() > 0)
                <a href="{{ $detailRoute }}"
                    class="flex items-center justify-center gap-2 w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-xl transition-all duration-200 font-semibold text-sm shadow-sm shadow-blue-600/20 group-hover:shadow-md group-hover:shadow-blue-600/30">
                    Lihat Detail <i class="bi bi-arrow-right text-xs"></i>
                </a>
            @else
                <a href="{{ $detailRoute }}"
                    class="flex items-center justify-center gap-2 w-full text-center bg-gray-100 text-gray-400 py-2.5 px-4 rounded-xl font-semibold text-sm cursor-not-allowed border border-gray-200">
                    <i class="bi bi-slash-circle text-xs"></i> Kuota Penuh
                </a>
            @endif
        </div>

    </div>
</div>
