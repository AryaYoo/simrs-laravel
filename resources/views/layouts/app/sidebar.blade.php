<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased" style="background-color: #FBF6F6;">

    {{-- Wrapper dengan Alpine.js untuk toggle sidebar --}}
    <div x-data="{ 
            sidebarOpen: {{ isset($hideSidebar) && $hideSidebar ? 'false' : "localStorage.getItem('sidebarOpen') !== 'false'" }},
            modulOpen: {{ request()->is('modul*') ? 'true' : 'false' }}
         }" x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))" class="flex min-h-screen">

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
                <div class="flex items-center h-[64px] border-b border-white/10 overflow-hidden"
                    :class="sidebarOpen ? 'px-4 gap-3' : 'justify-center px-0'">

                    {{-- Toggle Button --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="flex-shrink-0 p-1.5 rounded-lg hover:bg-white/10 transition-colors text-white focus:outline-none"
                        title="Toggle Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Logo and Text --}}
                    <a x-show="sidebarOpen" href="{{ route('dashboard') }}" wire:navigate
                        class="flex items-center gap-2 flex-1 min-w-0" style="text-decoration: none;"
                        x-transition:enter="transition opacity duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <span class="font-bold truncate text-white"
                            style="font-size: 1rem; letter-spacing: 0.03em;">LaraLite</span>
                    </a>
                </div>

                {{-- Admin Menu Header --}}
                <div x-show="sidebarOpen" style="padding: 0.75rem 1rem 0.25rem 1rem;">
                    <p
                        style="color: rgba(255, 255, 255, 0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
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
                        
                        <a href="{{ route('admin.settings') }}" wire:navigate
                            class="flex items-center rounded-md text-sm font-medium transition-colors"
                            :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="color: white; text-decoration: none; background-color: {{ request()->routeIs('admin.settings') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.settings') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                            title="{{ __('Pengaturan Aplikasi') }}">
                            <flux:icon name="cog-8-tooth" class="w-4 h-4 flex-shrink-0" />
                            <span x-show="sidebarOpen">{{ __('Pengaturan') }}</span>
                        </a>
                    @endif
                </nav>
            </div>

            {{-- SECTION: MAIN (Dark Olive) --}}
            <div class="flex-1 flex flex-col pt-2" style="background-color: #4C5C2D; color: white;">

                {{-- Main Menu Header --}}
                <div x-show="sidebarOpen" style="padding: 0.75rem 1rem 0.25rem 1rem;">
                    <p
                        style="color: rgba(255,255,255,0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                        {{ __('Main Menu') }}
                    </p>
                </div>
                <div x-show="!sidebarOpen" class="w-8 h-px bg-white/10 mx-auto my-2"></div>

                {{-- Main Navigation --}}
                <nav class="overflow-y-auto" :class="sidebarOpen ? 'px-2' : 'px-2 flex flex-col items-center'">
                    {{-- Modul with Dropdown --}}
                    <div class="flex flex-col mb-1" :class="sidebarOpen ? 'w-full' : 'w-full items-center'">
                        <div class="flex items-center rounded-md text-sm font-medium transition-colors group"
                            :class="sidebarOpen ? 'w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="background-color: {{ request()->is('modul*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};">

                            <a href="{{ route('modul.index') }}" wire:navigate class="flex items-center grow"
                                :class="sidebarOpen ? 'gap-2 px-3 py-2' : 'justify-center w-full h-full'"
                                style="color: white; text-decoration: none;"
                                onmouseover="this.parentElement.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                                onmouseout="this.parentElement.style.backgroundColor='{{ request()->is('modul*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                                title="{{ __('Modul') }}">
                                <flux:icon name="cube" class="w-4 h-4 flex-shrink-0" />
                                <span x-show="sidebarOpen">{{ __('Modul') }}</span>
                            </a>

                            <button x-show="sidebarOpen" @click.prevent="modulOpen = !modulOpen"
                                class="p-2 mr-1 rounded-md hover:bg-white/10 transition-all text-white/50 hover:text-white"
                                :class="modulOpen ? 'rotate-180 text-white' : ''">
                                <flux:icon name="chevron-down" class="w-3 h-3" />
                            </button>
                        </div>

                        {{-- Sub-menus --}}
                        <div x-show="sidebarOpen && modulOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="ml-6 space-y-1 mt-1 mb-2 border-l border-white/10 pl-2">

                            <a href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.registrasi-pasien*') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.registrasi-pasien*') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="users" class="w-3.5 h-3.5" />
                                <span>Registrasi</span>
                            </a>

                            <a href="{{ route('modul.pasien.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.pasien*') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.pasien*') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="identification" class="w-3.5 h-3.5" />
                                <span>Pasien</span>
                            </a>

                            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.rawat-inap*') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.rawat-inap*') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="home" class="w-3.5 h-3.5" />
                                <span>Rawat Inap</span>
                            </a>

                            <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.rawat-jalan*') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.rawat-jalan*') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                                <span>Rawat Jalan</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            {{-- SECONDARY ZONE: BRIDGING & FOOTER (Darker Olive Zone) --}}
            <div class="flex-1 flex flex-col pt-4" style="background-color: #3E4A25; border-top: 1px solid rgba(0,0,0,0.1);">
                {{-- SECTION: BRIDGING (Integrated Style) --}}
                <div x-show="sidebarOpen" style="padding: 0.75rem 1rem 0.25rem 1rem;">
                    <p
                        style="color: rgba(255,255,255,0.4); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                        {{ __('Bridging') }}
                    </p>
                </div>
                <div x-show="!sidebarOpen" class="w-8 h-px bg-white/10 mx-auto my-2"></div>

                    {{-- Bridging Navigation --}}
                    <nav class="mb-2" :class="sidebarOpen ? 'px-2' : 'px-2 flex flex-col items-center'">
                        <a href="{{ route('bridging.erm-bpjs.index') }}" wire:navigate
                            class="flex items-center rounded-md text-sm font-medium transition-colors"
                            :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="color: white; text-decoration: none; background-color: {{ request()->routeIs('bridging.erm-bpjs*') ? 'rgba(255, 255, 255, 0.15)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('bridging.erm-bpjs*') ? 'rgba(255, 255, 255, 0.15)' : 'transparent' }}'"
                            title="{{ __('ERM BPJS') }}">
                            <flux:icon name="document-text" class="w-4 h-4 flex-shrink-0" />
                            <span x-show="sidebarOpen">{{ __('Rekam Medis (E-Claim)') }}</span>
                        </a>
                    </nav>

                <div class="flex-1"></div>

                {{-- SECTION: FOOTER (Darker Olive) --}}
                <div style="background-color: #2F381C; border-top: 1px solid rgba(255,255,255,0.05);">
                    {{-- Language Switcher (Compact) --}}
                    <div x-show="sidebarOpen" style="padding: 0.5rem 1rem 0.25rem 1rem;">
                        <div class="flex items-center gap-1 opacity-60 hover:opacity-100 transition-opacity">
                            <flux:icon name="language" class="size-3" style="color: white;" />
                            <span
                                style="color: white; font-size: 0.65rem; margin-right: 4px;">{{ __('Language') }}:</span>
                            <a href="{{ route('lang.switch', 'id') }}"
                                class="px-1.5 py-0.5 rounded text-[0.65rem] transition-colors hover:bg-white/10"
                                style="{{ app()->getLocale() === 'id' ? 'background-color: rgba(255,255,255,0.2); color: white; font-weight:600;' : 'color: rgba(255,255,255,0.5);' }}">ID</a>
                            <a href="{{ route('lang.switch', 'en') }}"
                                class="px-1.5 py-0.5 rounded text-[0.65rem] transition-colors hover:bg-white/10"
                                style="{{ app()->getLocale() === 'en' ? 'background-color: rgba(255,255,255,0.2); color: white; font-weight:600;' : 'color: rgba(255,255,255,0.5);' }}">EN</a>
                        </div>
                    </div>

                    {{-- Bottom User Block --}}
                    <div style="background-color: rgba(0,0,0,0.1);"
                        :class="sidebarOpen ? 'p-3' : 'py-4 px-0 flex flex-col items-center gap-3'">
                        <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2 mb-2"
                            :class="!sidebarOpen ? 'justify-center mb-0' : ''" style="text-decoration: none;"
                            title="{{ __('Settings') }}">
                            <div class="flex items-center justify-center rounded-full flex-shrink-0 font-bold shadow-inner"
                                style="background-color: rgba(255,255,255,0.2); width: 2.2rem; height: 2.2rem; color: white; font-size: 0.75rem; border: 1px solid rgba(255,255,255,0.1);">
                                {{ auth()->user()->initials() }}
                            </div>
                            <div class="overflow-hidden" x-show="sidebarOpen">
                                <p class="truncate font-semibold text-white"
                                    style="font-size: 0.8rem; line-height: 1.2;">
                                    {{ auth()->user()->fullname }}
                                </p>
                                <p class="truncate text-white/50"
                                    style="font-size: 0.65rem; line-height: 1.2; text-transform: capitalize;">
                                    {{ auth()->user()->role }}
                                </p>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" :class="sidebarOpen ? 'w-full' : ''">
                            @csrf
                            <button type="submit" class="flex items-center justify-center transition-colors shadow-sm"
                                :class="sidebarOpen ? 'w-full gap-1.5 rounded-lg text-[0.7rem] font-semibold py-2 hover:bg-white/20 bg-white/10' : 'w-9 h-9 rounded-full hover:bg-white/20 bg-white/10'"
                                style="color: white; border: 1px solid rgba(255,255,255,0.05); cursor: pointer;"
                                title="{{ __('Log out') }}" data-test="logout-button">
                                <flux:icon name="arrow-right-start-on-rectangle" class="size-4" />
                                <span x-show="sidebarOpen" class="ml-1">{{ __('Log out') }}</span>
                            </button>
                        </form>
                    </div>
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
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down"
                        style="color:white;" />
                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start">
                                    <flux:avatar :name="auth()->user()->fullname"
                                        :initials="auth()->user()->initials()" />
                                    <div class="grid flex-1 text-start leading-tight">
                                        <flux:heading class="truncate text-base font-semibold">
                                            {{ auth()->user()->fullname }}</flux:heading>
                                        <flux:text class="truncate text-sm text-zinc-500"
                                            style="text-transform:capitalize;">{{ auth()->user()->role }}</flux:text>
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
                    html: data.html,
                    icon: data.icon ?? 'success',
                    confirmButtonColor: '#4C5C2D',
                    confirmButtonText: data.confirmButtonText ?? 'OK'
                });
            });
        });
    </script>
</body>

</html>