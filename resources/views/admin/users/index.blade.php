@extends('admin.layout.app')
@section('title', 'Users')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="card-header-title">Users</h3>

                <div class="card-header-toolbar single-row-toolbar">
                    <div class="search-box-wrap">
                        <span class="search-box-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="tableSearch" class="form-control form-control-sm search-box-input" placeholder="Search users...">
                    </div>
                </div>
            </div>

            <div class="card-body-custom p-0">
                <div class="table-scroll-wrap">
                    <table class="table table-custom table-stack-mobile mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Provider</th>
                                <th>Provider Id</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <tr id="loadingRow">
                                <td colspan="7" class="text-center py-4 text-muted">Loading users...</td>
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
            const fetchUrl = @json(route('users.index'));
            const tableBody = document.getElementById('userTableBody');
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

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text ?? '';
                return div.innerHTML;
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

            function renderRows(users, page = 1, perPage = 10) {
                if (!users.length) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No users found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = users.map((item, index) => `
                    <tr>
                        <td>${((page - 1) * perPage) + index + 1}</td>
                        <td>${escapeHtml(item.name || '-')}</td>
                        <td>${escapeHtml(item.email || '-')}</td>
                        <td>${escapeHtml(item.user_type || '-')}</td>
                        <td>${escapeHtml(item.provider || '-')}</td>
                        <td>${escapeHtml(item.provider_id || '-')}</td>
                        <td>${formatDate(item.created_at)}</td>
                    </tr>
                `).join('');

                applyResponsiveLabels();
            }

            function loadUsers(page = 1, search = '') {
                currentPage = page;
                currentSearch = search;

                const url = new URL(fetchUrl, window.location.origin);
                url.searchParams.set('page', page);
                if (search) url.searchParams.set('search', search);

                fetch(url.toString(), {
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
                        renderPagination(result, 'paginationWrapper', 'tableInfo', page => loadUsers(page, currentSearch));
                    })
                    .catch(error => {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load users.</td></tr>';
                        renderPagination({}, 'paginationWrapper', 'tableInfo');
                        console.error(error);
                    });
            }

            if (tableSearch) {
                let searchTimeout;
                tableSearch.addEventListener('input', function() {
                    const keyword = this.value.trim();
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        loadUsers(1, keyword);
                    }, 300);
                });
            }

            loadUsers(1, '');
        });
    </script>
@endpush
