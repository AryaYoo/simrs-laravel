@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand logo="{{ asset('logo.png') }}" name="LARALITE" {{ $attributes }} />
@else
    <flux:brand logo="{{ asset('logo.png') }}" name="LARALITE" {{ $attributes }} />
@endif
