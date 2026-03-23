<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased" style="background-color: #F4F7F6;">

    {{-- Wrapper dengan Alpine.js untuk toggle sidebar --}}
    <div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }"
         x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
         class="flex min-h-screen">

        {{-- ===== SIDEBAR ===== --}}
        <aside
            x-show="sidebarOpen"
            x-transition:enter="transition-all ease-in-out duration-200"
            x-transition:enter-start="opacity-0 -translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all ease-in-out duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-full"
            class="hidden lg:flex flex-col flex-shrink-0 sticky top-0 h-screen overflow-y-auto z-40"
            style="width: 210px; background-color: #1B9C85; color: white; border-right: 1px solid rgba(0,0,0,0.1);">

            {{-- Brand / Logo --}}
            <div style="padding: 1.1rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.15);" class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 flex-1 min-w-0" style="text-decoration: none;">
                    <div class="flex items-center justify-center rounded-md text-white flex-shrink-0"
                        style="background-color: rgba(255,255,255,0.2); width: 2rem; height: 2rem;">
                        <x-app-logo-icon class="fill-current" style="width: 1.2rem; height: 1.2rem; display:block;" />
                    </div>
                    <span class="font-bold truncate" style="color: white; font-size: 1rem; letter-spacing: 0.03em;">LaraLite</span>
                </a>
                {{-- Tombol tutup sidebar (desktop) --}}
                <button @click="sidebarOpen = false"
                    class="flex-shrink-0 p-1 rounded hover:bg-white/10 transition-colors"
                    title="Tutup Sidebar">
                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7M18 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            {{-- Admin Menu --}}
            <div style="padding: 0.75rem 1rem 0.25rem 1rem;">
                <p style="color: rgba(255,255,255,0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                    {{ __('Admin Menu') }}
                </p>
            </div>

            <nav style="padding: 0 0.5rem; flex: 1; overflow-y: auto;">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium mb-0.5 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'hover:bg-white/10' }}"
                    style="color: white; text-decoration: none;">
                    <flux:icon name="home" class="w-4 h-4 flex-shrink-0" />
                    <span>{{ __('Dashboard') }}</span>
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('users.index') }}" wire:navigate
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium mb-0.5 transition-colors {{ request()->routeIs('users.index') ? 'bg-white/20' : 'hover:bg-white/10' }}"
                        style="color: white; text-decoration: none;">
                        <flux:icon name="users" class="w-4 h-4 flex-shrink-0" />
                        <span>{{ __('Users') }}</span>
                    </a>
                    <a href="{{ route('master-data.index') }}" wire:navigate
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium mb-0.5 transition-colors {{ request()->routeIs('master-data*') ? 'bg-white/20' : 'hover:bg-white/10' }}"
                        style="color: white; text-decoration: none;">
                        <flux:icon name="circle-stack" class="w-4 h-4 flex-shrink-0" />
                        <span>{{ __('Master Data') }}</span>
                    </a>
                @endif
            </nav>

            {{-- Main Menu --}}
            <div style="padding: 0.75rem 1rem 0.25rem 1rem;">
                <p style="color: rgba(255,255,255,0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                    {{ __('Main Menu') }}
                </p>
            </div>

            <nav style="padding: 0 0.5rem;">
                <a href="{{ route('modul.index') }}" wire:navigate
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium mb-0.5 transition-colors {{ request()->routeIs('modul*') ? 'bg-white/20' : 'hover:bg-white/10' }}"
                    style="color: white; text-decoration: none;">
                    <flux:icon name="cube" class="w-4 h-4 flex-shrink-0" />
                    <span>{{ __('Modul') }}</span>
                </a>
            </nav>

            <div class="flex-1"></div>

            {{-- Language Switcher --}}
            <div style="padding: 0.5rem 1rem; border-top: 1px solid rgba(255,255,255,0.15);">
                <div class="flex items-center gap-1">
                    <flux:icon name="language" class="size-3" style="color: rgba(255,255,255,0.5);" />
                    <span style="color: rgba(255,255,255,0.5); font-size: 0.65rem; margin-right: 4px;">{{ __('Language') }}:</span>
                    <a href="{{ route('lang.switch', 'id') }}" class="px-1.5 py-0.5 rounded text-xs"
                        style="{{ app()->getLocale() === 'id' ? 'background-color: rgba(255,255,255,0.25); color: white; font-weight:600;' : 'color: rgba(255,255,255,0.5);' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-1.5 py-0.5 rounded text-xs"
                        style="{{ app()->getLocale() === 'en' ? 'background-color: rgba(255,255,255,0.25); color: white; font-weight:600;' : 'color: rgba(255,255,255,0.5);' }}">EN</a>
                </div>
            </div>

            {{-- Bottom User Block --}}
            <div style="padding: 0.75rem 1rem; border-top: 1px solid rgba(255,255,255,0.15); background-color: rgba(0,0,0,0.1);">
                <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2 mb-2" style="text-decoration: none;">
                    <div class="flex items-center justify-center rounded-full flex-shrink-0 font-bold"
                        style="background-color: rgba(255,255,255,0.25); width: 2rem; height: 2rem; color: white; font-size: 0.7rem;">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="truncate font-semibold" style="color: white; font-size: 0.8rem; line-height: 1.2;">
                            {{ auth()->user()->fullname }}
                        </p>
                        <p class="truncate" style="color: rgba(255,255,255,0.6); font-size: 0.65rem; line-height: 1.2; text-transform: capitalize;">
                            {{ auth()->user()->role }}
                        </p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 rounded-md text-xs font-medium py-1.5"
                        style="background-color: rgba(255,255,255,0.15); color: white; border: none; cursor: pointer; transition: background-color 0.15s;"
                        onmouseover="this.style.backgroundColor='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.backgroundColor='rgba(255,255,255,0.15)'"
                        data-test="logout-button">
                        <svg style="width:0.85rem;height:0.85rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('Log out') }}
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN AREA ===== --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- Top bar desktop: toggle button + conditional logo --}}
            <div class="hidden lg:flex items-center gap-3 px-4 py-2.5 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 sticky top-0 z-30">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors"
                    title="Toggle Sidebar">
                    <svg class="w-5 h-5 text-neutral-500 dark:text-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                {{-- Tampilkan logo LaraLite saat sidebar tertutup --}}
                <a x-show="!sidebarOpen" href="{{ route('dashboard') }}" wire:navigate
                   class="flex items-center gap-2" style="text-decoration: none;">
                    <div class="flex items-center justify-center rounded-md text-white flex-shrink-0"
                        style="background-color: #1B9C85; width: 1.8rem; height: 1.8rem;">
                        <x-app-logo-icon class="fill-current" style="width: 1rem; height: 1rem; display:block;" />
                    </div>
                    <span class="font-bold text-sm text-neutral-700 dark:text-neutral-200">LaraLite</span>
                </a>
            </div>

            {{-- Mobile Header --}}
            <flux:header class="lg:hidden" style="background-color: #1B9C85;">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" style="color: white;" />
                <span class="font-bold" style="color: white; font-size: 0.95rem;">LaraLite</span>
                <flux:spacer />
                <flux:dropdown position="top" align="end">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" style="color:white;" />
                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start">
                                    <flux:avatar :name="auth()->user()->fullname" :initials="auth()->user()->initials()" />
                                    <div class="grid flex-1 text-start leading-tight">
                                        <flux:heading class="truncate text-base font-semibold">{{ auth()->user()->fullname }}</flux:heading>
                                        <flux:text class="truncate text-sm text-zinc-500" style="text-transform:capitalize;">{{ auth()->user()->role }}</flux:text>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                {{ __('Settings') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer" data-test="logout-button">
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

            {{-- Page content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

    </div>{{-- end Alpine wrapper --}}

    @fluxScripts

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal', (event) => {
                const data = event[0];
                Swal.fire({
                    title: data.title ?? '',
                    text: data.text ?? '',
                    icon: data.icon ?? 'success',
                    confirmButtonColor: '#1B9C85',
                    confirmButtonText: data.confirmButtonText ?? 'OK'
                });
            });
        });
    </script>
</body>

</html>