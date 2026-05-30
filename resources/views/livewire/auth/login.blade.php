<x-layouts::auth :title="__('Log in')">
    <div class="w-full">
        {{-- Header / Branding --}}
        <div class="flex flex-col items-center mb-8 lg:items-start">
            {{-- Mobile-only logo (hidden on desktop since left panel has it) --}}
            <div class="lg:hidden mb-5">
                <img src="{{ asset('logo.png') }}" class="h-16 w-auto object-contain drop-shadow-md" alt="Logo" />
            </div>

            <div class="text-center lg:text-left">
                <h1 class="font-extrabold text-2xl lg:text-3xl tracking-tight leading-tight" style="color: #4C5C2D;">
                    Selamat Datang 👋
                </h1>
                <p class="mt-2 text-sm font-medium" style="color: rgba(76, 76, 109, 0.55);">
                    Masuk ke Sistem Informasi Rumah Sakit
                </p>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center lg:text-left mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Username -->
            <div class="space-y-1.5">
                <flux:input name="username" :label="__('Username')" :value="old('username')" required autofocus
                    placeholder="Masukkan username Anda"
                    class="!bg-white border-[#6A7E3F]/15 focus:border-[#4C5C2D] !rounded-xl !py-3"
                    icon="user" />
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <flux:input name="password" :label="__('Password')" type="password" required
                    autocomplete="current-password" placeholder="Masukkan password" viewable
                    class="!bg-white border-[#6A7E3F]/15 focus:border-[#4C5C2D] !rounded-xl !py-3"
                    icon="lock-closed" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')"
                    class="text-sm" style="color: rgba(76, 76, 109, 0.65);" />
            </div>

            <div class="mt-1">
                <button type="submit"
                    class="login-submit-btn w-full text-white font-bold py-3.5 rounded-xl transition-all duration-200 active:scale-[0.98] relative overflow-hidden"
                    data-test="login-button"
                    style="background: linear-gradient(135deg, #4C5C2D 0%, #6A7E3F 100%); box-shadow: 0 8px 24px -4px rgba(76, 92, 45, 0.35);">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        {{ __('Masuk') }}
                    </span>
                </button>
            </div>
        </form>

        {{-- Footer --}}
        <div class="mt-8 pt-6 border-t border-neutral-100">
            <div class="flex items-center justify-center lg:justify-start gap-1.5 text-xs" style="color: rgba(76, 76, 109, 0.4);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Butuh bantuan?</span>
                <a href="http://192.168.100.177/mastolongmas/public" target="_blank"
                   class="font-bold hover:underline transition-colors" style="color: #4C5C2D;">
                    Hubungi Tim IT
                </a>
            </div>
        </div>
    </div>
</x-layouts::auth>