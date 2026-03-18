<div class="flex flex-col gap-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">{{ $label }}</p>
    <p class="text-sm {{ isset($bold) && $bold ? 'font-semibold text-neutral-800 dark:text-neutral-100' : 'text-neutral-700 dark:text-neutral-300' }}">
        {{ $value ?: '-' }}
    </p>
</div>
