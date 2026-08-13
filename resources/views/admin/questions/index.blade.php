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
            const religionsUrl = @json(route('religions.fetch'));

            const tableBody = document.getElementById('questionTableBody');
            const table = document.getElementById('dataTable');
            const religionFilter = document.getElementById('religionFilter');
            const timeSlotFilter = document.getElementById('timeSlotFilter');
            const statusFilter = document.getElementById('statusFilter');

            let currentPage = 1;

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

            function populateReligionsFilter() {
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

            function buildFetchUrl(page = 1) {
                const url = new URL(fetchUrl, window.location.origin);
                url.searchParams.set('page', page);

                if (religionFilter && religionFilter.value) {
                    url.searchParams.set('religion_id', religionFilter.value);
                }
                if (timeSlotFilter && timeSlotFilter.value) {
                    url.searchParams.set('time_slot_id', timeSlotFilter.value);
                }
                if (statusFilter && statusFilter.value !== '') {
                    url.searchParams.set('status', statusFilter.value);
                }

                return url.toString();
            }

            function renderRows(questions, page = 1, perPage = 10) {
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

            function loadQuestions(page = 1) {
                currentPage = page;
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Loading questions...</td></tr>';

                fetch(buildFetchUrl(page), {
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
                    renderPagination(result, 'paginationWrapper', 'tableInfo', page => loadQuestions(page));
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load questions.</td></tr>';
                    renderPagination({}, 'paginationWrapper', 'tableInfo');
                    showToast('Could not load questions.', 'error');
                    console.error(error);
                });
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
                            loadQuestions(currentPage);
                        })
                        .catch(error => {
                            btn.disabled = false;
                            const message = error?.message || 'Failed to delete question.';
                            showToast(message, 'error');
                        });
                });
            }

            if (religionFilter) religionFilter.addEventListener('change', () => loadQuestions(1));
            if (timeSlotFilter) timeSlotFilter.addEventListener('change', () => loadQuestions(1));
            if (statusFilter) statusFilter.addEventListener('change', () => loadQuestions(1));

            populateReligionsFilter();
            loadQuestions(1);
        });
    </script>
@endpush
