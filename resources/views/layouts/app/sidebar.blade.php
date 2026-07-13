<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        /* Force SweetAlert2 container to be above Flux UI Modals/Flyouts */
        /* Flux UI Flyouts can have very high z-index, so we use a max value */
        .swal2-container {
            z-index: 99999999 !important;
        }
    </style>
</head>

<body class="min-h-screen antialiased relative overflow-x-hidden" style="background-color: #FBF6F6;">

    <!-- Global Background Watermark -->
    <img src="{{ asset('logo.png') }}" class="fixed -bottom-16 -right-16 w-[30rem] h-auto opacity-[0.05] pointer-events-none" style="z-index: 0;" alt="" />

    {{-- Wrapper dengan Alpine.js untuk toggle sidebar --}}
    <div x-data="{ 
            sidebarOpen: {{ isset($hideSidebar) && $hideSidebar ? 'false' : "localStorage.getItem('sidebarOpen') !== 'false'" }},
            mobileMenuOpen: false,
            frontOfficeOpen: {{ request()->routeIs('modul.registrasi-pasien*') || request()->routeIs('modul.pasien*') ? 'true' : 'false' }},
            rawatInapOpen: {{ request()->routeIs('modul.rawat-inap*') ? 'true' : 'false' }},
            casemixOpen: {{ request()->is('modul/casemix*') ? 'true' : 'false' }}
         }" x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))" class="flex min-h-screen relative z-10">

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
            @if(auth()->user()->role === 'admin')
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
                    <a x-show="sidebarOpen" href="{{ route('user.dashboard') }}" wire:navigate
                        class="flex items-center gap-2 flex-1 min-w-0" style="text-decoration: none;"
                        x-transition:enter="transition opacity duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <span class="font-bold truncate text-white uppercase"
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
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="flex items-center rounded-md text-sm font-medium transition-colors"
                        :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                        style="color: white; text-decoration: none; background-color: {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                        title="{{ __('Dashboard') }}">
                        <flux:icon name="home" class="w-4 h-4 flex-shrink-0" />
                        <span x-show="sidebarOpen">{{ __('Dashboard') }}</span>
                    </a>

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
                    
                    <a href="{{ route('admin.sql-tracker') }}" wire:navigate
                        class="flex items-center rounded-md text-sm font-medium transition-colors"
                        :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                        style="color: white; text-decoration: none; background-color: {{ request()->routeIs('admin.sql-tracker') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.sql-tracker') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                        title="{{ __('SQL Tracker') }}">
                        <flux:icon name="clock" class="w-4 h-4 flex-shrink-0" />
                        <span x-show="sidebarOpen">{{ __('SQL Tracker') }}</span>
                    </a>
                </nav>
            </div>
            @else
            {{-- BRAND AREA FOR NON-ADMIN (Standard Olive) --}}
            <div style="background-color: #4C5C2D; color: white;">
                <div class="flex items-center h-[64px] border-b border-white/10 overflow-hidden"
                    :class="sidebarOpen ? 'px-4 gap-3' : 'justify-center px-0'">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="flex-shrink-0 p-1.5 rounded-lg hover:bg-white/10 transition-colors text-white focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a x-show="sidebarOpen" href="{{ route('user.dashboard') }}" wire:navigate
                        class="flex items-center gap-2 flex-1 min-w-0" style="text-decoration: none;">
                        <span class="font-bold truncate text-white uppercase" style="font-size: 1rem; letter-spacing: 0.03em;">LaraLite</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- SECTION: MAIN (Dark Olive) - USER MENU --}}
            <div class="flex-1 flex flex-col" style="background-color: #4C5C2D; color: white; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.5rem;">

                {{-- Main Menu Header --}}
                <div x-show="sidebarOpen" style="padding: 0.75rem 1rem 0.25rem 1rem;">
                    <p
                        style="color: rgba(255,255,255,0.5); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">
                        {{ __('User Menu') }}
                    </p>
                </div>
                <div x-show="!sidebarOpen" class="w-8 h-px bg-white/10 mx-auto my-2"></div>

                {{-- Main Navigation --}}
                <nav class="overflow-y-auto" :class="sidebarOpen ? 'px-2' : 'px-2 flex flex-col items-center'">
                    {{-- Semua Modul (Standalone) --}}
                    <a href="{{ route('modul.index') }}" wire:navigate
                        class="flex items-center rounded-md text-sm font-medium transition-colors mb-1"
                        :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                        style="color: white; text-decoration: none; background-color: {{ request()->routeIs('modul.index') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('modul.index') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                        title="Semua Modul">
                        <flux:icon name="cube" class="w-4 h-4 flex-shrink-0" />
                        <span x-show="sidebarOpen">Semua Modul</span>
                    </a>

                    {{-- Front Office Dropdown --}}
                    <div class="flex flex-col mb-1" :class="sidebarOpen ? 'w-full' : 'w-full items-center'">
                        <div class="flex items-center rounded-md text-sm font-medium transition-colors group"
                            :class="sidebarOpen ? 'w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="background-color: {{ request()->routeIs('modul.registrasi-pasien*') || request()->routeIs('modul.pasien*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};">

                            <a href="#" class="flex items-center grow"
                                @click.prevent="frontOfficeOpen = !frontOfficeOpen; if(!sidebarOpen) sidebarOpen = true;"
                                :class="sidebarOpen ? 'gap-2 px-3 py-2' : 'justify-center w-full h-full'"
                                style="color: white; text-decoration: none;"
                                onmouseover="this.parentElement.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                                onmouseout="this.parentElement.style.backgroundColor='{{ request()->routeIs('modul.registrasi-pasien*') || request()->routeIs('modul.pasien*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                                title="Front Office">
                                <flux:icon name="building-office-2" class="w-4 h-4 flex-shrink-0" />
                                <span x-show="sidebarOpen">Front Office</span>
                            </a>

                            <button x-show="sidebarOpen" @click.prevent="frontOfficeOpen = !frontOfficeOpen"
                                class="p-2 mr-1 rounded-md hover:bg-white/10 transition-all text-white/50 hover:text-white"
                                :class="frontOfficeOpen ? 'rotate-180 text-white' : ''">
                                <flux:icon name="chevron-down" class="w-3 h-3" />
                            </button>
                        </div>

                        {{-- Sub-menus Front Office --}}
                        <div x-show="sidebarOpen && frontOfficeOpen" x-transition:enter="transition ease-out duration-200"
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
                        </div>
                    </div>

                    {{-- Rawat Jalan (Standalone) --}}
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate
                        class="flex items-center rounded-md text-sm font-medium transition-colors mb-1"
                        :class="sidebarOpen ? 'gap-2 px-3 py-2 w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                        style="color: white; text-decoration: none; background-color: {{ request()->routeIs('modul.rawat-jalan*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('modul.rawat-jalan*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                        title="Rawat Jalan">
                        <flux:icon name="calendar-days" class="w-4 h-4 flex-shrink-0" />
                        <span x-show="sidebarOpen">Rawat Jalan</span>
                    </a>

                    {{-- Rawat Inap with Dropdown --}}
                    <div class="flex flex-col mb-1" :class="sidebarOpen ? 'w-full' : 'w-full items-center'">
                        <div class="flex items-center rounded-md text-sm font-medium transition-colors group"
                            :class="sidebarOpen ? 'w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="background-color: {{ request()->routeIs('modul.rawat-inap*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};">

                            <a href="#" class="flex items-center grow"
                                @click.prevent="rawatInapOpen = !rawatInapOpen; if(!sidebarOpen) sidebarOpen = true;"
                                :class="sidebarOpen ? 'gap-2 px-3 py-2' : 'justify-center w-full h-full'"
                                style="color: white; text-decoration: none;"
                                onmouseover="this.parentElement.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                                onmouseout="this.parentElement.style.backgroundColor='{{ request()->routeIs('modul.rawat-inap*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                                title="Rawat Inap">
                                <flux:icon name="home" class="w-4 h-4 flex-shrink-0" />
                                <span x-show="sidebarOpen">Rawat Inap</span>
                            </a>

                            <button x-show="sidebarOpen" @click.prevent="rawatInapOpen = !rawatInapOpen"
                                class="p-2 mr-1 rounded-md hover:bg-white/10 transition-all text-white/50 hover:text-white"
                                :class="rawatInapOpen ? 'rotate-180 text-white' : ''">
                                <flux:icon name="chevron-down" class="w-3 h-3" />
                            </button>
                        </div>

                        {{-- Sub-menus Rawat Inap --}}
                        <div x-show="sidebarOpen && rawatInapOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="ml-6 space-y-1 mt-1 mb-2 border-l border-white/10 pl-2">

                            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.rawat-inap.index') || request()->routeIs('modul.rawat-inap.show') || request()->routeIs('modul.rawat-inap.sub-rawat-inap*') || request()->routeIs('modul.rawat-inap.perawatan-tindakan') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.rawat-inap.index') || request()->routeIs('modul.rawat-inap.show') || request()->routeIs('modul.rawat-inap.sub-rawat-inap*') || request()->routeIs('modul.rawat-inap.perawatan-tindakan') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="users" class="w-3.5 h-3.5" />
                                <span>Pasien Ranap</span>
                            </a>

                            <a href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.rawat-inap.kelahiran-bayi') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.rawat-inap.kelahiran-bayi') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="face-smile" class="w-3.5 h-3.5" />
                                <span>Kelahiran Bayi</span>
                            </a>
                        </div>
                    </div>

                    {{-- Casemix with Dropdown --}}
                    <div class="flex flex-col mb-1" :class="sidebarOpen ? 'w-full' : 'w-full items-center'">
                        <div class="flex items-center rounded-md text-sm font-medium transition-colors group"
                            :class="sidebarOpen ? 'w-full mb-0.5' : 'justify-center w-10 h-10 mb-2'"
                            style="background-color: {{ request()->is('modul/casemix*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }};">

                            <a href="#" class="flex items-center grow"
                                @click.prevent="casemixOpen = !casemixOpen; if(!sidebarOpen) sidebarOpen = true;"
                                :class="sidebarOpen ? 'gap-2 px-3 py-2' : 'justify-center w-full h-full'"
                                style="color: white; text-decoration: none;"
                                onmouseover="this.parentElement.style.backgroundColor='rgba(255, 255, 255, 0.1)'"
                                onmouseout="this.parentElement.style.backgroundColor='{{ request()->is('modul/casemix*') ? 'rgba(255, 255, 255, 0.2)' : 'transparent' }}'"
                                title="{{ __('Casemix') }}">
                                <flux:icon name="clipboard-document-check" class="w-4 h-4 flex-shrink-0" />
                                <span x-show="sidebarOpen">{{ __('Casemix') }}</span>
                            </a>

                            <button x-show="sidebarOpen" @click.prevent="casemixOpen = !casemixOpen"
                                class="p-2 mr-1 rounded-md hover:bg-white/10 transition-all text-white/50 hover:text-white"
                                :class="casemixOpen ? 'rotate-180 text-white' : ''">
                                <flux:icon name="chevron-down" class="w-3 h-3" />
                            </button>
                        </div>

                        {{-- Sub-menus Casemix --}}
                        <div x-show="sidebarOpen && casemixOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="ml-6 space-y-1 mt-1 mb-2 border-l border-white/10 pl-2">

                            <a href="{{ route('modul.casemix-rawat-jalan.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.casemix-rawat-jalan*') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.casemix-rawat-jalan*') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                                <span>Casemix RAJAL</span>
                            </a>

                            <a href="{{ route('modul.casemix-rawat-inap.index') }}" wire:navigate
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                style="color: {{ request()->routeIs('modul.casemix-rawat-inap*') ? 'white' : 'rgba(255,255,255,0.7)' }}; background-color: {{ request()->routeIs('modul.casemix-rawat-inap*') ? 'rgba(255,255,255,0.1)' : 'transparent' }}; text-decoration: none;">
                                <flux:icon name="home" class="w-3.5 h-3.5" />
                                <span>Casemix RANAP</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            {{-- SECONDARY ZONE: BRIDGING & FOOTER (Darker Olive Zone) --}}
            <div class="flex-1 flex flex-col pt-4"
                style="background-color: #3E4A25; border-top: 1px solid rgba(0,0,0,0.1);">
                @if(auth()->user()->role === 'admin')
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
                @endif

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
            <header class="lg:hidden flex items-center h-14 px-4 sticky top-0 z-40" style="background-color: #4C5C2D;">
                {{-- Hamburger Button --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-lg hover:bg-white/10 transition-colors text-white focus:outline-none flex-shrink-0"
                    aria-label="Toggle menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <span class="font-bold ml-3 text-white" style="font-size: 0.95rem;">LaraLite</span>

                <div class="ml-auto flex items-center gap-2">
                    <flux:dropdown position="bottom" align="end">
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
                                                {{ auth()->user()->fullname }}
                                            </flux:heading>
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
                </div>
            </header>

            {{-- Mobile Sidebar Overlay --}}
            <div x-show="mobileMenuOpen" class="lg:hidden fixed inset-0 z-50 flex"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">

                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/50" @click="mobileMenuOpen = false"></div>

                {{-- Drawer --}}
                <div class="relative flex flex-col w-72 h-full overflow-y-auto shadow-xl z-10"
                    style="background-color: #4C5C2D;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full">

                    {{-- Drawer Header --}}
                    <div class="flex items-center justify-between h-14 px-4 flex-shrink-0 border-b border-white/10">
                        <span class="font-bold text-white uppercase" style="font-size:1rem; letter-spacing:0.03em;">LaraLite</span>
                        <button @click="mobileMenuOpen = false"
                            class="p-1.5 rounded-lg hover:bg-white/10 text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Drawer Navigation --}}
                    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">

                        @if(auth()->user()->role === 'admin')
                        <p class="px-3 pb-1 text-[0.6rem] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,0.5);">Admin Menu</p>
                        <a href="{{ route('admin.dashboard') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            style="color:white; text-decoration:none; background-color:{{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'rgba(255,255,255,0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'rgba(255,255,255,0.2)' : 'transparent' }}'"
                            >
                            <flux:icon name="layout-grid" class="w-4 h-4 flex-shrink-0" />
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('users.index') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            style="color:white; text-decoration:none; background-color:{{ request()->routeIs('users*') ? 'rgba(255,255,255,0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('users*') ? 'rgba(255,255,255,0.2)' : 'transparent' }}'">
                            <flux:icon name="users" class="w-4 h-4 flex-shrink-0" />
                            <span>Manajemen User</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            style="color:white; text-decoration:none; background-color:{{ request()->routeIs('admin.settings') ? 'rgba(255,255,255,0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.settings') ? 'rgba(255,255,255,0.2)' : 'transparent' }}'">
                            <flux:icon name="cog-6-tooth" class="w-4 h-4 flex-shrink-0" />
                            <span>Pengaturan</span>
                        </a>
                        <div class="my-2 border-t border-white/10"></div>
                        @endif

                        <p class="px-3 pb-1 text-[0.6rem] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,0.5);">User Menu</p>

                        <a href="{{ route('modul.index') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            style="color:white; text-decoration:none; background-color:{{ request()->routeIs('modul.index') ? 'rgba(255,255,255,0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('modul.index') ? 'rgba(255,255,255,0.2)' : 'transparent' }}'">
                            <flux:icon name="cube" class="w-4 h-4 flex-shrink-0" />
                            <span>Semua Modul</span>
                        </a>

                        {{-- Front Office --}}
                        <div x-data="{ open: {{ request()->routeIs('modul.registrasi-pasien*') || request()->routeIs('modul.pasien*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors w-full text-left"
                                style="color:white; background-color:{{ request()->routeIs('modul.registrasi-pasien*') || request()->routeIs('modul.pasien*') ? 'rgba(255,255,255,0.2)' : 'transparent' }};">
                                <flux:icon name="building-office-2" class="w-4 h-4 flex-shrink-0" />
                                <span class="flex-1">Front Office</span>
                                <flux:icon name="chevron-down" class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" class="ml-6 mt-1 space-y-1 border-l border-white/10 pl-2">
                                <a href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate @click="mobileMenuOpen=false"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                    style="color:{{ request()->routeIs('modul.registrasi-pasien*') ? 'white' : 'rgba(255,255,255,0.7)' }}; text-decoration:none;">
                                    <flux:icon name="users" class="w-3.5 h-3.5" /><span>Registrasi</span>
                                </a>
                                <a href="{{ route('modul.pasien.index') }}" wire:navigate @click="mobileMenuOpen=false"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                    style="color:{{ request()->routeIs('modul.pasien*') ? 'white' : 'rgba(255,255,255,0.7)' }}; text-decoration:none;">
                                    <flux:icon name="identification" class="w-3.5 h-3.5" /><span>Pasien</span>
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            style="color:white; text-decoration:none; background-color:{{ request()->routeIs('modul.rawat-jalan*') ? 'rgba(255,255,255,0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('modul.rawat-jalan*') ? 'rgba(255,255,255,0.2)' : 'transparent' }}'">
                            <flux:icon name="calendar-days" class="w-4 h-4 flex-shrink-0" />
                            <span>Rawat Jalan</span>
                        </a>

                        {{-- Rawat Inap --}}
                        <div x-data="{ open: {{ request()->routeIs('modul.rawat-inap*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors w-full text-left"
                                style="color:white; background-color:{{ request()->routeIs('modul.rawat-inap*') ? 'rgba(255,255,255,0.2)' : 'transparent' }};">
                                <flux:icon name="home" class="w-4 h-4 flex-shrink-0" />
                                <span class="flex-1">Rawat Inap</span>
                                <flux:icon name="chevron-down" class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" class="ml-6 mt-1 space-y-1 border-l border-white/10 pl-2">
                                <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate @click="mobileMenuOpen=false"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                    style="color:{{ request()->routeIs('modul.rawat-inap.index') ? 'white' : 'rgba(255,255,255,0.7)' }}; text-decoration:none;">
                                    <flux:icon name="users" class="w-3.5 h-3.5" /><span>Pasien Ranap</span>
                                </a>
                                <a href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate @click="mobileMenuOpen=false"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                    style="color:{{ request()->routeIs('modul.rawat-inap.kelahiran-bayi') ? 'white' : 'rgba(255,255,255,0.7)' }}; text-decoration:none;">
                                    <flux:icon name="face-smile" class="w-3.5 h-3.5" /><span>Kelahiran Bayi</span>
                                </a>
                            </div>
                        </div>

                        {{-- Casemix --}}
                        <div x-data="{ open: {{ request()->is('modul/casemix*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors w-full text-left"
                                style="color:white; background-color:{{ request()->is('modul/casemix*') ? 'rgba(255,255,255,0.2)' : 'transparent' }};">
                                <flux:icon name="clipboard-document-check" class="w-4 h-4 flex-shrink-0" />
                                <span class="flex-1">Casemix</span>
                                <flux:icon name="chevron-down" class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" class="ml-6 mt-1 space-y-1 border-l border-white/10 pl-2">
                                <a href="{{ route('modul.casemix-rawat-jalan.index') }}" wire:navigate @click="mobileMenuOpen=false"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                    style="color:{{ request()->routeIs('modul.casemix-rawat-jalan*') ? 'white' : 'rgba(255,255,255,0.7)' }}; text-decoration:none;">
                                    <flux:icon name="calendar-days" class="w-3.5 h-3.5" /><span>Casemix RAJAL</span>
                                </a>
                                <a href="{{ route('modul.casemix-rawat-inap.index') }}" wire:navigate @click="mobileMenuOpen=false"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[0.75rem] font-medium transition-colors hover:bg-white/10"
                                    style="color:{{ request()->routeIs('modul.casemix-rawat-inap*') ? 'white' : 'rgba(255,255,255,0.7)' }}; text-decoration:none;">
                                    <flux:icon name="home" class="w-3.5 h-3.5" /><span>Casemix RANAP</span>
                                </a>
                            </div>
                        </div>

                        @if(auth()->user()->role === 'admin')
                        <div class="my-2 border-t border-white/10"></div>
                        <p class="px-3 pb-1 text-[0.6rem] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,0.5);">Bridging</p>
                        <a href="{{ route('bridging.erm-bpjs.index') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            style="color:white; text-decoration:none; background-color:{{ request()->routeIs('bridging.erm-bpjs*') ? 'rgba(255,255,255,0.2)' : 'transparent' }};"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('bridging.erm-bpjs*') ? 'rgba(255,255,255,0.2)' : 'transparent' }}'">
                            <flux:icon name="document-text" class="w-4 h-4 flex-shrink-0" />
                            <span>Rekam Medis (E-Claim)</span>
                        </a>
                        @endif

                    </nav>

                    {{-- Drawer Footer --}}
                    <div class="flex-shrink-0 border-t border-white/10 p-3" style="background-color:#2F381C;">
                        <a href="{{ route('profile.edit') }}" wire:navigate @click="mobileMenuOpen=false"
                            class="flex items-center gap-2 mb-2" style="text-decoration:none;">
                            <div class="flex items-center justify-center rounded-full flex-shrink-0 font-bold"
                                style="background-color:rgba(255,255,255,0.2); width:2.2rem; height:2.2rem; color:white; font-size:0.75rem;">
                                {{ auth()->user()->initials() }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="truncate font-semibold text-white" style="font-size:0.8rem; line-height:1.2;">{{ auth()->user()->fullname }}</p>
                                <p class="truncate text-white/50" style="font-size:0.65rem; text-transform:capitalize;">{{ auth()->user()->role }}</p>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center gap-1.5 w-full rounded-lg text-[0.7rem] font-semibold py-2 px-3 hover:bg-white/20 bg-white/10"
                                style="color:white; border:1px solid rgba(255,255,255,0.05); cursor:pointer;">
                                <flux:icon name="arrow-right-start-on-rectangle" class="size-4" />
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            {{-- Page content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        {{-- Floating Support Button (Ticketing IT) --}}
        <div class="fixed bottom-6 right-6 z-[9999] lg:bottom-8 lg:right-8">
            <a href="http://192.168.100.177/mastolongmas/public/login" target="_blank"
                class="flex items-center justify-start h-12 w-12 hover:w-36 rounded-full shadow-lg transition-all duration-300 ease-in-out group border border-white/10 backdrop-blur-md overflow-hidden"
                style="background-color: rgba(24, 24, 27, 0.8); color: white; text-decoration: none;"
                title="Ticketing IT">
                
                <div class="flex-shrink-0 flex items-center justify-center w-12 h-12">
                    <span class="text-xl font-bold text-white group-hover:text-white/80 transition-colors leading-none mt-1">?</span>
                </div>
                
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap text-[0.7rem] font-bold tracking-tight text-white pr-5">
                    BANTUAN IT?
                </span>
            </a>
        </div>

    </div>{{-- end Alpine wrapper --}}

    @fluxScripts

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Global Override for SweetAlert2 to handle z-index/top-layer issues with Flux UI
            const originalSwalFire = Swal.fire;
            Swal.fire = function(...args) {
                let options = args[0];
                if (typeof options === 'object' && !options.target) {
                    const activeModal = document.querySelector('dialog[open]');
                    if (activeModal) {
                        options.target = activeModal;
                    }
                }
                return originalSwalFire.apply(Swal, args);
            };

            Livewire.on('swal', (event) => {
                const data = event[0];
                
                // Hapus delay yang membuat popup terkesan lambat
                setTimeout(() => {
                    Swal.fire({
                        title: data.title ?? '',
                        text: data.text ?? '',
                        html: data.html,
                        icon: data.icon ?? 'success',
                        confirmButtonColor: '#4C5C2D',
                        confirmButtonText: data.confirmButtonText ?? 'OK',
                        timer: data.timer ?? undefined, // Tambahkan timer agar bisa auto-close
                        timerProgressBar: data.timer ? true : false
                    });
                }, 10);
            });
        });
    </script>
    @stack('scripts')
</body>

</html>