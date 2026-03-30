{{-- Update pada bagian class pembungkus paling luar --}}
<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition border flex flex-col h-full">

    <div class="p-6 flex flex-col h-full grow"> {{-- Tambahkan flex-col di sini --}}

        <div class="grow"> {{-- Bungkus semua konten atas dengan div ini --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1">
                        {{ $job->title }}
                    </h3>

                    <span class="text-[11px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded font-medium">
                        Divisi {{ $job->division_name }}
                    </span>
                </div>

                <span
                    class="text-[10px] font-bold px-2 py-1 rounded
                    {{ $job->type == 'magang' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                    {{ strtoupper($job->type) }}
                </span>
            </div>

            {{-- Deskripsi dengan tinggi minimal agar lebih rapi --}}
            <p class="text-sm text-gray-600 mb-4 min-h-12">
                {{ Str::limit($job->description, 100) }}
            </p>

            <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
                <span>
                    <i class="bi bi-people"></i>
                    Sisa Kuota: <strong class="text-blue-600">{{ $job->getSisaKuota() }}</strong> /
                    {{ $job->quota_slots }}
                </span>

                <span>
                    <i class="bi bi-calendar"></i>
                    {{ \Carbon\Carbon::parse($job->start_date)->format('d M') }}
                    -
                    {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}
                </span>
            </div>
        </div> {{-- Akhir pembungkus konten atas --}}

        {{-- Tombol sekarang akan otomatis terdorong ke paling bawah karena 'flex-grow' di atas --}}
        <div class="mt-auto">
            <a href="{{ route('landing.show', $job->id) }}"
                class="block w-full text-center bg-gray-900 hover:bg-gray-800 text-white py-2.5 rounded-lg transition-colors font-semibold">
                Lihat Detail
            </a>
        </div>

    </div>
</div>
