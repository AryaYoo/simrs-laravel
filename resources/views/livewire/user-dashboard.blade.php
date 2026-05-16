<div class="min-h-[80vh] flex items-center justify-center p-6 lg:p-10 transition-all duration-700 animate-in fade-in zoom-in-95">
    <div class="max-w-3xl w-full text-center">
        {{-- Floating Icon Decor --}}
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <div class="absolute inset-0 bg-[#4C5C2D] opacity-20 blur-2xl rounded-full scale-150 animate-pulse"></div>
                <div class="relative bg-gradient-to-br from-[#4C5C2D] to-[#6A7E3F] p-5 rounded-3xl shadow-2xl ring-4 ring-white/20">
                    <flux:icon name="sparkles" class="w-12 h-12 text-white animate-bounce" />
                </div>
            </div>
        </div>

        {{-- Welcome Text --}}
        <div class="space-y-4">
            <h1 class="text-4xl md:text-6xl font-black text-neutral-800 dark:text-neutral-100 tracking-tight leading-tight">
                Selamat Datang, <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#4C5C2D] via-[#6A7E3F] to-[#4C5C2D] animate-gradient-x">
                    {{ auth()->user()->fullname }}
                </span>
            </h1>
            
            <div class="flex justify-center">
                <div class="h-1 w-24 bg-gradient-to-r from-transparent via-[#4C5C2D] to-transparent rounded-full opacity-50"></div>
            </div>

            <p class="text-lg md:text-xl text-neutral-500 dark:text-neutral-400 font-medium max-w-lg mx-auto">
                Senang melihat Anda kembali. Silakan pilih modul di samping untuk mulai bekerja hari ini.
            </p>
        </div>

        {{-- Sub-info/Quote --}}
        <div class="mt-12 pt-8 border-t border-neutral-200 dark:border-neutral-800">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#4C5C2D]/60 dark:text-[#8CC7C4]/60">
                LaraLite Medical Information System &bull; RSIA IBI Surabaya
            </p>
        </div>
    </div>
</div>

<style>
    @keyframes gradient-x {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 5s ease infinite;
    }
</style>
