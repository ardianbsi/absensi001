@props(['title', 'subtitle' => null])
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $title }}</h2>
            @if($subtitle)
                <div class="text-muted mt-1">{{ $subtitle }}</div>
            @endif
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
