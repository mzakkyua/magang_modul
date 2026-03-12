<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    
<style>
    /* 1. Global Reset & Font */
    #calendar {
        font-family: 'Inter', sans-serif; /* Pastikan match dengan SINAKERTRANS */
        transition: all 0.4s ease;
    }

    /* 2. Menghilangkan Border Luar yang Kaku */
    .fc-theme-standard .fc-scrollgrid {
        border: none !important;
    }

    /* 3. Percantik Header Hari (MIN, SEN, SEL...) */
    .fc-col-header-cell-cushion {
        padding: 15px 0 !important;
        text-transform: uppercase;
        font-size: 0.7rem !important;
        letter-spacing: 0.15em;
        font-weight: 800 !important;
        color: #94a3b8 !important; /* Abu-abu pudar tapi tegas */
        text-decoration: none !important;
    }

    /* 4. Percantik Area Tanggal (Sama untuk Compact & Detailed) */
    .fc-daygrid-day-number {
        font-weight: 500;
        color: #475569 !important; /* Slate-600 */
        text-decoration: none !important;
    }

    /* Tambahkan garis batas antar sel yang sangat tipis agar area tanggal jelas */
    .fc-theme-standard td {
        border: 1px solid #f1f5f9 !important; /* Slate-100 */
        transition: all 0.3s;
    }

    /* Hover effect agar interaktif */
    .fc-daygrid-day:hover {
        background-color: #f8fafc !important; /* Slate-50 */
        cursor: pointer;
    }

    /* 5. Highlight Hari Ini (PENTING!) */
    .fc-day-today {
        background-color: #f0f7ff !important; /* Blue-50 (Latar belakang sangat pucat) */
        border-radius: 10px;
        position: relative;
    }
    /* Tambahkan lingkaran biru kecil di bawah angka untuk hari ini */
    .fc-day-today .fc-daygrid-day-number::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 50%;
        transform: translateX(-50%);
        width: 5px;
        height: 5px;
        background-color: #2563eb; /* Blue-600 */
        border-radius: 50%;
    }

    /* 6. Percantik Tombol Navigasi (Prev, Next) */
    .fc-header-toolbar {
        margin-bottom: 2rem !important;
    }

    .fc-button-group > .fc-button {
        background-color: white !important;
        border: 1px solid #e2e8f0 !important; /* Slate-200 */
        color: #1e293b !important; /* Slate-900 */
        padding: 8px 12px !important;
        border-radius: 10px !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        margin: 0 4px !important;
    }

    .fc-button-group > .fc-button:hover {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    
    .fc-button-primary:disabled {
        opacity: 0.4;
    }

    /* 7. MODIFIKASI STATUS ZOOM (CSS Khusus Mode) */
    /* -- Mode Zoom In -- */
    .detailed-mode .fc-daygrid-day-number {
        font-size: 1.2rem !important;
        padding: 20px !important;
    }
    
    .detailed-mode .fc-col-header-cell-cushion {
        font-size: 0.85rem !important;
    }

    /* -- Mode Compact (Default) -- */
    .compact-mode .fc-daygrid-day-number {
        font-size: 0.85rem !important;
        padding: 10px !important;
    }
</style>

<div id="calendar"></div>

<script>
let calendar;
document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        headerToolbar: {
            left: 'title',
            center: '',
            right: 'prev,next'
        },
        events: '{{ route("calendar.events") }}',
    });
    calendar.render();
});

function changeView(mode) {
    const wrapper = document.getElementById('timeline-wrapper');
    const calendarContainer = document.getElementById('calendar-container');
    const btnC = document.getElementById('btn-compact');
    const btnD = document.getElementById('btn-detailed');

    if (mode === 'detailed') {
        // 1. Ubah grid dari 2 kolom menjadi 1 kolom
        wrapper.classList.remove('md:grid-cols-2');
        wrapper.classList.add('grid-cols-1');
        
        // 2. Tambahkan class CSS khusus zoom (untuk memperbesar angka/jarak)
        calendarContainer.classList.add('detailed-mode');
        
        // 3. Update tampilan tombol
        btnD.classList.add('bg-blue-600', 'text-white');
        btnC.classList.remove('bg-blue-600', 'text-white');
    } else {
        // 1. Kembalikan ke grid 2 kolom (kalender ke samping lagi)
        wrapper.classList.add('md:grid-cols-2');
        wrapper.classList.remove('grid-cols-1');
        
        // 2. Hapus class zoom
        calendarContainer.classList.remove('detailed-mode');
        
        // 3. Update tampilan tombol
        btnC.classList.add('bg-blue-600', 'text-white');
        btnD.classList.remove('bg-blue-600', 'text-white');
    }
    
    // PENTING: Beri jeda sedikit agar transisi selesai, lalu update ukuran FullCalendar
    setTimeout(() => { 
        if(typeof calendar !== 'undefined') {
            calendar.updateSize(); 
        }
    }, 400);
}
</script>

</body>
</html>