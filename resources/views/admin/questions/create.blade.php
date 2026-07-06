@extends('admin.layout.app')
@section('title', 'Add Question')
@section('content')

    <div class="page-content">
        <div class="content-card">

            <div class="card-header-custom">
                <h3 class="card-header-title">Add Question</h3>

                <div class="card-header-actions">
                    <a href="{{ route('questions.index') }}" class="btn btn-sm btn-accent">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card-body-custom">
                <form id="questionForm" novalidate>
                    <div class="row g-4">

                        <div class="col-md-6">
                            <label for="religion_id" class="form-label-custom">Religion <span class="text-danger">*</span></label>
                            <select class="form-select form-control-custom" id="religion_id" name="religion_id" required>
                                <option value="">Select Religion</option>
                                @foreach ($religions as $religion)
                                    <option value="{{ $religion->id }}">{{ $religion->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a religion.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="time_slot_id" class="form-label-custom">Time Slot <span class="text-danger">*</span></label>
                            <select class="form-select form-control-custom" id="time_slot_id" name="time_slot_id" required>
                                <option value="">Select Time Slot</option>
                                @foreach ($timeSlots as $timeSlot)
                                    <option value="{{ $timeSlot->id }}">{{ $timeSlot->name ?? ($timeSlot->title ?? 'Time Slot #' . $timeSlot->id) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a time slot.</div>
                        </div>

                        <div class="col-12">
                            <label for="question" class="form-label-custom">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-custom" id="question" name="question" rows="4" placeholder="Enter question here..." required></textarea>
                            <div class="invalid-feedback">Please enter a question.</div>
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
                            <i class="bi bi-check-lg me-1"></i> Save Question
                        </button>
                        <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const storeUrl = @json(route('questions.store'));
            const indexUrl = @json(route('questions.index'));
            const form = document.getElementById('questionForm');
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
                        showToast(result.message || 'Question created successfully.', 'success');
                        setTimeout(() => {
                            window.location.href = indexUrl;
                        }, 1500);
                    })
                    .catch(error => {
                        let message = 'Failed to save question.';

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
