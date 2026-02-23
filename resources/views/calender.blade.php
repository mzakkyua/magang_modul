<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
</head>
<body>

<div class="container">
    <div id="calendar"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

let calendarEl = document.getElementById('calendar');
if(!calendarEl) return;

let calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'id',
    height: 420,

    events: '{{ route("calendar") }}'
});

calendar.render();
});
</script>

</body>
</html>