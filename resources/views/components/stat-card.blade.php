@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null])
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="subheader">{{ $title }}</div>
            <div class="ms-auto lh-1">
                <div class="dropdown">
                    <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-baseline">
            <div class="h1 mb-3 me-2">{{ $value }}</div>
            @if($trend)
                <span class="badge bg-{{ $trend['direction'] === 'up' ? 'green' : 'red' }} ms-auto">
                    <i class="ti ti-trending-{{ $trend['direction'] }}"></i> {{ $trend['percentage'] }}%
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center">
            <span class="avatar avatar-sm bg-{{ $color }}-lt me-2"><i class="{{ $icon }}"></i></span>
            <span class="text-muted">{{ $slot }}</span>
        </div>
    </div>
</div>
