<div class="d-flex gap-2">
    @foreach ($actions as $action)
        @if (!empty($action['onclick']))
            <button class="btn btn-sm {{ $action['class'] ?? '' }} square-icon-btn"
                title="{{ $action['title'] ?? '' }}"
                onclick="{{ $action['onclick'] }}">
                <i class="{{ $action['icon'] ?? '' }} fa-lg"></i>
            </button>
        @endif
    @endforeach
</div>