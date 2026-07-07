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

                    <div class="col-md-6">
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

                    <div class="col-12">
                        <label class="form-label-custom">Options <span class="text-danger">*</span></label>
                        <div id="optionsWrapper">
                            @forelse (($onboardingStep->options ?? []) as $option)
                                <div class="input-group mb-2 option-row">
                                    <input type="text" name="options[]" class="form-control form-control-custom" value="{{ $option }}" placeholder="Enter option" required>
                                    <button type="button" class="btn btn-outline-danger removeOptionBtn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="input-group mb-2 option-row">
                                    <input type="text" name="options[]" class="form-control form-control-custom" placeholder="Enter option" required>
                                    <button type="button" class="btn btn-outline-danger removeOptionBtn d-none">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endforelse
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addOptionBtn">
                            <i class="bi bi-plus-lg me-1"></i> Add Option
                        </button>
                        <div class="invalid-feedback d-block" id="optionsError"></div>
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
    const optionsWrapper = document.getElementById('optionsWrapper');
    const addOptionBtn = document.getElementById('addOptionBtn');
    const optionsError = document.getElementById('optionsError');

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

    function bindRemoveButtons() {
        document.querySelectorAll('.removeOptionBtn').forEach(btn => {
            btn.onclick = function() {
                const rows = document.querySelectorAll('.option-row');
                if (rows.length > 1) {
                    this.closest('.option-row').remove();
                    toggleRemoveButtons();
                }
            };
        });
    }

    function toggleRemoveButtons() {
        const rows = document.querySelectorAll('.option-row');
        rows.forEach(row => {
            const btn = row.querySelector('.removeOptionBtn');
            if (btn) btn.classList.toggle('d-none', rows.length === 1);
        });
    }

    addOptionBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'input-group mb-2 option-row';
        div.innerHTML = `
            <input type="text" name="options[]" class="form-control form-control-custom" placeholder="Enter option" required>
            <button type="button" class="btn btn-outline-danger removeOptionBtn">
                <i class="bi bi-trash"></i>
            </button>
        `;
        optionsWrapper.appendChild(div);
        bindRemoveButtons();
        toggleRemoveButtons();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        optionsError.textContent = '';

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            showToast('Please fill in all required fields correctly.', 'warning');
            return;
        }

        const optionInputs = [...document.querySelectorAll('input[name="options[]"]')];
        const validOptions = optionInputs.map(input => input.value.trim()).filter(Boolean);

        if (!validOptions.length) {
            optionsError.textContent = 'Please add at least one option.';
            showToast('Please add at least one option.', 'warning');
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

    bindRemoveButtons();
    toggleRemoveButtons();
});
</script>
@endpush
