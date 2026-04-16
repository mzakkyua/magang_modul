{{-- ==========================================================
     PARTIAL: resources/views/partials/calendar.blade.php
     Di-include di: dashboard.blade.php (kolom kanan)
     ========================================================== --}}

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* ── Header ── */
    .cal-section-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .cal-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .cal-month-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.03em;
    }

    .cal-nav-group {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    #cal-today-badge {
        font-size: 10px;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        padding: 4px 10px;
        border-radius: 20px;
        margin-right: 4px;
        white-space: nowrap;
    }

    .cal-nav-btn {
        width: 28px;
        height: 28px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 15px;
        font-weight: 700;
        transition: all 0.15s;
        line-height: 1;
    }

    .cal-nav-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
    }

    /* ── Grid ── */
    #cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }

    .cal-day-hdr {
        text-align: center;
        font-size: 9px;
        font-weight: 800;
        color: #cbd5e1;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 5px 0 8px;
    }

    .cal-cell {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 3px 1px 7px;
        border-radius: 9px;
        cursor: pointer;
        transition: background 0.12s;
        min-height: 44px;
    }

    .cal-cell:hover {
        background: #f1f5f9;
    }

    /* Angka hari */
    .cal-day-num {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        flex-shrink: 0;
        transition: background 0.12s, color 0.12s;
    }

    .cal-day-num.is-today {
        background: #2563eb;
        color: #ffffff;
        font-weight: 800;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.32);
    }

    .cal-day-num.is-other {
        color: #dde3eb;
        font-weight: 400;
    }

    /* Dots event resmi */
    .cal-dots {
        display: flex;
        gap: 3px;
        margin-top: 2px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .cal-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Bar tipis magang — 3px di bawah cell */
    .internship-bar {
        position: absolute;
        bottom: 4px;
        height: 3px;
        border-radius: 2px;
        background: #10b981;
        opacity: 0.72;
    }

    .bar-start {
        left: 6px;
        right: 0;
        border-radius: 2px 0 0 2px;
    }

    .bar-mid {
        left: 0;
        right: 0;
        border-radius: 0;
    }

    .bar-end {
        left: 0;
        right: 6px;
        border-radius: 0 2px 2px 0;
    }

    /* ── Legend ── */
    #cal-legend {
        display: flex;
        gap: 14px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }

    .leg-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        color: #64748b;
        font-weight: 600;
    }

    .leg-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .leg-bar {
        width: 14px;
        height: 3px;
        border-radius: 2px;
        display: inline-block;
    }

    /* ── Mini event list ── */
    #cal-event-list {
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .cal-event-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
    }

    .cei-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .cei-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cei-date {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 600;
        white-space: nowrap;
    }

    .cei-badge {
        font-size: 9.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-upcoming {
        background: #eff6ff;
        color: #2563eb;
    }
</style>

{{-- ── Header ── --}}
<p class="cal-section-label">Jadwal Aktif</p>
<div class="cal-top-row">
    <p class="cal-month-title" id="cal-month-label">—</p>
    <div class="cal-nav-group">
        <span id="cal-today-badge">—</span>
        <button class="cal-nav-btn" onclick="calPrev()" title="Bulan sebelumnya">&#8249;</button>
        <button class="cal-nav-btn" onclick="calNext()" title="Bulan berikutnya">&#8250;</button>
    </div>
</div>

{{-- ── Grid ── --}}
<div id="cal-grid">
    <div class="cal-day-hdr">Min</div>
    <div class="cal-day-hdr">Sen</div>
    <div class="cal-day-hdr">Sel</div>
    <div class="cal-day-hdr">Rab</div>
    <div class="cal-day-hdr">Kam</div>
    <div class="cal-day-hdr">Jum</div>
    <div class="cal-day-hdr">Sab</div>
</div>

{{-- ── Legend ── --}}
<div id="cal-legend">
    <div class="leg-item">
        <span class="leg-dot" style="background:#3b82f6;"></span> Event Resmi
    </div>
    <div class="leg-item">
        <span class="leg-bar" style="background:#10b981;"></span> Jadwal Magang
    </div>
</div>

{{-- ── Mini event list ── --}}
<div id="cal-event-list"></div>

<script>
    (function() {
        /* ── State ── */
        const TODAY = new Date();
        const ID_DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const ID_MONTHS_FULL = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];
        const ID_MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
        'Des'];

        let curYear = TODAY.getFullYear();
        let curMonth = TODAY.getMonth();

        let internshipStart = null;
        let internshipEnd = null;
        let globalEvents = [];

        /* ── Today badge ── */
        document.getElementById('cal-today-badge').textContent =
            ID_DAYS[TODAY.getDay()] + ', ' +
            TODAY.getDate() + ' ' +
            ID_MONTHS_FULL[TODAY.getMonth()] + ' ' +
            TODAY.getFullYear();

        /* ── Fetch events dari backend ── */
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const headers = csrfMeta ? {
            'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
        } : {};

        fetch('{{ route('calendar.events') }}', {
                headers
            })
            .then(r => r.json())
            .then(events => {
                events.forEach(e => {
                    if (e.extendedProps && e.extendedProps.type === 'internship') {
                        internshipStart = new Date(e.start);
                        // Backend sudah +1 hari pada end (FC eksklusif), kurangi kembali
                        const endRaw = new Date(e.end);
                        endRaw.setDate(endRaw.getDate() - 1);
                        internshipEnd = endRaw;
                    } else {
                        globalEvents.push({
                            date: new Date(e.start),
                            color: e.color || '#3b82f6',
                            title: e.title.replace(/^📌\s*/, '')
                        });
                    }
                });
                renderGrid();
                renderEventList();
            })
            .catch(() => renderGrid());

        /* ── Render grid ── */
        function renderGrid() {
            const grid = document.getElementById('cal-grid');

            // Hapus semua cell lama, sisakan 7 header
            while (grid.children.length > 7) grid.removeChild(grid.lastChild);

            document.getElementById('cal-month-label').textContent =
                ID_MONTHS_FULL[curMonth] + ' ' + curYear;

            const firstDay = new Date(curYear, curMonth, 1).getDay();
            const daysInMonth = new Date(curYear, curMonth + 1, 0).getDate();
            const prevDays = new Date(curYear, curMonth, 0).getDate();

            // Filler bulan lalu
            for (let i = firstDay - 1; i >= 0; i--) {
                grid.appendChild(buildCell(prevDays - i, true, false, 'none', []));
            }

            // Hari bulan ini
            for (let d = 1; d <= daysInMonth; d++) {
                const date = new Date(curYear, curMonth, d);
                const isToday = date.toDateString() === TODAY.toDateString();

                let barType = 'none';
                if (internshipStart && internshipEnd && date >= internshipStart && date <= internshipEnd) {
                    if (date.toDateString() === internshipStart.toDateString()) barType = 'bar-start';
                    else if (date.toDateString() === internshipEnd.toDateString()) barType = 'bar-end';
                    else barType = 'bar-mid';
                }

                const evts = globalEvents.filter(e => e.date.toDateString() === date.toDateString());
                grid.appendChild(buildCell(d, false, isToday, barType, evts));
            }

            // Filler bulan berikutnya
            const total = firstDay + daysInMonth;
            const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
            for (let i = 1; i <= remaining; i++) {
                grid.appendChild(buildCell(i, true, false, 'none', []));
            }
        }

        function buildCell(day, isOther, isToday, barType, events) {
            const cell = document.createElement('div');
            cell.className = 'cal-cell';

            // Angka hari
            const num = document.createElement('div');
            num.className = 'cal-day-num' +
                (isToday ? ' is-today' : '') +
                (isOther ? ' is-other' : '');
            num.textContent = day;
            cell.appendChild(num);

            // Dots event resmi
            if (events.length) {
                const dots = document.createElement('div');
                dots.className = 'cal-dots';
                events.forEach(ev => {
                    const dot = document.createElement('span');
                    dot.className = 'cal-dot';
                    dot.style.background = ev.color;
                    dots.appendChild(dot);
                });
                cell.appendChild(dots);
            }

            // Bar magang
            if (barType !== 'none') {
                const bar = document.createElement('div');
                bar.className = 'internship-bar ' + barType;
                cell.appendChild(bar);
            }

            return cell;
        }

        /* ── Render mini event list ── */
        function renderEventList() {
            const list = document.getElementById('cal-event-list');
            list.innerHTML = '';

            // Magang aktif
            if (internshipStart && internshipEnd) {
                list.appendChild(buildEventItem('#10b981', 'Masa Magang Aktif', null, 'cei-badge badge-active',
                    'Aktif'));
            }

            // Event resmi mendatang (maks 3)
            const upcoming = globalEvents
                .filter(e => e.date >= TODAY)
                .sort((a, b) => a.date - b.date)
                .slice(0, 3);

            upcoming.forEach(ev => {
                const dateStr = ev.date.getDate() + ' ' + ID_MONTHS_SHORT[ev.date.getMonth()];
                list.appendChild(buildEventItem(ev.color, ev.title, dateStr, 'cei-badge badge-upcoming',
                    null));
            });
        }

        function buildEventItem(color, title, dateStr, badgeClass, badgeText) {
            const item = document.createElement('div');
            item.className = 'cal-event-item';

            const dot = document.createElement('span');
            dot.className = 'cei-dot';
            dot.style.background = color;

            const ttl = document.createElement('span');
            ttl.className = 'cei-title';
            ttl.textContent = title;

            item.appendChild(dot);
            item.appendChild(ttl);

            if (dateStr) {
                const dt = document.createElement('span');
                dt.className = 'cei-date';
                dt.textContent = dateStr;
                item.appendChild(dt);
            }

            if (badgeText) {
                const bdg = document.createElement('span');
                bdg.className = badgeClass;
                bdg.textContent = badgeText;
                item.appendChild(bdg);
            }

            return item;
        }

        /* ── Navigasi ── */
        window.calPrev = function() {
            curMonth--;
            if (curMonth < 0) {
                curMonth = 11;
                curYear--;
            }
            renderGrid();
        };

        window.calNext = function() {
            curMonth++;
            if (curMonth > 11) {
                curMonth = 0;
                curYear++;
            }
            renderGrid();
        };

    })();
</script>
