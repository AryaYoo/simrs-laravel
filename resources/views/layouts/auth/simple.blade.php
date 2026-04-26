<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased relative overflow-hidden dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900"
    style="background-color: #E8F6EF;">
    
    <!-- Background Watermark -->
    <img src="{{ asset('logo.png') }}" class="fixed -bottom-16 -right-16 w-[30rem] h-auto opacity-10 pointer-events-none" alt="" />

    <div class="relative z-10 flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-2">
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>