@php
    $loginBg = \App\Models\AppSetting::where('setting_key', 'LOGIN_BACKGROUND_IMAGE')->first();
    $hasImage = $loginBg && !empty($loginBg->setting_value);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased overflow-hidden" style="background-color: #F0F4E8;">
    <div class="flex min-h-screen w-full">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- LEFT PANEL — Showcase Image / Default Gradient --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="login-split-left hidden lg:flex lg:w-[55%] xl:w-[58%] relative overflow-hidden">

            @if($hasImage)
                {{-- Admin-uploaded image --}}
                <img src="data:image/webp;base64,{{ $loginBg->setting_value }}" alt="Login Background"
                    class="login-bg-image absolute inset-0 w-full h-full object-cover" />
                {{-- Dark overlay for text readability --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-black/10 z-[1]"></div>
            @else
                {{-- Default gradient when no image uploaded --}}
                <div class="absolute inset-0 login-default-gradient"></div>
                {{-- Decorative floating shapes --}}
                <div class="absolute inset-0 overflow-hidden z-[1]">
                    <div class="login-float-shape login-shape-1"></div>
                    <div class="login-float-shape login-shape-2"></div>
                    <div class="login-float-shape login-shape-3"></div>
                </div>
            @endif

            {{-- Branding overlay (always visible) --}}
            <div class="relative z-[2] flex flex-col justify-between h-full w-full p-10 xl:p-14">
                {{-- Top: Logo --}}
                <div class="login-fade-in">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-lg border border-white/10">
                            <img src="{{ asset('logo.png') }}" class="w-7 h-7 object-contain" alt="Logo" />
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-lg tracking-wide leading-none">LARALITE</h2>
                            <p class="text-white/50 text-[0.65rem] font-medium tracking-wider uppercase mt-0.5">Hospital
                                Management</p>
                        </div>
                    </div>
                </div>

                {{-- Bottom: Tagline --}}
                <div class="login-fade-in-delayed">
                    <div class="max-w-md">
                        <h1 class="text-white text-3xl xl:text-4xl font-extrabold leading-tight tracking-tight mb-3">
                            Sistem Informasi<br />Rumah Sakit IBI Surabaya
                        </h1>
                        <p class="text-white/60 text-sm leading-relaxed font-medium">
                            Platform terintegrasi dengan KHANZA, dengan teknologi LARAVEL terbaru
                        </p>
                    </div>

                    {{-- Feature badges --}}
                    <div class="flex flex-wrap gap-2 mt-6">
                        <span class="login-feature-badge">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                            Keamanan Data
                        </span>
                        <span class="login-feature-badge">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Performa Tinggi
                        </span>
                        <span class="login-feature-badge">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Multi-Platform
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- RIGHT PANEL — Login Form --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="login-split-right flex-1 flex items-center justify-center relative px-6 py-10 sm:px-10">
            {{-- Subtle background pattern --}}
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                style="background-image: radial-gradient(circle, #4C5C2D 1px, transparent 1px); background-size: 24px 24px;">
            </div>

            {{-- Watermark logo (bottom-right, subtle) --}}
            <img src="{{ asset('logo.png') }}"
                class="fixed -bottom-12 -right-12 w-[22rem] h-auto opacity-[0.04] pointer-events-none z-0 lg:block hidden"
                alt="" />

            {{-- Form container --}}
            <div class="relative z-10 w-full max-w-[420px] login-form-entrance">
                {{ $slot }}
            </div>
        </div>

    </div>
    @fluxScripts
</body>

</html>