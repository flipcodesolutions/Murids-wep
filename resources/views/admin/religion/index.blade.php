@extends('admin.layout.app')
@section('title', 'Religions')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <div class="card-header-main">
                    <h3 class="card-header-title">Religions</h3>
                </div>

                <div class="card-header-toolbar single-row-toolbar">
                    <div class="search-box-wrap">
                        <span class="search-box-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="tableSearch" class="form-control form-control-sm search-box-input" placeholder="Search religions...">
                    </div>

                    <a href="{{ route('religions.create') }}" class="btn btn-sm btn-accent add-action-btn">
                        <i class="bi bi-plus-lg me-1"></i>
                        <span>Add Religion</span>
                    </a>
                </div>
            </div>

            <div class="card-body-custom p-0">
                <div class="table-scroll-wrap">
                    <table class="table table-custom table-stack-mobile mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="religionTableBody">
                            <tr id="loadingRow">
                                <td colspan="6" class="text-center py-4 text-muted">Loading religions...</td>
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
            const fetchUrl = @json(route('religions.fetch'));
            const destroyUrl = @json(url('/religions'));
            const editUrl = @json(url('/religions'));
            const storageBaseUrl = @json(asset('storage'));
            const noImageUrl = @json(asset('images/no-image.png'));
            const tableBody = document.getElementById('religionTableBody');
            const table = document.getElementById('dataTable');
            const searchInput = document.getElementById('tableSearch');

            let currentPage = 1;
            let currentSearch = '';

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

            function getImageUrl(imagePath) {
                if (!imagePath) return noImageUrl;
                if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
                    return imagePath;
                }
                return `${storageBaseUrl}/${imagePath.replace(/^\/+/, '')}`;
            }

            function renderRows(religions, page = 1, perPage = 10) {
                if (!religions.length) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No religions found. Add your first religion.</td></tr>';
                    return;
                }

                tableBody.innerHTML = religions.map((religion, index) => `
                    <tr data-id="${religion.id}">
                        <td>${((page - 1) * perPage) + index + 1}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img
                                    src="${getImageUrl(religion.image)}"
                                    alt="${escapeHtml(religion.name)}"
                                    width="40"
                                    height="40"
                                    style="object-fit:cover; border-radius:6px;"
                                    onerror="this.onerror=null;this.src='${noImageUrl}';"
                                >
                                <span>${escapeHtml(religion.name)}</span>
                            </div>
                        </td>
                        <td>${escapeHtml(truncateText(religion.description, 60))}</td>
                        <td>${getStatusBadge(religion.status)}</td>
                        <td>${formatDate(religion.created_at)}</td>
                        <td>
                            <div class="table-actions">
                                <a href="${editUrl}/${religion.id}/edit" class="btn-action edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn-action delete" title="Delete" data-id="${religion.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                tableBody.querySelectorAll('.btn-action.delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        deleteReligion(this.dataset.id, this);
                    });
                });

                applyResponsiveLabels();
            }

            function loadReligions(page = 1, search = '') {
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
                    .then(response => response.json().then(data => {
                        if (!response.ok) throw data;
                        return data;
                    }))
                    .then(result => {
                        renderRows(result.data || [], result.current_page, result.per_page);
                        renderPagination(result, 'paginationWrapper', 'tableInfo', page => loadReligions(page, currentSearch));
                    })
                    .catch(error => {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load religions.</td></tr>';
                        renderPagination({}, 'paginationWrapper', 'tableInfo');
                        showToast('Could not load religions. Please refresh the page.', 'error');
                        console.error(error);
                    });
            }

            function deleteReligion(id, btn) {
                Swal.fire({
                    title: 'Delete Religion?',
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
                        .then(response => response.json().then(data => {
                            if (!response.ok) throw data;
                            return data;
                        }))
                        .then(res => {
                            showToast(res.message || 'Religion deleted successfully.', 'success');
                            loadReligions(currentPage, currentSearch);
                        })
                        .catch(error => {
                            btn.disabled = false;
                            const message = error?.message || 'Failed to delete religion.';
                            showToast(message, 'error');
                        });
                });
            }

            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    const keyword = this.value.trim();
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        loadReligions(1, keyword);
                    }, 300);
                });
            }

            loadReligions(1, '');
        });
    </script>
@endpush
