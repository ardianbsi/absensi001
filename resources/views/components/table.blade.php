@props(['headers' => [], 'rows' => [], 'actions' => true, 'responsive' => true])
<div class="table-responsive">
    <table class="table table-vcenter card-table table-striped">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
                @if($actions)
                    <th class="w-1">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
