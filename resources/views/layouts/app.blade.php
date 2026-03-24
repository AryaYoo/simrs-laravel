<x-layouts::app.sidebar :title="$title ?? null" :hideSidebar="$hideSidebar ?? false">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
