@extends('admin.layout.app')
@section('title', 'Add Religion')
@section('content')

    <div class="page-content">
        <div class="content-card">

            <div class="card-header-custom">
                <h3 class="card-header-title">Add Religion</h3>

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
                            <input type="text" class="form-control form-control-custom" id="name" name="name" placeholder="e.g. Islam" required>
                            <div class="invalid-feedback">Please enter a religion name.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label-custom">Image</label>
                            <input type="file" class="form-control form-control-custom" id="image" name="image" accept="image/*">
                            <div class="invalid-feedback">Please upload a valid image file.</div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label-custom">Description</label>
                            <textarea class="form-control form-control-custom" id="description" name="description" rows="4" placeholder="Short description about this religion"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                            <i class="bi bi-check-lg me-1"></i> Save Religion
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
            const storeUrl = @json(route('religions.store'));
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
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1 text-white" style="color: white;"></span> <span class="text-white">Saving...</span>';

                const formData = new FormData(form);
                if (!formData.has('status')) {
                    formData.append('status', '0');
                }

                fetch(storeUrl, {
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
                        showToast(result.message || 'Religion created successfully.', 'success');
                        setTimeout(() => {
                            window.location.href = indexUrl;
                        }, 1500);
                    })
                    .catch(error => {
                        let message = 'Failed to save religion.';

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
