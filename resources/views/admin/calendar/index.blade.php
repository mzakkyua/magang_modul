@extends('layouts.admin')

@section('title', 'Manajemen Jadwal & Broadcast')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <style>
        :root {
            --fc-border-color: #f1f5f9;
            --fc-button-text-color: #475569;
            --fc-button-bg-color: #ffffff;
            --fc-button-border-color: #e2e8f0;
            --fc-button-hover-bg-color: #f8fafc;
            --fc-button-hover-border-color: #cbd5e1;
            --fc-button-active-bg-color: #f1f5f9;
            --fc-button-active-border-color: #cbd5e1;
            --fc-today-bg-color: #eff6ff;
        }

        #calendar {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 1.25rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            border: 1px solid #f1f5f9;
        }

        .fc-theme-standard .fc-scrollgrid {
            border: 1px solid #f1f5f9;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .fc-col-header-cell {
            background: #f8fafc;
            padding: 12px 0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .fc-daygrid-day:hover {
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
        }

        .fc-event {
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            border: none !important;
            font-size: 0.75rem;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: transform 0.1s;
        }

        .fc-event:hover {
            transform: scale(1.02);
            filter: brightness(0.95);
        }

        .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 900 !important;
            color: #1e293b !important;
        }

        .fc-button {
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            text-transform: capitalize !important;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active,
        .fc-button-primary:not(:disabled):active {
            color: #2563eb !important;
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
        }
    </style>
@endpush

@section('content')
    <div class="mb-6 flex flex-col lg:flex-row lg:items-end justify-between gap-5">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Broadcast Jadwal Kegiatan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan siarkan kegiatan secara global ke seluruh kalender peserta
                aktif.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 shrink-0">
            <div class="flex items-center gap-3 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Petunjuk:</span>
                <div class="flex items-center gap-1.5" title="Dibuat oleh Admin"><span
                        class="w-2.5 h-2.5 rounded-full bg-blue-500"></span><span
                        class="text-xs font-semibold text-gray-600">Umum</span></div>
                <div class="flex items-center gap-1.5" title="Kegiatan Opsional"><span
                        class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span
                        class="text-xs font-semibold text-gray-600">Opsional</span></div>
                <div class="flex items-center gap-1.5" title="Wajib/Ujian"><span
                        class="w-2.5 h-2.5 rounded-full bg-red-500"></span><span
                        class="text-xs font-semibold text-gray-600">Penting</span></div>
            </div>

            {{-- Perhatikan perubahan onclick di sini --}}
            <button onclick="triggerNewBroadcast()"
                class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-md shadow-blue-600/20 transition-all active:scale-95">
                <i class="bi bi-megaphone-fill"></i> Broadcast Baru
            </button>
        </div>
    </div>

    <div id="calendar" class="w-full"></div>

    {{-- MODAL NATIVE TAILWIND --}}
    <div id="eventModal" class="fixed inset-0 z-9999 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

            <div id="modalBackdrop" class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm opacity-0"
                aria-hidden="true" onclick="closeCalendarModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div id="modalPanel"
                class="inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 relative z-10">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-2" id="modal-title">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"><i
                                class="bi bi-broadcast"></i></div>
                        <span id="modalTitleText">Broadcast Kegiatan</span>
                    </h3>
                    <button type="button" onclick="closeCalendarModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center transition-colors">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="mb-5 bg-blue-50/50 border border-blue-100 rounded-xl p-3 flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-800 leading-relaxed font-medium">Kegiatan yang Anda simpan di sini akan
                        otomatis muncul dan dapat dilihat di kalender seluruh peserta magang yang sedang aktif.</p>
                </div>

                <form id="eventForm" class="space-y-4">
                    <input type="hidden" id="eventId">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Nama Kegiatan
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="eventTitle" required
                            class="w-full text-sm font-semibold border border-gray-300 rounded-xl px-4 py-2.5 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                            placeholder="Misal: Pembekalan Peserta Batch 1">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Tanggal
                                Mulai</label>
                            <input type="date" id="eventStart" required
                                class="w-full text-sm font-semibold border border-gray-300 rounded-xl px-4 py-2.5 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Selesai
                                <span class="text-gray-400 normal-case font-normal">(Opsional)</span></label>
                            <input type="date" id="eventEnd"
                                class="w-full text-sm font-semibold border border-gray-300 rounded-xl px-4 py-2.5 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Kategori /
                            Urgensi</label>
                        <select id="eventColor"
                            class="w-full text-sm font-bold border border-gray-300 rounded-xl px-4 py-2.5 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
                            <option value="#3b82f6" class="text-blue-600">🔵 Informasi Umum</option>
                            <option value="#f59e0b" class="text-amber-500">🟡 Kegiatan Opsional</option>
                            <option value="#ef4444" class="text-red-500">🔴 Sangat Penting / Ujian</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Keterangan
                            Tambahan <span class="text-gray-400 normal-case font-normal">(Opsional)</span></label>
                        <textarea id="eventDesc" rows="3"
                            class="w-full text-sm border border-gray-300 rounded-xl px-4 py-2.5 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all resize-none"
                            placeholder="Tuliskan tempat, waktu spesifik (Pukul 09.00), atau tautan rapat..."></textarea>
                    </div>

                    <div class="mt-6 flex gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeCalendarModal()"
                            class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 active:bg-gray-100 transition-colors">Batal</button>
                        <button type="submit" id="btnSubmitEvent"
                            class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow-md shadow-blue-600/20 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                            <i class="bi bi-send-fill"></i> Broadcast Sekarang
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // =========================================================================
        // PENGGANTIAN NAMA FUNGSI AGAR TIDAK BENTROK DENGAN APP.JS
        // =========================================================================

        window.triggerNewBroadcast = function() {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById('eventStart').value = today;
            document.getElementById('eventEnd').value = '';
            window.openCalendarModal('Broadcast Kegiatan Baru');
        };

        window.openCalendarModal = function(titleText = 'Broadcast Kegiatan') {
            document.getElementById('modalTitleText').innerText = titleText;
            const modal = document.getElementById('eventModal');
            const backdrop = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');

            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        };

        window.closeCalendarModal = function() {
            const modal = document.getElementById('eventModal');
            const backdrop = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');
            const form = document.getElementById('eventForm');

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                if (form) form.reset();
                document.getElementById('eventId').value = '';
            }, 300);
        };

        // =========================================================================
        // INISIALISASI FULLCALENDAR
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const form = document.getElementById('eventForm');

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            if (!calendarEl) return;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 'auto',
                contentHeight: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                events: '{{ route('calendar.events') }}',
                selectable: true,
                editable: true,
                dayMaxEvents: 3,

                select: function(info) {
                    let start = info.startStr;
                    let end = '';

                    if (info.endStr) {
                        let endDateAdjusted = new Date(info.end.valueOf() - 1).toISOString().split('T')[
                            0];
                        if (start !== endDateAdjusted) {
                            end = endDateAdjusted;
                        }
                    }

                    document.getElementById('eventStart').value = start;
                    document.getElementById('eventEnd').value = end;
                    window.openCalendarModal('Broadcast Kegiatan Baru');
                    calendar.unselect();
                },

                eventClick: function(info) {
                    if (String(info.event.id).startsWith('internship-')) {
                        Swal.fire({
                            title: 'Jadwal Otomatis Sistem',
                            text: 'Jadwal hijau ini diekstrak otomatis dari tanggal masuk-keluar peserta magang. Anda tidak bisa menghapusnya secara manual dari kalender.',
                            icon: 'info',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    Swal.fire({
                        title: info.event.title,
                        html: `<p class="text-sm text-gray-600 mb-4">${info.event.extendedProps.description || 'Tidak ada keterangan tambahan.'}</p>
                               <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-200">
                                Mulai: <b>${info.event.startStr}</b><br>
                                Selesai: <b>${info.event.endStr || info.event.startStr}</b>
                               </div>`,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Tutup',
                        denyButtonText: '<i class="bi bi-trash"></i> Hapus Broadcast',
                        confirmButtonColor: '#94a3b8',
                        denyButtonColor: '#ef4444',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isDenied) {
                            Swal.fire({
                                title: 'Cabut Broadcast?',
                                text: "Kegiatan akan hilang dari kalender seluruh peserta.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Cabut!',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#ef4444',
                            }).then((deleteResult) => {
                                if (deleteResult.isConfirmed) {
                                    let actualId = String(info.event.id).replace(
                                        'global-', '');
                                    fetch(`/admin/calendar/${actualId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json'
                                        }
                                    }).then(async res => {
                                        if (!res.ok) throw new Error(
                                            "Gagal menghapus");
                                        info.event.remove();
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'success',
                                            title: 'Broadcast Dihapus',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    }).catch(err => {
                                        Swal.fire('Error', err.message,
                                            'error');
                                    });
                                }
                            });
                        }
                    });
                },

                eventDrop: function(info) {
                    if (String(info.event.id).startsWith('internship-')) {
                        info.revert();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Jadwal peserta tidak bisa digeser.',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        return;
                    }

                    let actualId = String(info.event.id).replace('global-', '');
                    fetch(`/admin/calendar/${actualId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: info.event.title,
                            start_date: info.event.startStr,
                            end_date: info.event.endStr || info.event.startStr,
                            color: info.event.backgroundColor,
                            description: info.event.extendedProps.description
                        })
                    }).then(async res => {
                        if (!res.ok) throw new Error("Gagal");
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: 'Jadwal Dipindahkan',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }).catch(err => {
                        info.revert();
                    });
                },

                eventResize: function(info) {
                    if (String(info.event.id).startsWith('internship-')) {
                        info.revert();
                        return;
                    }
                    let actualId = String(info.event.id).replace('global-', '');
                    fetch(`/admin/calendar/${actualId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: info.event.title,
                            start_date: info.event.startStr,
                            end_date: info.event.endStr || info.event.startStr,
                            color: info.event.backgroundColor,
                            description: info.event.extendedProps.description
                        })
                    }).catch(() => info.revert());
                }
            });

            calendar.render();

            // =========================================================================
            // 3. SUBMIT FORM MODAL (AJAX POST)
            // =========================================================================
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const btn = document.getElementById('btnSubmitEvent');
                    // Simpan state awal tombol
                    const oriText = btn.innerHTML;

                    // Spinner SVG animasi mulus standar Enterprise
                    const spinnerSVG =
                        `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

                    // Kunci tombol sekeras-kerasnya di sisi DOM agar mustahil di double-click
                    btn.disabled = true;
                    btn.innerHTML = spinnerSVG + '<span>Menyebarkan...</span>';

                    const payload = {
                        title: document.getElementById('eventTitle').value,
                        start_date: document.getElementById('eventStart').value,
                        end_date: document.getElementById('eventEnd').value,
                        color: document.getElementById('eventColor').value,
                        description: document.getElementById('eventDesc').value
                    };

                    fetch('{{ route('admin.calendar.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(async res => {
                            if (!res.ok) throw new Error("Gagal menyimpan data ke database.");
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                calendar.refetchEvents(); // Refresh data dari server
                                window.closeCalendarModal(); // Tutup modal
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Broadcast Berhasil Disebarkan!',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire('Gagal!', error.message, 'error');
                        })
                        .finally(() => {
                            // SETELAH REQUEST SELESAI (Entah sukses / error), kembalikan tombol seperti semula
                            btn.disabled = false;
                            btn.innerHTML = oriText;
                        });
                });
            }
        });
    </script>
@endpush
