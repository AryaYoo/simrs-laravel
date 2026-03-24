<x-layouts::auth :title="__('Log in')">
    <div class="auth-card p-7">
        <div class="flex flex-col gap-2 text-center">
            <div class="flex flex-col items-center mb-2">
                <h1 class="font-extrabold" style="color: #4C5C2D; font-size: 1.75rem; line-height: 1.2; letter-spacing: 0.05em;">LARALITE</h1>
                <p style="color: rgba(76, 76, 109, 0.5); font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 2px;">
                    {{ __('System Information Management') }}
                </p>
            </div>
            <flux:text style="color: rgba(76, 76, 109, 0.7); font-size: 0.875rem;">
                {{ __('Please enter your credentials to access the system') }}
            </flux:text>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Username -->
            <flux:input name="username" :label="__('Username')" :value="old('username')" required autofocus
                placeholder="Enter your username"
                class="bg-zinc-50 border-[#6A7E3F]/20 focus:border-[#4C5C2D] dark:bg-zinc-800 dark:border-zinc-700"
                style="background-color: rgba(106, 126, 63, 0.05);" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')" type="password" required
                    autocomplete="current-password" :placeholder="__('Password')" viewable
                    class="bg-zinc-50 border-[#6A7E3F]/20 focus:border-[#4C5C2D] dark:bg-zinc-800 dark:border-zinc-700"
                    style="background-color: rgba(106, 126, 63, 0.05);" />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0 font-medium" :href="route('password.request')"
                        wire:navigate style="color: #4C5C2D;">
                        {{ __('Forgot?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Keep me logged in')" :checked="old('remember')"
                class="text-sm font-medium" style="color: rgba(76, 76, 109, 0.7);" />

            <div class="flex items-center justify-end">
                <flux:button type="submit"
                    class="w-full text-white border-none transition-all py-3 rounded-xl shadow-lg active:scale-[0.98] font-bold"
                    data-test="login-button"
                    style="background-color: #4C5C2D; box-shadow: 0 10px 15px -3px rgba(76, 92, 45, 0.2);">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center dark:text-zinc-400" style="color: rgba(76, 76, 109, 0.5);">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" class="font-bold hover:underline" wire:navigate
                    style="color: #4C5C2D;">
                    {{ __('Request Access') }}
                </flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>