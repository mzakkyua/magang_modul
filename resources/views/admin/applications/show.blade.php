@extends('layouts.admin')

@section('title', 'Detail Lamaran')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ================= LEFT SIDE ================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- POSISI DILAMAR --}}
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm uppercase tracking-wider font-bold mb-1">Posisi Dilamar</h3>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $application->vacancy->title }}
                </h2>

                <div class="mt-2 flex gap-3 text-sm">
                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">
                        <i class="bi bi-building mr-1"></i>
                        Divisi: {{ $application->vacancy->division_name }}
                    </span>

                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">
                        <i class="bi bi-calendar mr-1"></i>
                        Daftar: {{ \Carbon\Carbon::parse($application->created_at)->format('d M Y') }}
                    </span>

                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 uppercase font-bold text-xs">
                        Tipe: {{ $application->vacancy->type }}
                    </span>
                </div>
            </div>

            {{-- INFORMASI PENELITIAN (HANYA JIKA TIPE PENELITIAN) --}}
            @if ($application->vacancy->type === 'penelitian')
                <div class="bg-amber-50 border border-amber-200 p-6 rounded-lg shadow-sm">
                    <h3 class="text-amber-800 text-lg font-bold mb-4 flex items-center">
                        <i class="bi bi-journal-text mr-2"></i> Rencana Penelitian
                    </h3>

                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Judul Penelitian</h4>
                        <p class="text-gray-900 font-medium bg-white p-3 rounded border border-amber-100">
                            {{ $application->research_title ?? 'Tidak mencantumkan judul' }}</p>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Abstrak Singkat</h4>
                        <p
                            class="text-gray-800 bg-white p-4 rounded border border-amber-100 text-sm whitespace-pre-wrap leading-relaxed">
                            {{ $application->research_abstract ?? 'Tidak mencantumkan abstrak' }}</p>
                    </div>
                </div>
            @endif

            {{-- DATA PELAMAR --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    Data Pelamar / Anggota Kelompok
                </h3>

                @foreach ($application->members as $index => $member)
                    @php
                        $profile = $member->user->profile;
                        $isLeader = $member->user_id === $application->leader_user_id;
                    @endphp

                    <div
                        class="mb-6 p-5 border {{ $isLeader ? 'border-blue-200 bg-blue-50/30' : 'border-gray-200 bg-gray-50' }} rounded-xl">

                        {{-- Header Peserta --}}
                        <div class="flex items-start justify-between mb-4 border-b pb-3">
                            <div>
                                <h4 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                                    {{ $profile->full_name ?? $member->user->name }}
                                    @if ($isLeader)
                                        <span
                                            class="bg-blue-100 text-blue-700 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider">Ketua</span>
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="bi bi-envelope"></i> {{ $member->user->email }}
                                </p>
                            </div>
                        </div>

                        {{-- Grid Info --}}
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-4 gap-x-4 text-sm mb-5">
                            <div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase">NIM / NISN</span>
                                <div class="font-medium text-gray-800">{{ $profile->nim_nisn ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase">Instansi</span>
                                <div class="font-medium text-gray-800">{{ $profile->institution_name ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase">Jurusan</span>
                                <div class="font-medium text-gray-800">{{ $profile->major ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase">Jenjang</span>
                                <div class="font-medium text-gray-800">{{ $profile->education_level ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase">No. WhatsApp</span>
                                <div class="font-medium text-gray-800">
                                    @if ($profile && $profile->phone_number)
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $profile->phone_number)) }}"
                                            target="_blank" class="text-green-600 hover:underline flex items-center gap-1">
                                            <i class="bi bi-whatsapp"></i> {{ $profile->phone_number }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Dokumen Lampiran --}}
                        @if ($profile && ($profile->cv_file_path || $profile->proposal_file_path))
                            <div class="bg-white p-3 rounded border flex flex-wrap gap-3">
                                @if ($profile->cv_file_path)
                                    <a href="{{ Storage::url($profile->cv_file_path) }}" target="_blank"
                                        class="inline-flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded transition">
                                        <i class="bi bi-file-earmark-person-fill text-blue-500"></i> Lihat CV
                                    </a>
                                @endif

                                @if ($profile->proposal_file_path)
                                    <a href="{{ Storage::url($profile->proposal_file_path) }}" target="_blank"
                                        class="inline-flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded transition">
                                        <i class="bi bi-file-earmark-text-fill text-amber-500"></i> Lihat Proposal
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="text-xs text-red-500 italic">
                                <i class="bi bi-exclamation-circle"></i> Belum mengunggah dokumen (CV/Proposal).
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            {{-- CATATAN TAMBAHAN DARI PELAMAR --}}
            @if ($application->notes)
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-sm font-bold text-gray-800 mb-2 uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-chat-square-text"></i> Catatan Tambahan
                    </h3>
                    <p class="text-sm text-gray-600 italic bg-gray-50 p-4 rounded border">"{{ $application->notes }}"</p>
                </div>
            @endif

            {{-- TIMELINE PERJALANAN MAGANG (HANYA MUNCUL JIKA DITERIMA) --}}
            @if ($application->status === 'accepted')
                @php
                    $vacancy = $application->vacancy;
                    $now = \Carbon\Carbon::now();
                    $startDate = \Carbon\Carbon::parse($vacancy->start_date);
                    $endDate = \Carbon\Carbon::parse($vacancy->end_date);

                    $isStarted = $now->gte($startDate);
                    $isFinished = $now->gt($endDate);
                @endphp

                <div class="bg-white p-6 rounded-lg shadow mt-6 border-t-4 border-green-500">
                    <h3 class="text-lg font-bold text-gray-800 mb-8 flex items-center gap-2 border-b pb-3">
                        <i class="bi bi-geo-alt-fill text-green-500"></i> Pantauan Status Magang Pelamar
                    </h3>

                    <ol class="relative border-l-2 border-gray-200 ml-3">
                        {{-- STEP 1: DITERIMA --}}
                        <li class="mb-8 ml-6">
                            <div class="absolute w-4 h-4 bg-green-500 rounded-full -left-2.25 top-1 ring-4 ring-green-100">
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mb-1">Telah Diterima</h4>
                            <time class="block mb-1 text-xs font-bold text-gray-400">
                                <i class="bi bi-calendar-check mr-1"></i>
                                {{ \Carbon\Carbon::parse($application->updated_at)->format('d M Y') }}
                            </time>
                            <p class="text-xs text-gray-500">
                                Disetujui pada pukul {{ \Carbon\Carbon::parse($application->updated_at)->format('H:i') }}
                                WIB.
                            </p>
                        </li>

                        {{-- STEP 2: PERSIAPAN --}}
                        <li class="mb-8 ml-6">
                            <div
                                class="absolute w-4 h-4 {{ $isStarted ? 'bg-green-500 ring-green-100' : 'bg-blue-500 ring-blue-100 animate-pulse' }} rounded-full -left-2.25 top-1 ring-4">
                            </div>
                            <h4 class="text-sm font-bold {{ $isStarted ? 'text-gray-900' : 'text-blue-700' }} mb-1">
                                Persiapan Magang</h4>
                            <time class="block mb-1 text-xs font-bold text-gray-400">
                                <i class="bi bi-clock-history mr-1"></i> Target: {{ $startDate->format('d M Y') }}
                            </time>
                            <p class="text-xs text-gray-500">
                                @if ($isStarted)
                                    Masa persiapan telah dilewati.
                                @else
                                    Pelamar sedang dalam masa tunggu sebelum magang dimulai.
                                @endif
                            </p>
                        </li>

                        {{-- STEP 3: PELAKSANAAN --}}
                        <li class="mb-8 ml-6">
                            <div
                                class="absolute w-4 h-4 {{ $isFinished ? 'bg-green-500 ring-green-100' : ($isStarted ? 'bg-blue-500 ring-blue-100 animate-pulse' : 'bg-gray-200 ring-white') }} rounded-full -left-2.25 top-1 ring-4">
                            </div>
                            <h4
                                class="text-sm font-bold {{ $isFinished ? 'text-gray-900' : ($isStarted ? 'text-blue-700' : 'text-gray-400') }} mb-1">
                                Pelaksanaan Magang Aktif</h4>
                            <time class="block mb-1 text-xs font-bold text-gray-400">
                                <i class="bi bi-calendar-range mr-1"></i> {{ $startDate->format('d M') }} s/d
                                {{ $endDate->format('d M Y') }}
                            </time>
                            <p class="text-xs {{ $isStarted && !$isFinished ? 'text-gray-600' : 'text-gray-400' }}">
                                @if ($isFinished)
                                    Telah menyelesaikan masa magang.
                                @elseif($isStarted)
                                    Sedang aktif magang di divisi terkait.
                                @else
                                    Belum dimulai.
                                @endif
                            </p>
                        </li>

                        {{-- STEP 4: SELESAI --}}
                        <li class="ml-6">
                            <div
                                class="absolute w-4 h-4 {{ $isFinished ? 'bg-blue-500 ring-blue-100 animate-pulse' : 'bg-gray-200 ring-white' }} rounded-full -left-2.25 top-1 ring-4">
                            </div>
                            <h4 class="text-sm font-bold {{ $isFinished ? 'text-blue-700' : 'text-gray-400' }} mb-1">
                                Selesai & Penilaian</h4>
                            <time class="block mb-1 text-xs font-bold text-gray-400">
                                <i class="bi bi-flag mr-1"></i> Mulai: {{ $endDate->format('d M Y') }}
                            </time>
                            <p class="text-xs text-gray-400">
                                @if ($isFinished)
                                    Peserta siap dinilai. Silakan buka menu <strong>Penilaian</strong>.
                                @else
                                    Menunggu masa magang berakhir untuk melakukan penilaian.
                                @endif
                            </p>
                        </li>
                    </ol>
                </div>
            @endif

        </div>

        {{-- ================================================================
        RIGHT SIDEBAR: KEPUTUSAN VERIFIKASI
        ================================================================ --}}

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden sticky top-0">

                {{-- ── HEADER ── --}}
                <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="bi bi-shield-check text-blue-500 text-sm"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-gray-900">Keputusan Verifikasi</h3>
                </div>

                <div class="p-5 space-y-5">

                    {{-- ── STATUS SAAT INI ── --}}
                    <div
                        class="rounded-xl border p-4 text-center
                        {{ $application->status === 'accepted' ? 'bg-emerald-50 border-emerald-200' : '' }}
                        {{ $application->status === 'rejected' ? 'bg-red-50 border-red-200' : '' }}
                        {{ $application->status === 'resigned' ? 'bg-orange-50 border-orange-200' : '' }}
                        {{ $application->status === 'completed' ? 'bg-teal-50 border-teal-200' : '' }}
                        {{ in_array($application->status, ['pending', 'verified', 'interview']) ? 'bg-amber-50 border-amber-200' : '' }}">

                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                            Status Saat Ini
                        </p>

                        @php
                            $statusConfig = [
                                'pending' => [
                                    'label' => 'Menunggu',
                                    'color' => 'text-amber-600',
                                    'dot' => 'bg-amber-500',
                                ],
                                'verified' => [
                                    'label' => 'Terverifikasi',
                                    'color' => 'text-blue-600',
                                    'dot' => 'bg-blue-500',
                                ],
                                'interview' => [
                                    'label' => 'Wawancara',
                                    'color' => 'text-indigo-600',
                                    'dot' => 'bg-indigo-500',
                                ],
                                'accepted' => [
                                    'label' => 'Diterima',
                                    'color' => 'text-emerald-600',
                                    'dot' => 'bg-emerald-500',
                                ],
                                'rejected' => ['label' => 'Ditolak', 'color' => 'text-red-600', 'dot' => 'bg-red-500'],
                                'resigned' => [
                                    'label' => 'Mengundurkan Diri',
                                    'color' => 'text-orange-600',
                                    'dot' => 'bg-orange-500',
                                ],
                                'completed' => [
                                    'label' => 'Selesai',
                                    'color' => 'text-teal-600',
                                    'dot' => 'bg-teal-500',
                                ],
                            ];
                            $cfg = $statusConfig[$application->status] ?? [
                                'label' => strtoupper($application->status),
                                'color' => 'text-gray-600',
                                'dot' => 'bg-gray-400',
                            ];
                        @endphp

                        <div class="flex items-center justify-center gap-2">
                            <span
                                class="w-2 h-2 rounded-full {{ $cfg['dot'] }} {{ in_array($application->status, ['pending', 'verified', 'interview']) ? 'animate-pulse' : '' }}"></span>
                            <span class="text-lg font-extrabold {{ $cfg['color'] }}">
                                {{ $cfg['label'] }}
                            </span>
                        </div>

                        {{-- Catatan admin jika ada --}}
                        @if ($application->admin_feedback)
                            <div class="mt-3 text-left bg-white/60 border border-red-100 rounded-lg px-3 py-2">
                                <p class="text-[10px] font-bold text-red-400 uppercase tracking-wider mb-0.5">
                                    Catatan Admin
                                </p>
                                <p class="text-xs text-red-600 leading-relaxed">
                                    {{ $application->admin_feedback }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- ── BAGIAN 1: AKSI SELANJUTNYA ── --}}
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">
                            Aksi Selanjutnya
                        </p>

                        @if ($application->status === 'pending')
                            <form action="{{ route('admin.applications.update-status', $application->id) }}"
                                method="POST" class="form-approve action-form"
                                data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="verified">
                                <button type="submit"
                                    class="action-btn w-full flex items-center justify-center gap-2 bg-blue-50 text-blue-600 border border-blue-200 font-bold py-2.5 rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:shadow-md hover:shadow-blue-600/25 transition-all duration-200 text-sm">
                                    <i class="bi bi-patch-check"></i> Verifikasi Dokumen
                                </button>
                            </form>
                        @elseif ($application->status === 'verified')
                            <form action="{{ route('admin.applications.update-status', $application->id) }}"
                                method="POST" class="action-form"
                                data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="interview">
                                <button type="submit"
                                    class="action-btn w-full flex items-center justify-center gap-2 bg-indigo-50 text-indigo-600 border border-indigo-200 font-bold py-2.5 rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 hover:shadow-md hover:shadow-indigo-600/25 transition-all duration-200 text-sm">
                                    <i class="bi bi-camera-video"></i> Panggil Wawancara
                                </button>
                            </form>
                        @elseif ($application->status === 'interview')
                            <form action="{{ route('admin.applications.update-status', $application->id) }}"
                                method="POST" class="form-approve action-form"
                                data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit"
                                    class="action-btn w-full flex items-center justify-center gap-2 bg-emerald-600 text-white font-bold py-2.5 rounded-xl hover:bg-emerald-700 shadow-md shadow-emerald-600/25 hover:shadow-emerald-600/40 transition-all duration-200 text-sm">
                                    <i class="bi bi-check-circle-fill"></i> Terima Pemagang
                                </button>
                            </form>
                        @elseif ($application->status === 'accepted')
                            @php
                                $memberIds = $application->members->pluck('id');
                                $totalMembers = $memberIds->count();
                                $dinilaiCount = \App\Models\AssessmentMagang::whereIn('member_id', $memberIds)->count();
                                $semuaDinilai = $dinilaiCount >= $totalMembers;
                                $belumDinilai = $totalMembers - $dinilaiCount;
                            @endphp

                            @if ($semuaDinilai)
                                <form action="{{ route('admin.applications.update-status', $application->id) }}"
                                    method="POST" id="form-complete-action">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="button" onclick="confirmSelesaiMagang()"
                                        class="w-full flex items-center justify-center gap-2 bg-teal-600 text-white font-bold py-2.5 rounded-xl hover:bg-teal-700 shadow-md shadow-teal-600/25 hover:shadow-teal-600/40 transition-all duration-200 text-sm">
                                        <i class="bi bi-award-fill"></i> Selesaikan Magang
                                    </button>
                                </form>
                            @else
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="bi bi-exclamation-triangle-fill text-amber-500 text-sm"></i>
                                        <p class="text-xs font-bold text-amber-700">Penilaian Belum Lengkap</p>
                                    </div>
                                    <p class="text-[11px] text-amber-600 leading-relaxed mb-3">
                                        Masih <strong>{{ $belumDinilai }} peserta</strong> belum dinilai.
                                    </p>
                                    <a href="{{ route('admin.assessments.index') }}"
                                        class="w-full flex items-center justify-center gap-2 bg-amber-500 text-white font-bold py-2 rounded-xl hover:bg-amber-600 transition-all duration-200 text-xs">
                                        <i class="bi bi-pencil-square"></i> Buka Penilaian
                                    </a>
                                </div>
                                <button type="button" disabled
                                    class="mt-2 w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-400 font-bold py-2.5 rounded-xl cursor-not-allowed text-sm border border-gray-200">
                                    <i class="bi bi-lock-fill text-xs"></i> Selesaikan Magang
                                </button>
                            @endif
                        @endif {{-- INI ADALAH @endif YANG SEBELUMNYA HILANG --}}

                        {{-- ── TOLAK (muncul selama belum rejected / completed) ── --}}
                        @if (!in_array($application->status, ['rejected', 'completed', 'resigned']))
                            <form action="{{ route('admin.applications.update-status', $application->id) }}"
                                method="POST" class="form-reject action-form mt-2"
                                data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <input type="hidden" name="admin_feedback">
                                <button type="submit"
                                    class="action-btn w-full flex items-center justify-center gap-2 bg-white text-red-500 border border-red-200 font-bold py-2.5 rounded-xl hover:bg-red-600 hover:text-white hover:border-red-600 hover:shadow-md hover:shadow-red-600/20 transition-all duration-200 text-sm">
                                    <i class="bi bi-x-circle"></i> Tolak Lamaran
                                </button>
                            </form>
                        @endif

                        {{-- ── RESIGN ── --}}
                        @if ($application->status === 'accepted')
                            <div class="mt-2">
                                <form action="{{ route('admin.applications.update-status', $application->id) }}"
                                    method="POST" id="form-resign-intern-action" class="form-resign-intern"
                                    data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="resigned">
                                    <button type="button" id="btn-trigger-resign"
                                        class="w-full flex items-center justify-center gap-2 bg-white text-orange-500 border border-orange-200 font-bold py-2.5 rounded-xl hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-all duration-200 text-sm">
                                        <i class="bi bi-person-walking"></i> Mengundurkan Diri
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- ── BAGIAN 2: OVERRIDE MANUAL ── --}}
                    <div class="pt-5 border-t border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">
                            Ubah Status Manual
                        </p>

                        <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST"
                            class="flex gap-2">
                            @csrf @method('PATCH')
                            <select name="status"
                                class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 bg-white outline-none appearance-none cursor-pointer hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-position-[right_10px_center] bg-no-repeat bg-size-[14px]">
                                <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="verified" {{ $application->status == 'verified' ? 'selected' : '' }}>
                                    Verified</option>
                                <option value="interview" {{ $application->status == 'interview' ? 'selected' : '' }}>
                                    Interview</option>
                                <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>
                                    Accepted</option>
                                <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>
                                    Rejected</option>
                                <option value="resigned" {{ $application->status == 'resigned' ? 'selected' : '' }}>
                                    Resigned</option>
                                <option value="completed" {{ $application->status == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                            </select>
                            <button type="submit"
                                class="bg-gray-900 hover:bg-gray-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 shrink-0">
                                Ubah
                            </button>
                        </form>
                        <p class="text-[10px] text-gray-400 mt-2 leading-relaxed">
                            Gunakan jika ada pembatalan, kesalahan sistem, atau kondisi di luar alur normal.
                        </p>
                    </div>

                    {{-- ── KEMBALI ── --}}
                    <div class="pt-4 border-t border-gray-50 text-center">
                        <a href="{{ route('admin.applications.index') }}"
                            class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-blue-600 font-medium transition-colors duration-150">
                            <i class="bi bi-arrow-left text-xs"></i> Kembali ke Daftar Lamaran
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/delete-confirm.js'])

    {{-- Script khusus untuk Selesaikan Magang --}}
    <script>
        function confirmSelesaiMagang() {
            Swal.fire({
                title: "Selesaikan Magang?",
                text: "Status pemagang akan diubah menjadi Selesai. Pastikan semua penilaian sudah benar.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#0f766e", // Warna teal-700
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Selesaikan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Bypass semua script JS lain dan submit langsung
                    document.getElementById('form-complete-action').submit();
                }
            });
        }
    </script>
@endpush
