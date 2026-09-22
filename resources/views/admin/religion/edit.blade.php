@extends('admin.layout.app')
@section('title', 'Edit Religion')
@section('content')

    <div class="page-content">
        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="card-header-title">Edit Religion</h3>

                <div class="card-header-actions">
                    <a href="{{ route('religions.index') }}" class="btn btn-sm btn-accent">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body-custom">
                <form id="religionForm" novalidate enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label-custom">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-custom" id="name" name="name" value="{{ $religion->name }}" placeholder="e.g. Islam" required>
                            <div class="invalid-feedback">Please enter a religion name.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label-custom">Image</label>
                            <input type="file" class="form-control form-control-custom" id="image" name="image" accept="image/*">
                            <div class="invalid-feedback">Please upload a valid image file.</div>
                            @if ($religion->image_url)
                                <div class="mt-2">
                                    <img src="{{ $religion->image_url }}" alt="{{ $religion->name }}" class="rounded border" style="max-height: 80px; max-width: 120px; object-fit: cover;" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23f1f5f9\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'12\' fill=\'%2394a3b8\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                                    <small class="d-block text-muted mt-1">Current image</small>
                                </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label-custom">Description</label>
                            <textarea class="form-control form-control-custom" id="description" name="description" rows="4" placeholder="Short description about this religion">{{ $religion->description }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $religion->status ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                            <i class="bi bi-check-lg me-1"></i> Update Religion
                        </button>
                        <a href="{{ route('religions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateUrl = @json(route('religions.update', $religion));
            const indexUrl = @json(route('religions.index'));
            const form = document.getElementById('religionForm');
            const submitBtn = document.getElementById('submitBtn');

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

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    showToast('Please fill in all required fields correctly.', 'warning');
                    return;
                }

                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1 text-white" style="color: white;"></span> <span class="text-white">Saving...</span>';


                const formData = new FormData(form);
                if (!formData.has('status')) {
                    formData.append('status', '0');
                }

                fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                    .then(response => response.json().then(data => {
                        if (!response.ok) throw data;
                        return data;
                    }))
                    .then(result => {
                        showToast(result.message || 'Religion updated successfully.', 'success');
                        setTimeout(() => {
                            window.location.href = indexUrl;
                        }, 1500);
                    })
                    .catch(error => {
                        let message = 'Failed to update religion.';

                        if (error?.errors) {
                            message = Object.values(error.errors).flat().join(' ');
                        } else if (error?.message) {
                            message = error.message;
                        }

                        showToast(message, 'error');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });
        });
    </script>
@endpush
