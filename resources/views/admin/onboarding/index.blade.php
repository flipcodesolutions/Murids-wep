@extends('admin.layout.app')
@section('title', 'Onboarding Steps')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="card-header-title">Onboarding Steps</h3>

                <div class="card-header-actions">
                    <a href="{{ route('onboarding-steps.create') }}" class="btn btn-sm btn-accent">
                        <i class="bi bi-plus me-1"></i> Add Onboarding Step
                    </a>
                </div>
            </div>

            <div class="card-body-custom border-bottom">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="religionFilter" class="form-label">Religion</label>
                        <select id="religionFilter" class="form-select form-select-md">
                            <option value="">All Religions</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="questionFilter" class="form-label">Question</label>
                        <input type="text" id="questionFilter" class="form-control form-control-md" placeholder="Search question...">
                    </div>
                </div>
            </div>

            <div class="card-body-custom p-0">
                <div class="table-scroll-wrap">
                    <table class="table table-custom table-stack-mobile mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Religion</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="stepTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Loading onboarding steps...</td>
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
            const fetchUrl = @json(route('onboarding-steps.fetch'));
            const destroyUrl = @json(url('/onboarding-steps'));
            const editUrl = @json(url('/onboarding-steps'));
            const religionsUrl = @json(route('religions.fetch'));

            const tableBody = document.getElementById('stepTableBody');
            const table = document.getElementById('dataTable');
            const religionFilter = document.getElementById('religionFilter');
            const questionFilter = document.getElementById('questionFilter');

            let currentPage = 1;
            let currentReligion = '';
            let currentSearch = '';

            function getCsrfToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function showToast(message, type) {
                if (type === 'success') toastr.success(message);
                else if (type === 'error') toastr.error(message);
                else if (type === 'warning') toastr.warning(message);
                else toastr.info(message);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text ?? '';
                return div.innerHTML;
            }

            function truncateText(text, maxLength) {
                if (!text) return '-';
                text = String(text);
                return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
            }

            function applyResponsiveLabels() {
                if (!table) return;
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.querySelectorAll('td').forEach((td, index) => {
                        if (headers[index]) td.setAttribute('data-label', headers[index]);
                    });
                });
            }

            function renderOptions(options) {
                if (!Array.isArray(options) || !options.length) return '-';
                return options.map(option =>
                    `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(option)}</span>`
                ).join('');
            }

            function loadReligions() {
                fetch(`${religionsUrl}?per_page=100`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(result => {
                    const religions = result.data || [];
                    religionFilter.innerHTML = '<option value="">All Religions</option>' +
                        religions.map(r => `<option value="${r.id}">${escapeHtml(r.name)}</option>`).join('');
                })
                .catch(err => console.error(err));
            }

            function buildFetchUrl(page = 1, religionId = '', search = '') {
                const url = new URL(fetchUrl, window.location.origin);
                url.searchParams.set('page', page);
                if (religionId) url.searchParams.set('religion_id', religionId);
                if (search) url.searchParams.set('search', search);
                return url.toString();
            }

            function renderRows(rows, page = 1, perPage = 10) {
                if (!rows.length) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No onboarding steps found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = rows.map((item, index) => `
                    <tr data-id="${item.id}">
                        <td>${((page - 1) * perPage) + index + 1}</td>
                        <td>${escapeHtml(item.religion?.name || '-')}</td>
                        <td>${escapeHtml(truncateText(item.question, 80))}</td>
                        <td>${renderOptions(item.options)}</td>
                        <td>
                            <div class="table-actions">
                                <a href="${editUrl}/${item.id}/edit" class="btn-action edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn-action delete" title="Delete" data-id="${item.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                tableBody.querySelectorAll('.btn-action.delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        deleteStep(this.dataset.id, this);
                    });
                });

                applyResponsiveLabels();
            }

            function loadSteps(page = 1, religionId = '', search = '') {
                currentPage = page;
                currentReligion = religionId;
                currentSearch = search;

                fetch(buildFetchUrl(page, religionId, search), {
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
                        renderPagination(result, 'paginationWrapper', 'tableInfo', page => loadSteps(page, currentReligion, currentSearch));
                    })
                    .catch(error => {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load onboarding steps.</td></tr>';
                        renderPagination({}, 'paginationWrapper', 'tableInfo');
                        console.error(error);
                    });
            }

            function deleteStep(id, btn) {
                Swal.fire({
                    title: 'Delete Onboarding Step?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    btn.disabled = true;

                    fetch(`${destroyUrl}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) throw data;
                            return data;
                        })
                        .then(result => {
                            showToast(result.message || 'Onboarding step deleted successfully.', 'success');
                            loadSteps(currentPage, currentReligion, currentSearch);
                        })
                        .catch(error => {
                            btn.disabled = false;
                            const message = error?.message || 'Failed to delete onboarding step.';
                            showToast(message, 'error');
                        });
                });
            }

            if (religionFilter) {
                religionFilter.addEventListener('change', function() {
                    loadSteps(1, this.value, currentSearch);
                });
            }

            if (questionFilter) {
                let filterTimeout;
                questionFilter.addEventListener('input', function() {
                    const keyword = this.value.trim();
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => {
                        loadSteps(1, currentReligion, keyword);
                    }, 300);
                });
            }

            loadReligions();
            loadSteps(1, '', '');
        });
    </script>
@endpush
