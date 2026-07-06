@extends('admin.layout.app')
@section('title', 'Questions')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="card-header-title">Questions</h3>

                <div class="card-header-actions">
                    <a href="{{ route('questions.create') }}" class="btn btn-sm btn-accent">
                        <i class="bi bi-plus me-1"></i> Add Question
                    </a>
                </div>
            </div>

            <div class="card-body-custom border-bottom">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label for="religionFilter" class="form-label">Religion</label>
                        <select id="religionFilter" class="form-select form-select-md">
                            <option value="">All Religions</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="timeSlotFilter" class="form-label">Time Slot</label>
                        <select id="timeSlotFilter" class="form-select form-select-md">
                            <option value="">All Time Slots</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select id="statusFilter" class="form-select form-select-md">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
                                <th>Time Slot</th>
                                <th>Question</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="questionTableBody">
                            <tr id="loadingRow">
                                <td colspan="7" class="text-center py-4 text-muted">Loading questions...</td>
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
            const fetchUrl = @json(route('questions.fetch'));
            const destroyUrl = @json(url('/questions'));
            const editUrl = @json(url('/questions'));

            const tableBody = document.getElementById('questionTableBody');
            const table = document.getElementById('dataTable');
            const tableInfo = document.getElementById('tableInfo');
            const paginationWrapper = document.getElementById('paginationWrapper');

            const religionFilter = document.getElementById('religionFilter');
            const timeSlotFilter = document.getElementById('timeSlotFilter');
            const statusFilter = document.getElementById('statusFilter');

            let allQuestions = [];
            let filteredQuestions = [];
            let currentPage = 1;
            const perPage = 10;

            function getCsrfToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function showToast(message, type) {
                if (type === 'success') {
                    toastr.success(message);
                } else if (type === 'error') {
                    toastr.error(message);
                } else if (type === 'warning') {
                    toastr.warning(message);
                } else {
                    toastr.info(message);
                }
            }

            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
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

            function getStatusBadge(status) {
                return status ?
                    '<span class="badge-status active">Active</span>' :
                    '<span class="badge-status inactive">Inactive</span>';
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

            async function loadAllQuestions() {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Loading questions...</td></tr>';

                try {
                    const firstPage = await fetchPage(1);
                    let collected = [...(firstPage.data || [])];
                    const lastPage = firstPage.last_page || 1;

                    for (let page = 2; page <= lastPage; page++) {
                        const nextPage = await fetchPage(page);
                        collected = collected.concat(nextPage.data || []);
                    }

                    allQuestions = collected;
                    filteredQuestions = [...allQuestions];

                    populateFilters(allQuestions);
                    renderClientPage(1);
                } catch (error) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load questions.</td></tr>';
                    if (tableInfo) tableInfo.textContent = 'Failed to load data.';
                    if (paginationWrapper) paginationWrapper.innerHTML = '';
                    showToast('Could not load questions. Please refresh the page.', 'error');
                    console.error(error);
                }
            }

            function populateFilters(questions) {
                const religionValues = [...new Set(
                    questions
                    .map(item => item.religion?.name)
                    .filter(Boolean)
                )].sort();

                const timeSlotValues = [...new Set(
                    questions
                    .map(item => item.time_slot?.name || item.time_slot?.title)
                    .filter(Boolean)
                )].sort();

                religionFilter.innerHTML = '<option value="">All Religions</option>' +
                    religionValues.map(name => `<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`).join('');

                timeSlotFilter.innerHTML = '<option value="">All Time Slots</option>' +
                    timeSlotValues.map(name => `<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`).join('');
            }

            function applyFilters() {
                const selectedReligion = religionFilter.value;
                const selectedTimeSlot = timeSlotFilter.value;
                const selectedStatus = statusFilter.value;

                filteredQuestions = allQuestions.filter(item => {
                    const religionName = item.religion?.name || '';
                    const timeSlotName = item.time_slot?.name || item.time_slot?.title || '';
                    const statusValue = String(Number(!!item.status));

                    const religionMatch = !selectedReligion || religionName === selectedReligion;
                    const timeSlotMatch = !selectedTimeSlot || timeSlotName === selectedTimeSlot;
                    const statusMatch = selectedStatus === '' || statusValue === selectedStatus;

                    return religionMatch && timeSlotMatch && statusMatch;
                });

                renderClientPage(1);
            }

            function renderRows(questions, page = 1) {
                if (!questions.length) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No questions found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = questions.map((item, index) => `
            <tr data-id="${item.id}">
                <td>${((page - 1) * perPage) + index + 1}</td>
                <td>${escapeHtml(item.religion?.name || '-')}</td>
                <td>${escapeHtml(item.time_slot?.name || item.time_slot?.title || ('Time Slot #' + item.time_slot_id))}</td>
                <td>${escapeHtml(truncateText(item.question, 80))}</td>
                <td>${getStatusBadge(item.status)}</td>
                <td>${formatDate(item.created_at)}</td>
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
                        deleteQuestion(this.dataset.id, this);
                    });
                });

                applyResponsiveLabels();
            }

            function updateTableInfo(total, pageRows, page) {
                if (!total || pageRows.length === 0) {
                    tableInfo.textContent = 'No questions found.';
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
                const pageRows = filteredQuestions.slice(start, end);

                renderRows(pageRows, page);
                updateTableInfo(filteredQuestions.length, pageRows, page);
                renderPagination(filteredQuestions.length, page);
            }

            function deleteQuestion(id, btn) {
                Swal.fire({
                    title: 'Delete Question?',
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

                    fetch(destroyUrl + '/' + id, {
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
                            showToast(result.message || 'Question deleted successfully.', 'success');

                            allQuestions = allQuestions.filter(item => item.id != id);
                            applyFilters();
                        })
                        .catch(error => {
                            btn.disabled = false;
                            const message = error?.message ||
                                (error?.errors && Object.values(error.errors).flat().join(' ')) ||
                                'Failed to delete question.';
                            showToast(message, 'error');
                        });
                });
            }

            if (religionFilter) {
                religionFilter.addEventListener('change', applyFilters);
            }

            if (timeSlotFilter) {
                timeSlotFilter.addEventListener('change', applyFilters);
            }

            if (statusFilter) {
                statusFilter.addEventListener('change', applyFilters);
            }

            loadAllQuestions();
        });
    </script>
@endpush
