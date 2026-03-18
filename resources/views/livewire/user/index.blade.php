<div class="p-6 md:p-8">
    <div class="flex w-full justify-between items-center mb-6">
        <div class="text-left">
            <h1 class="text-xl font-bold" style="color: #4C4C6D;">{{ __('User Management') }}</h1>
            <p class="text-sm" style="color: rgba(76, 76, 109, 0.55);">
                {{ __('View, search, and manage system users.') }}
            </p>
        </div>
        <div>
            <button wire:click="toggleCreateForm" class="btn-primary flex items-center gap-2">
                @if($showCreateForm)
                    <flux:icon.x-mark class="w-4 h-4" /> {{ __('Cancel') }}
                @else
                    <flux:icon.plus class="w-4 h-4" /> {{ __('Add User') }}
                @endif
            </button>
        </div>
    </div>

    @if($showCreateForm)
        <div class="content-card mb-8 transition-all duration-300">
            <div class="mb-6 pb-4 border-b" style="border-color: rgba(76, 76, 109, 0.1);">
                <h2 class="text-lg font-bold" style="color: #4C4C6D;">
                    {{ $editId ? __('Edit User') : __('Create New User') }}
                </h2>
            </div>

            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <flux:input wire:model="username" :label="__('Username')" required placeholder="johndoe" />
                    <flux:input wire:model="fullname" :label="__('Full Name')" required placeholder="John Doe" />
                    <flux:input wire:model="email" type="email" :label="__('Email address')" required
                        placeholder="john@example.com" />
                    <flux:select wire:model="role" :label="__('Role')" required>
                        <flux:select.option value="user">{{ __('User') }}</flux:select.option>
                        <flux:select.option value="admin">{{ __('Administrator') }}</flux:select.option>
                    </flux:select>
                </div>

                <flux:input wire:model="password" type="password" :label="__('Password')" :required="!$editId"
                    placeholder="{{ $editId ? __('Leave blank to keep current password') : '' }}" viewable />
                <flux:input wire:model="cap" :label="__('Cap / Position')"
                    placeholder="{{ __('e.g. Kepala Bagian IT') }}" />

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: #4C4C6D;">{{ __('Description') }}</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full rounded-lg border px-3 py-2 text-sm resize-none focus:outline-none"
                        style="border-color: rgba(76,76,109,0.2); color: #4C4C6D; background-color: #fff;"
                        placeholder="{{ __('Short bio or notes about this user...') }}"></textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" wire:click="toggleCreateForm" class="btn-ghost">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn-primary">
                        {{ $editId ? __('Update User') : __('Save User') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="content-card">
        <div class="mb-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users...') }}"
                icon="magnifying-glass" clearable />
        </div>

        <div class="rounded-xl border overflow-hidden" style="border-color: rgba(76, 76, 109, 0.12);">
            <table class="w-full text-sm text-left align-middle" style="color: #4C4C6D;">
                <thead style="background: rgba(76, 76, 109, 0.03); border-bottom: 0.5px solid rgba(76, 76, 109, 0.1);">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-xs" style="color: rgba(76, 76, 109, 0.55);">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-xs" style="color: rgba(76, 76, 109, 0.55);">
                            {{ __('Username') }}
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-xs" style="color: rgba(76, 76, 109, 0.55);">
                            {{ __('Role') }}
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-xs" style="color: rgba(76, 76, 109, 0.55);">
                            {{ __('Position (Cap)') }}
                        </th>
                        <th scope="col" class="px-4 py-3 font-medium text-xs text-right"
                            style="color: rgba(76, 76, 109, 0.55);">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/40 transition-colors"
                            style="border-bottom: 0.5px solid rgba(76, 76, 109, 0.07);">
                            {{-- Name & Email --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0"
                                        style="background: #EEEDFE; color: #3C3489;">
                                        {{ strtoupper(substr($user->fullname, 0, 1)) }}{{ strtoupper(substr(strrchr($user->fullname, ' '), 1, 1)) }}
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <span class="font-medium text-sm"
                                            style="color: #4C4C6D;">{{ $user->fullname }}</span>
                                        <span class="text-xs"
                                            style="color: rgba(76, 76, 109, 0.55);">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Username --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm" style="color: rgba(76, 76, 109, 0.65);">
                                {{ $user->username }}
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                        style="background: #EEEDFE; color: #3C3489;">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border"
                                        style="background: transparent; color: rgba(76, 76, 109, 0.6); border-color: rgba(76, 76, 109, 0.18);">
                                        User
                                    </span>
                                @endif
                            </td>

                            {{-- Cap --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm" style="color: rgba(76, 76, 109, 0.65);">
                                {{ $user->cap ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button type="button" wire:click="edit({{ $user->id }})"
                                    class="w-8 h-8 rounded-lg border inline-flex items-center justify-center transition-all duration-150 cursor-pointer"
                                    style="color: rgba(76, 76, 109, 0.5); border-color: rgba(76, 76, 109, 0.15);"
                                    onmouseover="this.style.background='#EEEDFE';this.style.color='#3C3489';this.style.borderColor='#AFA9EC'"
                                    onmouseout="this.style.background='';this.style.color='rgba(76,76,109,0.5)';this.style.borderColor='rgba(76,76,109,0.15)'"
                                    title="{{ __('Edit') }}">
                                    <flux:icon.pencil-square class="w-4 h-4 pointer-events-none" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center" style="color: rgba(76, 76, 109, 0.45);">
                                <flux:icon.users class="w-7 h-7 mx-auto mb-2 opacity-40" />
                                <p class="text-sm">{{ __('No users found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>