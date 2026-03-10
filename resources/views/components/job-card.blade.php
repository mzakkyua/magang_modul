<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition border">

    <div class="p-6">

        <div class="flex justify-between items-start mb-4">

            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $job->title }}
                </h3>

                <span class="text-sm text-blue-600 bg-blue-50 px-2 py-1 rounded">
                    Divisi {{ $job->division_name }}
                </span>
            </div>

            <span
                class="text-xs font-bold px-2 py-1 rounded
                {{ $job->type == 'magang' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">

                {{ strtoupper($job->type) }}

            </span>

        </div>


        <p class="text-sm text-gray-600 mb-4">
            {{ Str::limit($job->description, 100) }}
        </p>


        <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">

            <span>
                <i class="bi bi-people"></i>
                Kuota: {{ $job->quota_slots }}
            </span>

            <span>
                <i class="bi bi-calendar"></i>
                {{ \Carbon\Carbon::parse($job->start_date)->format('d M') }}
                -
                {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}
            </span>

        </div>


        <a href="{{ route('landing.show', $job->id) }}"
            class="block w-full text-center bg-gray-900 hover:bg-gray-800 text-white py-2 rounded-lg">

            Lihat Detail

        </a>

    </div>

</div>
