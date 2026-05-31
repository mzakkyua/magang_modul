{{-- resources/views/components/search-lowongan.blade.php --}}
@props(['action' => '', 'search' => ''])

<div class="w-full">

    <form onsubmit="doSearch(); return false;">
        <div
            class="flex gap-2 bg-white p-1.5 rounded-2xl shadow-lg shadow-gray-200/80 border border-gray-100 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-200 transition-all duration-200">

            <div class="flex items-center flex-1 gap-2 px-3">

                {{-- Ikon search / dot loading --}}
                <div class="shrink-0 w-4 h-4 flex items-center justify-center">
                    <svg id="search-icon" class="w-4 h-4 text-gray-400" viewBox="0 0 20 20" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="8.5" cy="8.5" r="5.5" />
                        <path d="M15 15l-3-3" />
                    </svg>
                    <div id="search-loading" class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse hidden"></div>
                </div>

                <input type="text" id="search-input" name="search" value="{{ $search }}"
                    placeholder="Cari posisi magang atau divisi..." autocomplete="off"
                    oninput="onSearchInput(this.value)" onkeydown="if(event.key==='Escape'){ clearSearch(); }"
                    class="flex-1 py-2 outline-none text-sm text-gray-700 bg-transparent placeholder-gray-400" />

                {{-- Tombol X --}}
                <button type="button" id="clear-btn" @class([
                    'shrink-0 p-0.5 text-gray-300 hover:text-red-400 transition-colors',
                    'hidden' => !$search,
                ]) title="Hapus pencarian"
                    onclick="clearSearch()">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

            </div>

            <button type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 active:scale-95 transition-all shadow-md shadow-blue-600/30 flex items-center gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8.5" cy="8.5" r="5.5" />
                    <path d="M15 15l-3-3" />
                </svg>
                Cari
            </button>

        </div>
    </form>

    {{-- Badge hasil pencarian --}}
    <div id="search-badge" @class([
        'mt-3 items-center gap-2 text-xs text-gray-500 flex-wrap',
        'flex' => $search,
        'hidden' => !$search,
    ])>
        <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor"
            stroke-width="2">
            <circle cx="8.5" cy="8.5" r="5.5" />
            <path d="M15 15l-3-3" />
        </svg>
        Hasil untuk:
        <span id="badge-keyword"
            class="font-semibold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100">
            "{{ $search }}"
        </span>
        <span id="badge-count" class="text-gray-400"></span>
        <button type="button" onclick="clearSearch()"
            class="ml-auto flex items-center gap-1 text-gray-400 hover:text-red-500 transition-colors">
            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            Reset
        </button>
    </div>

</div>

@once
    @push('script')
        <script>
            // ... (Kode JavaScript di sini tidak diubah sama sekali agar tidak merusak fungsi search)
            var searchDebounceTimer = null;
            var SEARCH_DEBOUNCE_MS = 500;

            function filterCards(keyword) {
                var kw = (keyword || '').trim().toLowerCase();
                var allCards = document.querySelectorAll('[data-search-card]');
                var count = 0;

                allCards.forEach(function(card) {
                    var match = kw.length === 0 ||
                        (card.dataset.title || '')
                        .includes(kw) ||
                        (card.dataset.division || '').includes(kw) ||
                        (card.dataset.type || '').includes(kw);

                    card.style.display = match ? '' : 'none';
                    if (match) count++;
                });

                ['semua', 'magang', 'penelitian'].forEach(function(tab) {
                    var container = document.getElementById('tab-' + tab);
                    if (!container) return;
                    var empty = container.querySelector('[data-empty-state]');
                    if (!empty) return;
                    var anyVisible = Array.from(
                        container.querySelectorAll('[data-search-card]')
                    ).some(function(c) {
                        return c.style.display !== 'none';
                    });
                    empty.style.display = anyVisible ? 'none' : '';
                });

                updateBadge(keyword, count);

                var url = new URL(window.location.href);
                if (kw.length > 0) {
                    url.searchParams.set('search', keyword.trim());
                } else {
                    url.searchParams.delete('search');
                }
                window.history.replaceState({}, '', url.toString());
            }

            function updateBadge(keyword, count) {
                var badge = document.getElementById('search-badge');
                var kw = document.getElementById('badge-keyword');
                var badgeCnt = document.getElementById('badge-count');

                if (!badge) return;

                var trimmed = (keyword || '').trim();

                if (trimmed.length === 0) {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                } else {
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                    if (kw) kw.textContent = '"' + trimmed + '"';
                    if (badgeCnt) badgeCnt.textContent = '— ' + count + ' lowongan';
                }
            }

            function setSearchLoading(on) {
                var icon = document.getElementById('search-icon');
                var loading = document.getElementById('search-loading');
                if (icon) icon.classList.toggle('hidden', on);
                if (loading) loading.classList.toggle('hidden', !on);
            }

            function onSearchInput(value) {
                var clearBtn = document.getElementById('clear-btn');

                if (clearBtn) {
                    if (value.length > 0) {
                        clearBtn.classList.remove('hidden');
                    } else {
                        clearBtn.classList.add('hidden');
                    }
                }

                clearTimeout(searchDebounceTimer);

                if (value.trim().length === 0) {
                    setSearchLoading(false);
                    filterCards('');
                    return;
                }

                setSearchLoading(true);

                searchDebounceTimer = setTimeout(function() {
                    setSearchLoading(false);
                    filterCards(value);
                }, SEARCH_DEBOUNCE_MS);
            }

            function doSearch() {
                clearTimeout(searchDebounceTimer);
                setSearchLoading(false);
                var input = document.getElementById('search-input');
                if (input) filterCards(input.value);
            }

            function clearSearch() {
                clearTimeout(searchDebounceTimer);
                setSearchLoading(false);

                var input = document.getElementById('search-input');
                var clearBtn = document.getElementById('clear-btn');

                if (input) {
                    input.value = '';
                    input.focus();
                }
                if (clearBtn) clearBtn.classList.add('hidden');

                filterCards('');
            }

            // ─────────────────────────────────────────
            // TRIGGER SEARCH DARI COMPONENT LAIN (Card Divisi)
            // ─────────────────────────────────────────
            function searchDivisionAndScroll(divisionName) {
                // 1. Masukkan teks divisi ke dalam input search
                var input = document.getElementById('search-input');
                if (input) {
                    input.value = divisionName;

                    // 2. Munculkan tombol X (clear)
                    var clearBtn = document.getElementById('clear-btn');
                    if (clearBtn) clearBtn.classList.remove('hidden');
                }

                // 3. Jalankan filter client-side langsung tanpa reload
                filterCards(divisionName);

                // 4. Scroll smooth ke section lowongan
                var sectionLowongan = document.getElementById('lowongan');
                if (sectionLowongan) {
                    sectionLowongan.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }

            // ─────────────────────────────────────────
            // INIT — jalankan filter jika ada search dari server
            // ─────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', function() {
                var input = document.getElementById('search-input');
                if (input && input.value.trim().length > 0) {
                    filterCards(input.value);
                }

                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    }
                });
            });
        </script>
    @endpush
@endonce
