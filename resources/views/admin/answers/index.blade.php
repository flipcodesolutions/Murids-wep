@extends('admin.layout.app')
@section('title', 'Reports')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="card-header-title">Reports</h3>

                <div class="card-header-toolbar single-row-toolbar">
                    <div class="search-box-wrap">
                        <span class="search-box-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="tableSearch" class="form-control form-control-sm search-box-input" placeholder="Search reports...">
                    </div>
                </div>
            </div>

            <div class="card-body-custom p-0">
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
                                <td colspan="8" class="text-center py-4 text-muted">Loading reports...</td>
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
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fetchUrl = @json(route('answers.index'));
            const tableBody = document.getElementById('answerTableBody');
            const table = document.getElementById('dataTable');
            const tableSearch = document.getElementById('tableSearch');

            let currentPage = 1;
            let currentSearch = '';

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

            function buildFetchUrl(page = 1, search = '') {
                const url = new URL(fetchUrl, window.location.origin);
                url.searchParams.set('page', page);
                if (search) url.searchParams.set('search', search);
                return url.toString();
            }

            function renderRows(rows, page = 1, perPage = 10) {
                if (!rows.length) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No reports found.</td></tr>';
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

            function loadAnswers(page = 1, search = '') {
                currentPage = page;
                currentSearch = search;
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Loading reports...</td></tr>';

                fetch(buildFetchUrl(page, search), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw data;
                    return data;
                })
                .then(result => {
                    renderRows(result.data || [], result.current_page, result.per_page);
                    renderPagination(result, 'paginationWrapper', 'tableInfo', page => loadAnswers(page, currentSearch));
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load reports.</td></tr>';
                    renderPagination({}, 'paginationWrapper', 'tableInfo');
                    console.error(error);
                });
            }

            if (tableSearch) {
                let searchTimeout;
                tableSearch.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const keyword = this.value.trim();

                    searchTimeout = setTimeout(() => {
                        loadAnswers(1, keyword);
                    }, 300);
                });
            }

            loadAnswers(1, '');
        });
    </script>
@endpush
