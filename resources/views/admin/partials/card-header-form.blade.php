<div class="card-header-custom card-header-form">
    <h3 class="card-header-title">{{ $title }}</h3>
    <div class="card-header-logo">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Murids" class="card-header-logo-img">
    </div>
    <div class="card-header-actions">
        <a href="{{ $actionUrl ?? $backUrl ?? route('religions.index') }}" class="btn btn-sm btn-accent">
            <i class="bi {{ $actionIcon ?? 'bi-arrow-left' }} me-1"></i> {{ $actionLabel ?? $backLabel ?? 'Back to List' }}
        </a>
    </div>
</div>
