@if ($floating)
    <a
        href="{{ $resolvedLink() }}"
        target="_blank"
        rel="noopener noreferrer"
        title="Chat with us on WhatsApp"
        aria-label="Chat with us on WhatsApp"
        {{ $attributes->merge(['class' => 'whatsapp-float d-flex align-items-center justify-content-center']) }}
    >
        <i class="bi bi-whatsapp"></i>
    </a>
@else
    <a
        href="{{ $resolvedLink() }}"
        target="_blank"
        rel="noopener noreferrer"
        {{ $attributes->merge(['class' => 'btn btn-whatsapp']) }}
    >
        <i class="bi bi-whatsapp me-2"></i>
        Enquire on WhatsApp
    </a>
@endif