@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Kegiatan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <style>
        #calendar {
            font-family: 'Inter', sans-serif;
            background: white;
            padding: 20px;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .fc-theme-standard .fc-scrollgrid {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .fc-col-header-cell {
            background: #f8fafc;
            padding: 10px 0;
            color: #475569;
        }

        .fc-daygrid-day:hover {
            background: #f1f5f9;
            cursor: pointer;
        }

        .fc-event {
            cursor: pointer;
            padding: 3px 5px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Jadwal Kegiatan</h1>
        <p class="text-gray-600">Klik pada tanggal untuk menambah acara, atau geser acara untuk memindahkan tanggal.</p>
    </div>

    <div id="calendar"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: '{{ route('calendar.events') }}',
                selectable: true,
                editable: true,

                // 1. KETIKA TANGGAL DIKLIK (TAMBAH ACARA)
                select: async function(info) {
                    // Mengakali "Jebakan" FullCalendar agar end-date-nya sesuai
                    let endDateAdjusted = new Date(info.end.valueOf() - 1).toISOString().split('T')[0];

                    const {
                        value: formValues
                    } = await Swal.fire({
                        title: 'Tambah Kegiatan Baru',
                        width: '32rem', // Ukuran pop-up yang lebih proporsional (max-w-lg)
                        // KITA GUNAKAN TAILWIND MURNI DI SINI
                        html: `
                            <div class="text-left space-y-5 mt-4">
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                                    <input id="swal-title" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow text-gray-800" placeholder="Misal: Onboarding Peserta">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                        <input id="swal-start" type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" value="${info.startStr}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                                        <input id="swal-end" type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" value="${endDateAdjusted}">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Warna</label>
                                    <select id="swal-color" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 bg-white">
                                        <option value="#3b82f6">🔵 Biru (Umum)</option>
                                        <option value="#10b981">🟢 Hijau (Penting)</option>
                                        <option value="#f59e0b">🟡 Kuning (Opsional)</option>
                                        <option value="#ef4444">🔴 Merah (Ujian/Evaluasi)</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Tambahan</label>
                                    <textarea id="swal-desc" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 resize-none" placeholder="Ketik deskripsi singkat di sini..."></textarea>
                                </div>

                            </div>
                        `,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        // Styling tombol bawaan SweetAlert agar senada dengan Tailwind
                        customClass: {
                            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors border-0',
                            cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg transition-colors border-0'
                        },
                        buttonsStyling: false, // Matikan style tombol bawaan SweetAlert
                        preConfirm: () => {
                            const title = document.getElementById('swal-title').value;
                            if (!title) {
                                Swal.showValidationMessage('Nama kegiatan wajib diisi!');
                                return false;
                            }
                            return {
                                title: title,
                                description: document.getElementById('swal-desc').value,
                                color: document.getElementById('swal-color').value,
                                start_date: document.getElementById('swal-start').value,
                                end_date: document.getElementById('swal-end').value
                            }
                        }
                    });

                    if (formValues && formValues.title) {
                        fetch('{{ route('admin.calendar.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify(formValues)
                            })
                            .then(async res => {
                                if (!res.ok) {
                                    const errData = await res.json();
                                    throw new Error(errData.message ||
                                        "Gagal menyimpan ke database");
                                }
                                return res.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    calendar.refetchEvents();
                                    Swal.fire('Berhasil!', 'Kegiatan ditambahkan.', 'success');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Gagal Menyimpan!', error.message, 'error');
                            });
                    }
                    calendar.unselect();
                },

                // 2. KETIKA ACARA DIKLIK (EDIT / HAPUS)
                eventClick: function(info) {
                    // CEK PINTAR: Apakah ini event magang otomatis (warna hijau)?
                    if (String(info.event.id).startsWith('internship-')) {
                        Swal.fire({
                            title: 'Jadwal Otomatis',
                            text: 'Jadwal ini dibuat otomatis oleh sistem untuk peserta magang. Anda tidak bisa menghapusnya dari sini. Silakan ubah status pemagang menjadi "Mundur" atau "Ditolak" di menu Verifikasi.',
                            icon: 'warning',
                            confirmButtonText: 'Paham',
                            confirmButtonColor: '#3b82f6' // Warna biru
                        });
                        return; // Hentikan proses di sini, jangan munculkan tombol hapus!
                    }

                    // Jika ini jadwal biasa (biru, merah, kuning), munculkan pop-up hapus
                    Swal.fire({
                        title: info.event.title,
                        text: info.event.extendedProps.description || 'Tidak ada deskripsi',
                        icon: 'info',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Tutup',
                        denyButtonText: 'Hapus Kegiatan',
                        denyButtonColor: '#dc2626'
                    }).then((result) => {
                        if (result.isDenied) {
                            Swal.fire({
                                title: 'Yakin hapus?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Hapus!'
                            }).then((deleteResult) => {
                                if (deleteResult.isConfirmed) {
                                    let actualId = String(info.event.id).replace(
                                        'global-', '');
                                    fetch(`/admin/calendar/${actualId}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json' // Beritahu Laravel kita butuh JSON
                                            }
                                        })
                                        // TAMBAHAN KODE PINTAR: Tangkap error dari server
                                        .then(async res => {
                                            if (!res.ok) {
                                                throw new Error(
                                                    `Gagal menghapus! (Error Code: ${res.status}) - Pastikan URL route benar.`
                                                );
                                            }
                                            return res.json();
                                        })
                                        .then(data => {
                                            if (data.success) {
                                                info.event.remove();
                                                Swal.fire('Terhapus!',
                                                    'Kegiatan berhasil dihapus.',
                                                    'success');
                                            }
                                        })
                                        .catch(err => {
                                            // Munculkan error kalau URL salah atau data tidak ada
                                            Swal.fire('Error Backend', err.message,
                                                'error');
                                        });
                                }
                            });
                        }
                    });
                },

                // 3. KETIKA ACARA DIGESER / DRAG (UPDATE TANGGAL)
                eventDrop: function(info) {
                    // Bersihkan kata 'global-' dari ID
                    let actualId = String(info.event.id).replace('global-', '');

                    fetch(`/admin/calendar/${actualId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            title: info.event.title,
                            start_date: info.event.startStr,
                            end_date: info.event.endStr ? info.event.endStr : info.event
                                .startStr,
                            color: info.event.backgroundColor,
                            description: info.event.extendedProps.description
                        })
                    }).then(res => {
                        if (!res.ok) {
                            info.revert();
                            Swal.fire('Error', 'Gagal memindahkan jadwal.', 'error');
                        }
                    });
                }
            });

            calendar.render();
        });
    </script>
@endpush
