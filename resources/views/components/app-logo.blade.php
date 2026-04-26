@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand logo="{{ asset('logo.png') }}" name="Casemix Rawat Inap" {{ $attributes }} />
@else
    <flux:brand logo="{{ asset('logo.png') }}" name="Casemix Rawat Inap" {{ $attributes }} />
@endif
