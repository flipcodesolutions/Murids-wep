@extends('admin.layout.app')
@section('title', 'Edit Onboarding Step')
@section('content')

<div class="page-content">
    <div class="content-card">
        <div class="card-header-custom">
            <h3 class="card-header-title">Edit Onboarding Step</h3>

            <div class="card-header-actions">
                <a href="{{ route('onboarding-steps.index') }}" class="btn btn-sm btn-accent">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card-body-custom">
            <form id="onboardingStepForm" novalidate>
                <div class="row g-4">

                    <div class="col-md-12">
                        <label for="religion_id" class="form-label-custom">Religion <span class="text-danger">*</span></label>
                        <select class="form-select form-control-custom" id="religion_id" name="religion_id" required>
                            <option value="">Select Religion</option>
                            @foreach ($religions as $religion)
                                <option value="{{ $religion->id }}" {{ $onboardingStep->religion_id == $religion->id ? 'selected' : '' }}>
                                    {{ $religion->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a religion.</div>
                    </div>

                    <div class="col-12">
                        <label for="question" class="form-label-custom">Question <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-custom" id="question" name="question" rows="4" placeholder="Enter question here..." required>{{ $onboardingStep->question }}</textarea>
                        <div class="invalid-feedback">Please enter a question.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="yes_response" class="form-label-custom">Option :- Yes</label>
                        <input type="text" class="form-control form-control-custom" id="yes_response" name="yes_response" value="{{ $onboardingStep->yes_response }}" placeholder="Enter response for Yes">
                    </div>

                    <div class="col-md-6">
                        <label for="no_response" class="form-label-custom">Option :- No</label>
                        <input type="text" class="form-control form-control-custom" id="no_response" name="no_response" value="{{ $onboardingStep->no_response }}" placeholder="Enter response for No">
                    </div>

                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                        <i class="bi bi-check-lg me-1"></i> Update Onboarding Step
                    </button>
                    <a href="{{ route('onboarding-steps.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateUrl = @json(route('onboarding-steps.update', $onboardingStep->id));
    const indexUrl = @json(route('onboarding-steps.index'));
    const form = document.getElementById('onboardingStepForm');
    const submitBtn = document.getElementById('submitBtn');

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

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            showToast('Please fill in all required fields correctly.', 'warning');
            return;
        }

        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1 text-white"></span> <span class="text-white">Updating...</span>';

        const formData = new FormData(form);
        formData.append('_method', 'PUT');

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
            showToast(result.message || 'Onboarding step updated successfully.', 'success');
            setTimeout(() => {
                window.location.href = indexUrl;
            }, 1500);
        })
        .catch(error => {
            let message = 'Failed to update onboarding step.';
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
