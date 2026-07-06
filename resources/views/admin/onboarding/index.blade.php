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
                        <label for="stepFilter" class="form-label">Step No</label>
                        <select id="stepFilter" class="form-select form-select-md">
                            <option value="">All Steps</option>
                        </select>
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
                                <th>Step No</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="stepTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Loading onboarding steps...</td>
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

            const tableBody = document.getElementById('stepTableBody');
            const table = document.getElementById('dataTable');
            const tableInfo = document.getElementById('tableInfo');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const religionFilter = document.getElementById('religionFilter');
            const stepFilter = document.getElementById('stepFilter');

            let allSteps = [];
            let filteredSteps = [];
            let currentPage = 1;
            const perPage = 10;

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
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.querySelectorAll('td').forEach((td, index) => {
                        if (headers[index]) td.setAttribute('data-label', headers[index]);
                    });
                });
            }

            function renderOptions(options) {
                if (!Array.isArray(options) || !options.length) return '-';
                return options.map(option => `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(option)}</span>`).join('');
            }

            async function fetchAll() {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Loading onboarding steps...</td></tr>';

                try {
                    const response = await fetch(fetchUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json();
                    if (!response.ok) throw data;

                    allSteps = data.data || data;
                    filteredSteps = [...allSteps];

                    populateFilters();
                    renderPage(1);
                } catch (error) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load onboarding steps.</td></tr>';
                    tableInfo.textContent = 'Failed to load data.';
                    paginationWrapper.innerHTML = '';
                    showToast('Could not load onboarding steps.', 'error');
                }
            }

            function populateFilters() {
                const religionValues = [...new Set(allSteps.map(item => item.religion?.name).filter(Boolean))].sort();
                const stepValues = [...new Set(allSteps.map(item => item.step_no).filter(Boolean))].sort((a, b) => a - b);

                religionFilter.innerHTML = '<option value="">All Religions</option>' +
                    religionValues.map(name => `<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`).join('');

                stepFilter.innerHTML = '<option value="">All Steps</option>' +
                    stepValues.map(step => `<option value="${step}">${step}</option>`).join('');
            }

            function applyFilters() {
                const selectedReligion = religionFilter.value;
                const selectedStep = stepFilter.value;

                filteredSteps = allSteps.filter(item => {
                    const religionMatch = !selectedReligion || (item.religion?.name || '') === selectedReligion;
                    const stepMatch = !selectedStep || String(item.step_no) === selectedStep;
                    return religionMatch && stepMatch;
                });

                renderPage(1);
            }

            function renderRows(rows, page = 1) {
                if (!rows.length) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No onboarding steps found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = rows.map((item, index) => `
            <tr data-id="${item.id}">
                <td>${((page - 1) * perPage) + index + 1}</td>
                <td>${escapeHtml(item.religion?.name || '-')}</td>
                <td>${escapeHtml(item.step_no || '-')}</td>
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

            function updateTableInfo(total, pageRows, page) {
                if (!total || pageRows.length === 0) {
                    tableInfo.textContent = 'No onboarding steps found.';
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
                html += `<button type="button" class="page-btn nav-btn" data-page="${current - 1}" ${current === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;

                for (let i = 1; i <= last; i++) {
                    html += `<button type="button" class="page-btn ${i === current ? 'active' : ''}" data-page="${i}">${i}</button>`;
                }

                html += `<button type="button" class="page-btn nav-btn" data-page="${current + 1}" ${current === last ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
                html += `</div>`;

                paginationWrapper.innerHTML = html;

                paginationWrapper.querySelectorAll('.page-btn[data-page]').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.disabled) return;
                        const page = parseInt(this.dataset.page);
                        if (!isNaN(page)) renderPage(page);
                    });
                });
            }

            function renderPage(page = 1) {
                currentPage = page;
                const start = (page - 1) * perPage;
                const end = start + perPage;
                const pageRows = filteredSteps.slice(start, end);

                renderRows(pageRows, page);
                updateTableInfo(filteredSteps.length, pageRows, page);
                renderPagination(filteredSteps.length, page);
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
                            allSteps = allSteps.filter(item => item.id != id);
                            applyFilters();
                        })
                        .catch(error => {
                            btn.disabled = false;
                            const message = error?.message || 'Failed to delete onboarding step.';
                            showToast(message, 'error');
                        });
                });
            }

            religionFilter.addEventListener('change', applyFilters);
            stepFilter.addEventListener('change', applyFilters);

            fetchAll();
        });
    </script>
@endpush
