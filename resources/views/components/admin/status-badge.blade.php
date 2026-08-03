@props(['status', 'activeLabel' => 'Active', 'inactiveLabel' => 'Inactive'])

<span {{ $attributes->merge(['class' => 'status-badge '.($status ? 'active' : 'inactive')]) }}>
    {{ $status ? $activeLabel : $inactiveLabel }}
</span>
