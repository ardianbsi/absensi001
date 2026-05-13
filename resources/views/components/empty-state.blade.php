@props(['icon' => 'ti ti-box', 'title' => 'No data found', 'message' => 'There are no records to display.', 'action' => null])
<div class="empty">
    <div class="empty-icon">
        <i class="{{ $icon }} text-muted" style="font-size: 3rem;"></i>
    </div>
    <p class="empty-title h3">{{ $title }}</p>
    <p class="empty-subtitle text-muted">{{ $message }}</p>
    @if($action)
        <div class="empty-action">
            {{ $action }}
        </div>
    @endif
</div>
