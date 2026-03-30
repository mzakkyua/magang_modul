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
                        Daftar: {{ \Carbon\Carbon::parse($application->submission_date)->format('d M Y') }}
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

                    {{-- MENGGUNAKAN LIST AGAR ANTI NABRAK & LEBIH RAPI --}}
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

        {{-- ================= RIGHT SIDE ================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Keputusan Verifikasi
                </h3>

                {{-- STATUS SAAT INI --}}
                <div class="mb-5 text-center p-4 bg-gray-50 rounded border">
                    <p class="text-xs text-gray-500 uppercase">Status Saat Ini</p>

                    <h2
                        class="text-xl font-bold
                    {{ $application->status === 'accepted' ? 'text-green-600' : '' }}
                    {{ $application->status === 'rejected' ? 'text-red-600' : '' }}
                    {{ in_array($application->status, ['pending', 'verified', 'interview']) ? 'text-yellow-600' : '' }}">
                        {{ strtoupper($application->status) }}
                    </h2>

                    @if ($application->admin_feedback)
                        <div class="mt-2 text-xs bg-red-50 text-red-700 p-2 rounded border">
                            <strong>Catatan Admin:</strong><br>
                            {{ $application->admin_feedback }}
                        </div>
                    @endif
                </div>

                {{-- ======== APPROVE ======== --}}
                @if ($application->status !== 'accepted')
                    <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST"
                        class="form-approve mb-3 action-form"
                        data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="accepted">

                        <button type="submit"
                            class="action-btn w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded shadow-sm transition">
                            ✅ TERIMA LAMARAN
                        </button>
                    </form>
                @else
                    <button disabled
                        class="w-full bg-gray-200 text-gray-500 font-bold py-2 rounded shadow-inner mb-3 cursor-not-allowed">
                        ✅ SUDAH DITERIMA
                    </button>
                @endif

                {{-- ======== REJECT ======== --}}
                @if ($application->status !== 'rejected')
                    <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST"
                        class="form-reject action-form"
                        data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="admin_feedback">

                        <button type="submit"
                            class="action-btn w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded shadow-sm transition">
                            ❌ TOLAK LAMARAN
                        </button>
                    </form>
                @else
                    <button disabled
                        class="w-full bg-gray-200 text-gray-500 font-bold py-2 rounded shadow-inner cursor-not-allowed">
                        ❌ SUDAH DITOLAK
                    </button>
                @endif

                <div class="mt-4 pt-4 border-t text-center">
                    <a href="{{ route('admin.applications.index') }}"
                        class="text-sm text-gray-500 hover:text-blue-600 font-medium transition">
                        ← Kembali ke Daftar Lamaran
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/delete-confirm.js'])
@endpush
