@if (isset($paginator) && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'd-flex justify-content-center mt-4']) }}>
        {{ $paginator->links() }}
    </div>
@endif