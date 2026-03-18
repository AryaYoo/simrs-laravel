@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="LaraLite" {{ $attributes }} />
@else
    <flux:brand name="LaraLite" {{ $attributes }} />
@endif
