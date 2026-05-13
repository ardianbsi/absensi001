@props(['items' => []])
<div class="mb-3">
    <ol class="breadcrumb" aria-label="breadcrumbs">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti ti-home"></i></a></li>
        @foreach($items as $item)
            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                @if(isset($item['route']) && !$loop->last)
                    <a href="{{ route($item['route']) }}">{{ $item['name'] }}</a>
                @else
                    {{ $item['name'] }}
                @endif
            </li>
        @endforeach
    </ol>
</div>
