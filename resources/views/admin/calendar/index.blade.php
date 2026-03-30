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
                events: '{{ route('calendar.events') }}', // Ambil data
                selectable: true, // Bisa klik tanggal
                editable: true, // Bisa drag and drop

                // 1. KETIKA TANGGAL DIKLIK (TAMBAH ACARA)
                select: async function(info) {
                    const {
                        value: formValues
                    } = await Swal.fire({
                        title: 'Tambah Kegiatan Baru',
                        html: `<input id="swal-title" class="swal2-input" placeholder="Nama Kegiatan (Misal: Onboarding)">` +
                            `<textarea id="swal-desc" class="swal2-textarea" placeholder="Deskripsi Singkat"></textarea>` +
                            `<select id="swal-color" class="swal2-select">
                            <option value="#3b82f6">Biru (Umum)</option>
                            <option value="#10b981">Hijau (Penting)</option>
                            <option value="#f59e0b">Kuning (Opsional)</option>
                            <option value="#ef4444">Merah (Ujian/Evaluasi)</option>
                        </select>`,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            return {
                                title: document.getElementById('swal-title').value,
                                description: document.getElementById('swal-desc').value,
                                color: document.getElementById('swal-color').value,
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
                            body: JSON.stringify({
                                title: formValues.title,
                                description: formValues.description,
                                color: formValues.color,
                                start_date: info.startStr,
                                end_date: info
                                    .endStr // Fullcalendar otomatis +1 hari untuk blok selection
                            })
                        }).then(res => res.json()).then(data => {
                            if (data.success) {
                                calendar.refetchEvents();
                                Swal.fire('Berhasil!', 'Kegiatan ditambahkan.', 'success');
                            }
                        });
                    }
                    calendar.unselect();
                },

                // 2. KETIKA ACARA DIKLIK (EDIT / HAPUS)
                eventClick: function(info) {
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
                                    fetch(`/admin/calendar/${info.event.id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken
                                        }
                                    }).then(res => res.json()).then(data => {
                                        if (data.success) {
                                            info.event.remove();
                                            Swal.fire('Terhapus!', '',
                                                'success');
                                        }
                                    });
                                }
                            });
                        }
                    });
                },

                // 3. KETIKA ACARA DIGESER / DRAG (UPDATE TANGGAL)
                eventDrop: function(info) {
                    fetch(`/admin/calendar/${info.event.id}`, {
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
