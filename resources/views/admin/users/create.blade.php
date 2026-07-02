@extends('admin.layout.app')
@section('title', 'Add User')
@section('content')

<div class="page-content">
    <div class="content-card">

        <div class="card-header-custom">
            <h3 class="card-header-title">Add User</h3>

            <div class="card-header-actions">
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-accent">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card-body-custom">
            <form id="userForm" novalidate>
                <div class="row g-4">

                    <div class="col-md-6">
                        <label for="name" class="form-label-custom">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" id="name" name="name"
                            placeholder="e.g. John Doe" required>
                        <div class="invalid-feedback">Please enter user name.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label-custom">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email"
                            placeholder="e.g. john@example.com" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="user_type" class="form-label-custom">User Type <span class="text-danger">*</span></label>
                        <select class="form-select form-control-custom" id="user_type" name="user_type" required>
                            <option value="">Select User Type</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        <div class="invalid-feedback">Please select user type.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="provider" class="form-label-custom">Provider <span class="text-danger">*</span></label>
                        <select class="form-select form-control-custom" id="provider" name="provider" required>
                            <option value="">Select Provider</option>
                            <option value="google">Google</option>
                            <option value="apple">Apple</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select provider.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="provider_id" class="form-label-custom">Provider ID</label>
                        <input type="text" class="form-control form-control-custom" id="provider_id" name="provider_id"
                            placeholder="e.g. 123456789">
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-custom" id="password" name="password"
                            placeholder="Enter password" required>
                        <div class="invalid-feedback">Please enter password.</div>
                    </div>

                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                        <i class="bi bi-check-lg me-1"></i> Save User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const storeUrl = @json(route('users.store'));
        const indexUrl = @json(route('users.index'));
        const form = document.getElementById('userForm');
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
                    showToast(result.message || 'User created successfully.', 'success');
                    setTimeout(() => {
                        window.location.href = indexUrl;
                    }, 1500);
                })
                .catch(error => {
                    let message = 'Failed to save user.';

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
