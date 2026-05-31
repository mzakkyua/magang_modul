{{--
    PARTIAL: vacancy-modal.blade.php
    Modal konfirmasi & form lamaran magang.

    Variabel yang WAJIB tersedia di scope pemanggil:
      $vacancy      — VacancyMagang model
      $accentColor  — string hex warna aksen
      $isTypeMagang — bool
--}}

@if (Auth::guard('magang')->check())

    <div id="applicationModal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/60 backdrop-blur-sm">
        <div class="relative w-full max-w-lg max-h-full mx-auto mt-10">
            <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4"
                    style="background: linear-gradient(135deg, {{ $accentColor }}, {{ $isTypeMagang ? '#1d4ed8' : '#7c3aed' }})">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="bi bi-send text-white text-sm"></i>
                        </div>
                        <h3 class="text-base font-bold text-white">Konfirmasi Lamaran</h3>
                    </div>
                    <button type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-red-500 text-white transition"
                        data-modal-hide="applicationModal">
                        <i class="bi bi-x-lg text-sm"></i>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6">

                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 flex items-start gap-3">
                        <i class="bi bi-info-circle-fill text-blue-500 mt-0.5 shrink-0"></i>
                        <p class="text-sm text-blue-800 leading-relaxed">
                            Anda akan melamar untuk posisi <strong>{{ $vacancy->title }}</strong>.
                            Pastikan profil dan CV Anda sudah diperbarui sebelum mengirim lamaran.
                        </p>
                    </div>

                    <form id="form-lamaran" action="{{ route('applications.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <input type="hidden" name="vacancy_updated_at" value="{{ $vacancy->updated_at }}">
                        <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">

                        {{-- ---- Khusus jalur Penelitian ---- --}}
                        @if ($vacancy->type === 'penelitian')
                            <div>
                                <label for="research_title" class="block mb-1.5 text-sm font-semibold text-gray-700">
                                    Judul Penelitian <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="research_title" name="research_title" maxlength="255" required
                                    class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    placeholder="Masukkan rencana judul penelitian Anda">
                                <p id="title_counter" class="text-xs text-gray-400 text-right mt-1">0 / 255 karakter</p>
                            </div>

                            <div>
                                <label for="research_abstract" class="block mb-1.5 text-sm font-semibold text-gray-700">
                                    Abstrak Singkat <span class="text-red-500">*</span>
                                </label>
                                <textarea id="research_abstract" name="research_abstract" rows="4" maxlength="1000" required
                                    class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none"
                                    placeholder="Jelaskan latar belakang dan tujuan penelitian Anda..."></textarea>
                                <p id="abstract_counter" class="text-xs text-gray-400 text-right mt-1">0 / 1000 karakter
                                </p>
                            </div>
                        @endif

                        {{-- ---- Input anggota kelompok / hybrid ---- --}}
                        @if (in_array($vacancy->registration_mode, ['kelompok', 'hybrid']))

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">

                                @if ($vacancy->registration_mode === 'hybrid')
                                    <div class="mb-4">
                                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Daftar
                                            Sebagai:</label>
                                        <select id="hybrid-mode-select"
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="individu">Individu (Sendiri)</option>
                                            <option value="kelompok">Kelompok (Tim)</option>
                                        </select>
                                    </div>
                                @endif

                                <div id="group-input-area" @class([
                                    'hidden' => $vacancy->registration_mode === 'hybrid',
                                ])>
                                    <label class="block mb-1 text-sm font-semibold text-gray-700">
                                        Anggota Kelompok (Email)
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">
                                        Min {{ $vacancy->min_members }} — Maks {{ $vacancy->max_members }} orang
                                        (termasuk Anda sebagai Ketua).
                                    </p>

                                    <div id="members-container" class="space-y-2">
                                        <div class="flex gap-2 member-input">
                                            <input type="email" name="member_emails[]"
                                                {{ $vacancy->registration_mode === 'kelompok' ? 'required' : '' }}
                                                {{ $vacancy->registration_mode === 'hybrid' ? 'disabled' : '' }}
                                                class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                                placeholder="email.anggota1@contoh.com">
                                        </div>
                                    </div>

                                    <button type="button" id="add-member-btn"
                                        class="mt-3 text-xs text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1.5 transition">
                                        <i class="bi bi-plus-circle-fill"></i> Tambah Anggota Lainnya
                                    </button>
                                    <p class="text-[10px] text-gray-400 mt-2">
                                        * Pastikan email anggota sudah terdaftar di sistem.
                                    </p>
                                </div>

                            </div>

                        @endif

                        {{-- ---- Catatan opsional ---- --}}
                        <div>
                            <label for="notes" class="block mb-1.5 text-sm font-semibold text-gray-700">
                                Catatan Singkat
                                <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea id="notes" name="notes" rows="2"
                                class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none"
                                placeholder="Kenapa Anda tertarik dengan posisi ini?"></textarea>
                        </div>

                        {{-- ---- Submit ---- --}}
                        <button type="submit" id="btn-submit"
                            class="w-full text-white font-bold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-md"
                            style="background: linear-gradient(135deg, {{ $accentColor }}, {{ $isTypeMagang ? '#1d4ed8' : '#7c3aed' }})">
                            <i class="bi bi-send-fill"></i>
                            <span id="btn-text">Kirim Lamaran</span>
                        </button>

                    </form>
                </div>{{-- end body --}}

            </div>
        </div>
    </div>

@endif
