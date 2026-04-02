{{-- ===================== CALENDAR COMPONENT ===================== --}}
{{-- Logic: FullCalendar v6, changeView(), calendar.events tidak diubah --}}

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* ===================== SECTION: BASE RESET ===================== */
    #calendar {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        transition: all 0.4s ease;
    }

    /* Hilangkan border luar yang kaku */
    .fc-theme-standard .fc-scrollgrid {
        border: none !important;
    }

    /* ===================== SECTION: HEADER TOOLBAR ===================== */
    .fc-header-toolbar {
        margin-bottom: 1.25rem !important;
        align-items: center !important;
    }

    /* Judul bulan (contoh: "Juni 2025") */
    .fc-toolbar-title {
        font-size: 1rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        letter-spacing: -0.01em;
    }

    /* ===================== SECTION: NAVIGASI PREV/NEXT ===================== */
    .fc-button-group {
        gap: 4px;
        display: flex !important;
    }

    .fc-button-group>.fc-button,
    .fc-button-primary {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        padding: 6px 10px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
        margin: 0 2px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        transition: all 0.15s ease !important;
    }

    .fc-button-group>.fc-button:hover,
    .fc-button-primary:hover {
        background-color: #e0e7ff !important;
        border-color: #c7d2fe !important;
        color: #2563eb !important;
    }

    .fc-button-primary:not(:disabled):active,
    .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: white !important;
    }

    .fc-button-primary:disabled {
        opacity: 0.35 !important;
    }

    /* ===================== SECTION: HEADER HARI (MIN, SEN...) ===================== */
    .fc-col-header-cell-cushion {
        padding: 12px 0 10px !important;
        text-transform: uppercase;
        font-size: 0.65rem !important;
        letter-spacing: 0.12em;
        font-weight: 800 !important;
        color: #94a3b8 !important;
        text-decoration: none !important;
    }

    /* Border header */
    .fc-theme-standard th {
        border: none !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    /* ===================== SECTION: SEL TANGGAL ===================== */
    .fc-theme-standard td {
        border: 1px solid #f1f5f9 !important;
        transition: background-color 0.15s;
    }

    .fc-daygrid-day-number {
        font-weight: 600;
        font-size: 0.8rem !important;
        color: #475569 !important;
        text-decoration: none !important;
        padding: 8px 10px !important;
        line-height: 1;
    }

    /* Hover effect */
    .fc-daygrid-day:hover {
        background-color: #f8fafc !important;
        cursor: pointer;
    }

    /* Hari di luar bulan ini (lebih pudar) */
    .fc-day-other .fc-daygrid-day-number {
        color: #cbd5e1 !important;
        font-weight: 400 !important;
    }

    /* ===================== SECTION: HIGHLIGHT HARI INI ===================== */
    .fc-day-today {
        background-color: #eff6ff !important;
        position: relative;
    }

    .fc-day-today .fc-daygrid-day-number {
        color: #2563eb !important;
        font-weight: 800 !important;
        position: relative;
    }

    /* Titik biru kecil di bawah angka hari ini */
    .fc-day-today .fc-daygrid-day-number::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        background-color: #2563eb;
        border-radius: 50%;
    }

    /* ===================== SECTION: EVENTS ===================== */
    .fc-event {
        border-radius: 6px !important;
        border: none !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        padding: 2px 6px !important;
        cursor: pointer;
        transition: opacity 0.15s;
    }

    .fc-event:hover {
        opacity: 0.85;
    }

    .fc-event-title {
        font-weight: 600 !important;
    }

    /* ===================== SECTION: MODE COMPACT (DEFAULT) ===================== */
    .compact-mode .fc-daygrid-day-number {
        font-size: 0.78rem !important;
        padding: 8px 10px !important;
    }

    .compact-mode .fc-col-header-cell-cushion {
        font-size: 0.62rem !important;
    }

    /* ===================== SECTION: MODE ZOOM IN (DETAILED) ===================== */
    .detailed-mode .fc-daygrid-day-number {
        font-size: 1.1rem !important;
        padding: 16px 12px !important;
        font-weight: 700 !important;
    }

    .detailed-mode .fc-col-header-cell-cushion {
        font-size: 0.8rem !important;
        padding: 16px 0 12px !important;
    }

    /* ===================== SECTION: MORE EVENTS LINK ===================== */
    .fc-more-link {
        color: #2563eb !important;
        font-size: 0.7rem !important;
        font-weight: 700 !important;
        padding: 2px 6px !important;
        border-radius: 6px;
        background-color: #eff6ff;
        text-decoration: none !important;
    }

    .fc-more-link:hover {
        background-color: #dbeafe !important;
    }

    /* ===================== SECTION: POPOVER ===================== */
    .fc-popover {
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
    }

    .fc-popover-header {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 10px 14px !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        color: #374151 !important;
    }

    .fc-popover-close {
        color: #94a3b8 !important;
    }
</style>

{{-- ===================== CALENDAR ELEMENT ===================== --}}
<div id="calendar"></div>

{{-- ===================== SCRIPT: FullCalendar Init ===================== --}}
{{-- Logic tidak diubah: initialView, locale, events route, changeView() --}}
<script>
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');

        // BUSINESS LOGIC: Inisialisasi FullCalendar
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next'
            },
            events: '{{ route('calendar.events') }}',

            // STEP: Tambahkan dayMaxEvents agar tidak overflow di sel kecil
            dayMaxEvents: 2,
        });

        calendar.render();
    });

    // SECTION: Toggle compact / detailed view
    // Logic changeView tidak berubah — hanya tombol state-nya yang sudah dipindah ke dashboard
    function changeView(mode) {
        const wrapper = document.getElementById('timeline-wrapper');
        const calendarContainer = document.getElementById('calendar-container');
        const btnC = document.getElementById('btn-compact');
        const btnD = document.getElementById('btn-detailed');

        if (mode === 'detailed') {
            // STEP 1: Ubah grid jadi 1 kolom (kalender full width)
            wrapper.classList.remove('md:grid-cols-2');
            wrapper.classList.add('grid-cols-1');

            // STEP 2: Tambahkan class CSS zoom
            calendarContainer.classList.add('detailed-mode');
            calendarContainer.classList.remove('compact-mode');

            // STEP 3: Update state tombol
            btnD.classList.add('bg-blue-600', 'text-white');
            btnD.classList.remove('text-gray-500');
            btnC.classList.remove('bg-blue-600', 'text-white');
            btnC.classList.add('text-gray-500');

        } else {
            // STEP 1: Kembalikan ke grid 2 kolom
            wrapper.classList.add('md:grid-cols-2');
            wrapper.classList.remove('grid-cols-1');

            // STEP 2: Hapus class zoom
            calendarContainer.classList.remove('detailed-mode');
            calendarContainer.classList.add('compact-mode');

            // STEP 3: Update state tombol
            btnC.classList.add('bg-blue-600', 'text-white');
            btnC.classList.remove('text-gray-500');
            btnD.classList.remove('bg-blue-600', 'text-white');
            btnD.classList.add('text-gray-500');
        }

        // BUSINESS LOGIC: Beri jeda agar transisi CSS selesai, lalu resize FullCalendar
        setTimeout(() => {
            if (typeof calendar !== 'undefined') {
                calendar.updateSize();
            }
        }, 400);
    }
</script>
