@php
    $icon = match ($type ?? 'success') {
        'error' => 'bi-exclamation-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'info' => 'bi-info-circle-fill',
        default => 'bi-check-circle-fill',
    };
    $dissmissible = isset($dismissible) ? $dismissible : true;
@endphp

@if (! empty($message))
    <div {{ $attributes->merge(['class' => 'alert alert-'.($type ?? 'success').' d-flex align-items-center rounded-3']) }} role="alert">
        <i class="bi {{ $icon }} me-2"></i>
        <div class="flex-grow-1">{{ $message }}</div>
        @if ($dissmissible)
            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
    </div>
@endif