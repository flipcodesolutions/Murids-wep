@extends('admin.layout.app')
@section('title', 'Religions')
@section('content')

      <div class="page-content">
        <div class="content-card">
        <div class="card-header-custom">
            <h3 class="card-header-title">Add Religion</h3>
            
            <div class="card-header-actions">
              
              <input type="text" id="tableSearch" class="form-control form-control-sm" placeholder="Search religions...">
              <a href="{{ route('religions.index') }}" class="btn btn-sm btn-accent">
                <i class="bi bi-arrow-left me-1"></i> Back to List
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
            <span class="text-muted" id="tableInfo" style="font-size: 0.85rem;">Loading...</span>
          </div>
        </div>
      </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const fetchUrl = @json(route('religions.fetch'));
  const destroyUrl = @json(url('/religions'));
  const editUrl = @json(url('/religions'));
  const tableBody = document.getElementById('religionTableBody');
  const table = document.getElementById('dataTable');

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
    div.textContent = text;
    return div.innerHTML;
  }

  function getStatusBadge(status) {
    return status
      ? '<span class="badge-status active">Active</span>'
      : '<span class="badge-status inactive">Inactive</span>';
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

  function updateTableInfo(count) {
    const info = document.getElementById('tableInfo');
    if (!info) return;
    info.textContent = count === 0
      ? 'No religions found.'
      : 'Showing ' + count + ' of ' + count + ' entries';
  }

  function renderRows(religions) {
    if (!religions.length) {
      tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No religions found. Add your first religion.</td></tr>';
      return;
    }

    tableBody.innerHTML = religions.map((religion, index) => `
      <tr data-id="${religion.id}">
        <td>${index + 1}</td>
        <td>${escapeHtml(religion.name)}</td>
        <td>${escapeHtml(truncateText(religion.description, 60))}</td>
        <td>${getStatusBadge(religion.status)}</td>
        <td>${formatDate(religion.created_at)}</td>
        <td>
          <div class="table-actions">
            <a href="${editUrl}/${religion.id}/edit" class="btn-action edit" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <button class="btn-action delete" title="Delete" data-id="${religion.id}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    tableBody.querySelectorAll('.btn-action.delete').forEach(btn => {
      btn.addEventListener('click', function () {
        deleteReligion(this.dataset.id, this);
      });
    });
  }

  function loadReligions() {
    fetch(fetchUrl, {
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
        renderRows(result.data || []);
        applyResponsiveLabels();
        updateTableInfo((result.data || []).length);
      })
      .catch(error => {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load religions.</td></tr>';
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

      const row = btn.closest('tr');
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
        .then(response => response.json().then(data => {
          if (!response.ok) throw data;
          return data;
        }))
        .then(result => {
          if (row) {
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            setTimeout(() => {
              row.remove();
              const remainingRows = document.querySelectorAll('#religionTableBody tr[data-id]');
              updateTableInfo(remainingRows.length);

              if (remainingRows.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No religions found. Add your first religion.</td></tr>';
              } else {
                remainingRows.forEach((tr, index) => {
                  tr.querySelector('td:first-child').textContent = index + 1;
                });
              }
            }, 300);
          }

          showToast(result.message || 'Religion deleted successfully.', 'success');
        })
        .catch(error => {
          btn.disabled = false;
          const message = error?.message
            || (error?.errors && Object.values(error.errors).flat().join(' '))
            || 'Failed to delete religion.';
          showToast(message, 'error');
        });
    });
  }

  loadReligions();
});
</script>
@endpush
