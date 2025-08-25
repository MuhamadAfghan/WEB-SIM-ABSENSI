@extends('components.template')

@section('title', 'Manajemen Akun - Hadir.in')

@section('content')
    <div class="mx-auto w-full max-w-7xl">
        <!-- Header + Actions -->
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="ml-2 text-3xl font-bold text-gray-800 md:ml-4 md:text-4xl">Manajemen Akun</h2>

            <div class="flex w-full flex-col gap-3 sm:flex-row md:w-auto">
                <!-- Search -->
                <div class="relative w-full md:w-96 lg:w-[400px]">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="searchInput" type="text" placeholder="Cari"
                        class="w-full rounded-xl border-0 bg-white py-3 pl-11 pr-4 text-gray-700 shadow-sm ring-1 ring-transparent focus:ring-2 focus:ring-blue-400" />
                </div>

                <!-- Division Filter -->
                <div class="relative min-w-[140px]">
                    <select id="divisionFilter"
                        class="w-full appearance-none rounded-xl border-0 bg-white py-3 pl-4 pr-10 text-gray-700 shadow-sm ring-1 ring-transparent focus:ring-2 focus:ring-blue-400">
                        <option selected>Divisi</option>
                        <option>Akademik</option>
                        <option>Keuangan</option>
                        <option>Operasional</option>
                    </select>
                    <i
                        class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Cards Grid (dynamic) -->
        <div id="usersGrid" class="mt-15 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3"></div>
        <!-- Pagination -->
        <div id="paginationControls" class="mt-6 flex flex-wrap items-center justify-center gap-2"></div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const usersGrid = document.getElementById('usersGrid');
                const paginationControls = document.getElementById('paginationControls');
                const searchInput = document.getElementById('searchInput');
                const divisionFilter = document.getElementById('divisionFilter');

                const API_ENDPOINT = `{{ url('/api/user') }}`;

                const viewState = {
                    page: 1,
                    perPage: 9,
                    sortField: 'id',
                    sortOrder: 'asc',
                    searchQuery: '',
                    divisi: ''
                };

                function buildParams() {
                    const params = {
                        page: viewState.page,
                        per_page: viewState.perPage,
                        sort_field: viewState.sortField,
                        sort_order: viewState.sortOrder,
                    };
                    const q = viewState.searchQuery?.trim();
                    if (q) params.search = q;
                    const d = viewState.divisi?.trim();
                    if (d) params.divisi = d;
                    return params;
                }

                function renderLoading() {
                    usersGrid.innerHTML = '<div class="col-span-full text-center text-gray-500">Memuat data...</div>';
                }

                function renderError() {
                    usersGrid.innerHTML = '<div class="col-span-full text-center text-red-500">Gagal memuat data</div>';
                    paginationControls.innerHTML = '';
                }

                function renderUnauthorized() {
                    usersGrid.innerHTML =
                        '<div class="col-span-full text-center text-red-500">Unauthorized. Silakan login sebagai admin terlebih dahulu.</div>';
                    paginationControls.innerHTML = '';
                }

                function escapeHtml(text) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return String(text ?? '').replace(/[&<>"']/g, m => map[m]);
                }

                function renderUsers(users) {
                    if (!users || users.length === 0) {
                        usersGrid.innerHTML =
                            '<div class="col-span-full text-center text-gray-500">Tidak ada data</div>';
                        return;
                    }

                    usersGrid.innerHTML = users.map(user => {
                        const name = escapeHtml(user.name);
                        const division = escapeHtml(user.divisi ?? '-');
                        const nip = escapeHtml(user.nip ?? '-');
                        const userId = user.id;
                        return `
                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-500">
                                    <i class="fas fa-user"></i>
                                </div>
                                <p class="text-base font-semibold text-gray-800">${name}</p>
                            </div>
                            <div class="mt-4 space-y-1 text-sm">
                                <div class="flex items-center justify-between text-black-600">
                                    <span>Divisi :</span>
                                    <span class="font-medium text-black-800">${division}</span>
                                </div>
                                <div class="flex items-center justify-between text-black-600">
                                    <span>NIP    :</span>
                                    <span class="font-medium text-black-800">${nip}</span>
                                </div>
                            </div>
                            <button type="button" onclick="window.location.href='{{ route('employee.details', '') }}/${userId}'" class="mt-4 w-full rounded-xl bg-[#60B5FF] px-4 py-2.5 font-medium text-white transition-colors hover:bg-blue-400">
                                <img src="{{ asset('img-icon-acount-management/edit_white.png') }}" alt="Edit" class=" inline h-5 w-5 mr-1">
                                Profil
                            </button>
                        </div>`;
                    }).join('');
                }

                function renderPagination(meta) {
                    if (!meta || meta.last_page <= 1) {
                        paginationControls.innerHTML = '';
                        return;
                    }

                    const current = Number(meta.current_page) || 1;
                    const last = Number(meta.last_page) || 1;

                    const buttonHtml = (label, page, {
                        disabled = false,
                        active = false
                    } = {}) => {
                        const base = 'px-3 py-2 rounded-lg border text-sm';
                        const classes = [
                            base,
                            active ? 'bg-[#60B5FF] text-white border-[#60B5FF]' :
                            'bg-white text-gray-700 border-gray-200 hover:bg-blue-50',
                            disabled ? 'opacity-50 cursor-not-allowed' : ''
                        ].join(' ');
                        return `<button class="${classes}" ${disabled ? 'disabled' : ''} data-page="${page}">${label}</button>`;
                    };

                    const windowSize = 5;
                    const half = Math.floor(windowSize / 2);
                    let start = Math.max(1, current - half);
                    let end = Math.min(last, start + windowSize - 1);
                    start = Math.max(1, end - windowSize + 1);

                    let html = '';
                    html += buttonHtml('Prev', Math.max(1, current - 1), {
                        disabled: current === 1
                    });
                    for (let p = start; p <= end; p++) {
                        html += buttonHtml(String(p), p, {
                            active: p === current
                        });
                    }
                    html += buttonHtml('Next', Math.min(last, current + 1), {
                        disabled: current === last
                    });

                    paginationControls.innerHTML = html;
                    paginationControls.querySelectorAll('button[data-page]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const target = Number(btn.getAttribute('data-page'));
                            if (target && target !== viewState.page) {
                                updateState({
                                    page: target
                                });
                                usersGrid.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        });
                    });
                }

                async function fetchAndRender() {
                    renderLoading();
                    try {
                        const params = new URLSearchParams(buildParams()).toString();
                        const response = await fetch(`${API_ENDPOINT}?${params}`);
                        const json = await response.json();

                        if (!response.ok) {
                            if (response.status === 401) return renderUnauthorized();
                            throw new Error(json.message || 'Gagal memuat data');
                        }

                        renderUsers(Array.isArray(json?.data) ? json.data : []);
                        renderPagination(json?.meta);
                    } catch (e) {
                        console.error('Error fetching data:', e);
                        renderError();
                    }
                }

                function updateState(partial) {
                    Object.assign(viewState, partial);
                    fetchAndRender();
                }

                function debounce(fn, delay) {
                    let timerId;
                    return (...args) => {
                        clearTimeout(timerId);
                        timerId = setTimeout(() => fn(...args), delay);
                    };
                }

                const handleSearch = debounce((event) => {
                    updateState({
                        searchQuery: event.target.value,
                        page: 1
                    });
                }, 300);
                searchInput?.addEventListener('input', handleSearch);

                divisionFilter?.addEventListener('change', (e) => {
                    const value = e.target.value;
                    updateState({
                        divisi: value === 'Divisi' || value === '' ? '' : value,
                        page: 1
                    });
                });

                async function loadDivisions() {
                    try {
                        const collected = new Set();
                        let page = 1;
                        const maxPages = 5; // batasi maksimal 5 halaman
                        while (page <= maxPages) {
                            const params = new URLSearchParams({
                                page,
                                per_page: 50,
                                sort_field: 'divisi',
                                sort_order: 'asc'
                            }).toString();
                            const res = await fetch(`${API_ENDPOINT}?${params}`);
                            const json = await res.json();
                            const items = Array.isArray(json?.data) ? json.data : [];
                            items.forEach(u => {
                                if (u?.divisi) collected.add(u.divisi);
                            });
                            const meta = json?.meta;
                            if (!meta || page >= meta.last_page) break;
                            page += 1;
                        }

                        const current = divisionFilter.value;
                        const options = ['Divisi', ...Array.from(collected).sort()];
                        divisionFilter.innerHTML = options.map((opt, idx) => {
                            const selected = opt === current || (idx === 0 && current === '');
                            return `<option ${selected ? 'selected' : ''}>${opt}</option>`;
                        }).join('');
                    } catch (e) {
                        console.error('Error loading divisions:', e);
                        // Jika gagal, biarkan opsi default
                    }
                }

                // const token = localStorage.getItem('admin_token');
                // if (token) {
                //     window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                // }
                // if (!token) {
                //     renderUnauthorized();
                //     return;
                // }

                loadDivisions();
                updateState({
                    page: 1,
                    perPage: 9
                });
            });
        </script>
    </div>
@endsection
