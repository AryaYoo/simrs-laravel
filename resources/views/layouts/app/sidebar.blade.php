<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased" style="background-color: #FBF6F6;">

    {{-- Wrapper dengan Alpine.js untuk toggle sidebar --}}
    <div x-data="{ sidebarOpen: {{ isset($hideSidebar) && $hideSidebar ? 'false' : "localStorage.getItem('sidebarOpen') !== 'false'" }} }"
         x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
         class="flex min-h-screen">

        {{-- ===== SIDEBAR ===== --}}
        <aside
            class="hidden lg:flex flex-col flex-shrink-0 sticky top-0 h-screen overflow-y-auto z-40 transition-all duration-300 ease-in-out"
            :style="{ 
                width: sidebarOpen ? '210px' : '72px', 
                backgroundColor: '#4C5C2D', 
                color: 'white', 
                borderRight: '1px solid rgba(0,0,0,0.1)' 
            }">

            {{-- SECTION: ADMIN (Olive) --}}
            <div style="background-color: #6A7E3F; color: white;">
                {{-- Brand / Logo & Toggle --}}
                <div class="flex items-center h-[64px] border-b border-white/10 overflow-hidden" :class="sidebarOpen ? 'px-4 gap-3' : 'justify-center px-0'">
                    
                    {{-- Toggle Button --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="flex-shrink-0 p-1.5 rounded-lg hover:bg-white/10 transition-colors text-white focus:outline-none"
                        title="Toggle Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Logo and Text --}}
                    <a x-show="sidebarOpen" href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 flex-1 min-w-0" style="text-decoration: none;"
                        x-transition:enter="transition opacity duration-300 delay-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100">
                        <span class="font-bold truncate text-white" style="font-size: 1rem; letter-spacing: 0.03em;">LaraLite</span>
                    </a>
                </div>

                {{-- Admin Menu Header --}}
                <div x-show="sidebarOpen" style="padding: 0.75rem 1rem 0.25rem 1rem;">
                    <p style="color: rgba(255, 255, 255, 0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                        {{ __('Admin Menu') }}
                    </p>
                </div>
                <div x-show="!sidebarOpen" class="h-4"></div>

                {{-- Admin Navigation --}}
                <nav class="pb-4" :class="sidebarOpen ? 'px-2' : 'px-2 flex flex-col items-center'">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="flex items-center rounded-md text-sm font-medium transition-colors"
                        :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                        style="color: white; text-decoration: none; background-color: {{ request()->routeIs('dashboard') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('dashboard') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                        title="{{ __('Dashboard') }}">
                        <flux:icon name="home" class="w-4 h-4 flex-shrink-0" />
                        <span x-show="sidebarOpen">{{ __('Dashboard') }}</span>
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('users.index') }}" wire:navigate
                            class="flex items-center rounded-md text-sm font-medium transition-colors"
                            :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="color: white; text-decoration: none; background-color: {{ request()->routeIs('users.index') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('users.index') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                            title="{{ __('Users') }}">
                            <flux:icon name="users" class="w-4 h-4 flex-shrink-0" />
                            <span x-show="sidebarOpen">{{ __('Users') }}</span>
                        </a>
                        <a href="{{ route('master-data.index') }}" wire:navigate
                            class="flex items-center rounded-md text-sm font-medium transition-colors"
                            :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="color: white; text-decoration: none; background-color: {{ request()->routeIs('master-data*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('master-data*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                            title="{{ __('Master Data') }}">
                            <flux:icon name="circle-stack" class="w-4 h-4 flex-shrink-0" />
                            <span x-show="sidebarOpen">{{ __('Master Data') }}</span>
                        </a>
                    @endif
                </nav>
            </div>

            {{-- SECTION: MAIN (Dark Olive) --}}
            <div class="flex-1 flex flex-col pt-2" style="background-color: #4C5C2D; color: white;">

            {{-- Main Menu Header --}}
            <div x-show="sidebarOpen" style="padding: 0.75rem 1rem 0.25rem 1rem;">
                <p style="color: rgba(255,255,255,0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                    {{ __('Main Menu') }}
                </p>
            </div>
            <div x-show="!sidebarOpen" class="h-4 border-t border-white/10 mx-2 mt-2 mb-2"></div>

                {{-- Main Navigation --}}
                <nav class="flex-1 overflow-y-auto" :class="sidebarOpen ? 'px-2' : 'px-2 flex flex-col items-center'">
                    <a href="{{ route('modul.index') }}" wire:navigate
                        class="flex items-center rounded-md text-sm font-medium transition-colors"
                        :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                        style="color: white; text-decoration: none; background-color: {{ request()->routeIs('modul*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('modul*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                        title="{{ __('Modul') }}">
                        <flux:icon name="cube" class="w-4 h-4 flex-shrink-0" />
                        <span x-show="sidebarOpen">{{ __('Modul') }}</span>
                    </a>
                </nav>

            <div class="flex-1"></div>

            {{-- Language Switcher --}}
            <div x-show="sidebarOpen" style="padding: 0.5rem 1rem; border-top: 1px solid rgba(255,255,255,0.15);">
                <div class="flex items-center gap-1">
                    <flux:icon name="language" class="size-3" style="color: rgba(255,255,255,0.5);" />
                    <span style="color: rgba(255,255,255,0.5); font-size: 0.65rem; margin-right: 4px;">{{ __('Language') }}:</span>
                    <a href="{{ route('lang.switch', 'id') }}" class="px-1.5 py-0.5 rounded text-xs transition-colors hover:bg-white/10"
                        style="{{ app()->getLocale() === 'id' ? 'background-color: rgba(255,255,255,0.25); color: white; font-weight:600;' : 'color: rgba(255,255,255,0.5);' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-1.5 py-0.5 rounded text-xs transition-colors hover:bg-white/10"
                        style="{{ app()->getLocale() === 'en' ? 'background-color: rgba(255,255,255,0.25); color: white; font-weight:600;' : 'color: rgba(255,255,255,0.5);' }}">EN</a>
                </div>
            </div>

            {{-- Bottom User Block --}}
            <div style="border-top: 1px solid rgba(255,255,255,0.15); background-color: rgba(0,0,0,0.1);"
                 :class="sidebarOpen ? 'p-3' : 'py-3 px-0 flex flex-col items-center gap-2'">
                <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2 mb-2" :class="!sidebarOpen ? 'justify-center mb-0' : ''" style="text-decoration: none;" title="{{ __('Settings') }}">
                    <div class="flex items-center justify-center rounded-full flex-shrink-0 font-bold"
                        style="background-color: rgba(255,255,255,0.25); width: 2rem; height: 2rem; color: white; font-size: 0.7rem;">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="overflow-hidden" x-show="sidebarOpen">
                        <p class="truncate font-semibold" style="color: white; font-size: 0.8rem; line-height: 1.2;">
                            {{ auth()->user()->fullname }}
                        </p>
                        <p class="truncate" style="color: rgba(255,255,255,0.6); font-size: 0.65rem; line-height: 1.2; text-transform: capitalize;">
                            {{ auth()->user()->role }}
                        </p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" :class="sidebarOpen ? 'w-full' : ''">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center transition-colors"
                        :class="sidebarOpen ? 'w-full gap-1.5 rounded-md text-xs font-medium py-1.5 hover:bg-white/20 bg-white/10' : 'w-10 h-10 rounded-full hover:bg-white/20 bg-white/10'"
                        style="color: white; border: none; cursor: pointer;"
                        title="{{ __('Log out') }}"
                        data-test="logout-button">
                        <svg class="flex-shrink-0" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="sidebarOpen" class="ml-1">{{ __('Log out') }}</span>
                    </button>
                </form>
            </div>
            </div>
        </aside>

        {{-- ===== MAIN AREA ===== --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- (Desktop Top Header Removed) --}}

            {{-- Mobile Header --}}
            <flux:header class="lg:hidden" style="background-color: #4C5C2D;">
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
                    confirmButtonColor: '#4C5C2D',
                    confirmButtonText: data.confirmButtonText ?? 'OK'
                });
            });
        });
    </script>
</body>

</html>