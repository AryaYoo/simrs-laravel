<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="username" :label="__('Username')" type="text" disabled autocomplete="username" />
            <flux:input wire:model="fullname" :label="__('Full Name')" type="text" required autofocus
                autocomplete="name" />
            <flux:input wire:model="email" :label="__('Email')" type="email" autocomplete="email" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button type="submit" class="w-full text-white border-none shadow-md font-bold"
                        style="background-color: #4C5C2D; box-shadow: 0 4px 6px -1px rgba(76, 92, 45, 0.1);">
                        {{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>