@extends('admin.layout.app')
@section('title', 'Reports')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="card-header-title">Reports</h3>

                {{-- <div class="card-header-actions">
                    <input type="text" id="tableSearch" class="form-control form-control-sm" placeholder="Search answers...">
                </div> --}}
            </div>

            {{-- <div class="card-body-custom p-0">
                <div class="table-scroll-wrap">
                    <table class="table table-custom table-stack-mobile mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Religion</th>
                                <th>Time Slot</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Answered At</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="answerTableBody">
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Loading answers...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-body-custom border-top table-footer">
                <div class="table-footer-inner">
                    <span class="text-muted table-footer-info" id="tableInfo">Loading...</span>
                    <div id="paginationWrapper" class="theme-pagination"></div>
                </div>
            </div> --}}
        </div>
    </div>

@endsection

{{-- @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fetchUrl = @json(route('answers.index'));
            const tableBody = document.getElementById('answerTableBody');
            const table = document.getElementById('dataTable');
            const tableSearch = document.getElementById('tableSearch');
            const tableInfo = document.getElementById('tableInfo');
            const paginationWrapper = document.getElementById('paginationWrapper');

            let allAnswers = [];
            let filteredAnswers = [];
            let currentPage = 1;
            const perPage = 10;
            let searchTimeout = null;

            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                });
            }

            function formatDateTime(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            }

            function truncateText(text, maxLength) {
                if (!text) return '-';
                text = String(text);
                return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text ?? '';
                return div.innerHTML;
            }

            function getAnswerBadge(answer) {
                return answer ?
                    '<span class="badge-status active">Yes</span>' :
                    '<span class="badge-status inactive">No</span>';
            }

            function applyResponsiveLabels() {
                if (!table) return;
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());

                table.querySelectorAll('tbody tr').forEach(row => {
                    row.querySelectorAll('td').forEach((td, index) => {
                        if (headers[index]) {
                            td.setAttribute('data-label', headers[index]);
                        }
                    });
                });
            }

            function buildFetchUrl(page = 1) {
                const url = new URL(fetchUrl, window.location.origin);
                url.searchParams.set('page', page);
                return url.toString();
            }

            async function fetchPage(page) {
                const response = await fetch(buildFetchUrl(page), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();
                if (!response.ok) throw data;
                return data;
            }

            async function loadAllAnswers() {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Loading answers...</td></tr>';

                try {
                    const firstPage = await fetchPage(1);
                    let collected = [...(firstPage.data || [])];
                    const lastPage = firstPage.last_page || 1;

                    for (let page = 2; page <= lastPage; page++) {
                        const nextPage = await fetchPage(page);
                        collected = collected.concat(nextPage.data || []);
                    }

                    allAnswers = collected;
                    filteredAnswers = [...allAnswers];
                    renderClientPage(1);
                } catch (error) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load answers.</td></tr>';
                    tableInfo.textContent = 'Failed to load data.';
                    paginationWrapper.innerHTML = '';
                    console.error(error);
                }
            }

            function normalizeAnswer(item) {
                return {
                    user: item.user?.name || '',
                    religion: item.religion?.name || '',
                    timeSlot: item.time_slot?.name || item.time_slot?.title || '',
                    question: item.question?.question || '',
                    answer: item.answer ? 'yes' : 'no',
                    answeredAt: item.answered_at || '',
                    createdAt: item.created_at || '',
                };
            }

            function filterAnswers(keyword) {
                const search = keyword.trim().toLowerCase();

                if (!search) {
                    filteredAnswers = [...allAnswers];
                    renderClientPage(1);
                    return;
                }

                filteredAnswers = allAnswers.filter(item => {
                    const row = normalizeAnswer(item);

                    return row.user.toLowerCase().includes(search) ||
                        row.religion.toLowerCase().includes(search) ||
                        row.timeSlot.toLowerCase().includes(search) ||
                        row.question.toLowerCase().includes(search) ||
                        row.answer.includes(search) ||
                        row.answeredAt.toLowerCase().includes(search) ||
                        row.createdAt.toLowerCase().includes(search);
                });

                renderClientPage(1);
            }

            function renderRows(rows, page = 1) {
                if (!rows.length) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No answers found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = rows.map((item, index) => `
            <tr>
                <td>${((page - 1) * perPage) + index + 1}</td>
                <td>${escapeHtml(item.user?.name || '-')}</td>
                <td>${escapeHtml(item.religion?.name || '-')}</td>
                <td>${escapeHtml(item.time_slot?.name || item.time_slot?.title || '-')}</td>
                <td>${escapeHtml(truncateText(item.question?.question || '-', 80))}</td>
                <td>${getAnswerBadge(item.answer)}</td>
                <td>${formatDateTime(item.answered_at)}</td>
                <td>${formatDate(item.created_at)}</td>
            </tr>
        `).join('');

                applyResponsiveLabels();
            }

            function updateTableInfo(total, pageRows, page) {
                if (!total || pageRows.length === 0) {
                    tableInfo.textContent = 'No answers found.';
                    return;
                }

                const from = ((page - 1) * perPage) + 1;
                const to = from + pageRows.length - 1;
                tableInfo.textContent = `Showing ${from} to ${to} of ${total} entries`;
            }

            function renderPagination(totalItems, current) {
                const last = Math.ceil(totalItems / perPage);

                if (last <= 1) {
                    paginationWrapper.innerHTML = '';
                    return;
                }

                let html = `<div class="pagination-group">`;

                html += `
            <button type="button"
                class="page-btn nav-btn"
                data-page="${current - 1}"
                ${current === 1 ? 'disabled' : ''}>
                <i class="bi bi-chevron-left"></i>
            </button>
        `;

                function pageButton(page) {
                    return `
                <button type="button"
                    class="page-btn ${page === current ? 'active' : ''}"
                    data-page="${page}">
                    ${page}
                </button>
            `;
                }

                function dots() {
                    return `<span class="page-dots">...</span>`;
                }

                let start = Math.max(1, current - 1);
                let end = Math.min(last, current + 1);

                if (start > 1) html += pageButton(1);
                if (start > 2) html += dots();

                for (let i = start; i <= end; i++) {
                    html += pageButton(i);
                }

                if (end < last - 1) html += dots();
                if (end < last) html += pageButton(last);

                html += `
            <button type="button"
                class="page-btn nav-btn"
                data-page="${current + 1}"
                ${current === last ? 'disabled' : ''}>
                <i class="bi bi-chevron-right"></i>
            </button>
        `;

                html += `</div>`;
                paginationWrapper.innerHTML = html;

                paginationWrapper.querySelectorAll('.page-btn[data-page]').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.disabled) return;
                        const page = parseInt(this.dataset.page);
                        if (!isNaN(page)) {
                            renderClientPage(page);
                        }
                    });
                });
            }

            function renderClientPage(page = 1) {
                currentPage = page;

                const start = (page - 1) * perPage;
                const end = start + perPage;
                const pageRows = filteredAnswers.slice(start, end);

                renderRows(pageRows, page);
                updateTableInfo(filteredAnswers.length, pageRows, page);
                renderPagination(filteredAnswers.length, page);
            }

            if (tableSearch) {
                tableSearch.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const keyword = this.value || '';

                    searchTimeout = setTimeout(() => {
                        filterAnswers(keyword);
                    }, 300);
                });
            }

            loadAllAnswers();
        });
    </script>
@endpush --}}
