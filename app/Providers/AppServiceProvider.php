<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Force HTTPS if APP_URL starts with https
        if (str_starts_with(config('app.url', ''), 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Global SQL Tracker (SOP compatibility with trackersql)
        DB::listen(function ($query) {
            $sql = strtolower($query->sql);
            
            // Only log insert, update, delete operations
            if (str_starts_with($sql, 'insert') || str_starts_with($sql, 'update') || str_starts_with($sql, 'delete')) {
                // Prevent infinite recursion by ignoring tracker tables
                if (str_contains($sql, 'trackersql') || str_contains($sql, 'tracker')) {
                    return;
                }

                $rawSql = $query->sql;
                foreach ($query->bindings as $binding) {
                    if ($binding instanceof \DateTimeInterface) {
                        $val = $binding->format('Y-m-d H:i:s');
                    } elseif (is_bool($binding)) {
                        $val = $binding ? '1' : '0';
                    } elseif (is_null($binding)) {
                        $val = null;
                    } else {
                        $val = $binding;
                    }

                    if (is_null($val)) {
                        $value = 'NULL';
                    } elseif (is_numeric($val) && (int)$val == $val && !str_starts_with((string)$val, '0')) {
                        $value = $val;
                    } else {
                        $value = "'" . addslashes((string)$val) . "'";
                    }
                    
                    // Replace the first '?' placeholder
                    $rawSql = preg_replace('/\?/', $value, $rawSql, 1);
                }

                $ip = request()->ip() ?? '127.0.0.1';
                $username = auth()->user()->username ?? 'System';

                try {
                    DB::table('trackersql')->insert([
                        'tanggal' => now(),
                        'sqlan' => $ip . ' ' . $rawSql,
                        'usere' => $username
                    ]);
                } catch (\Exception $e) {
                    // Fail silently to prevent disrupting user experience
                    logger()->error('Failed to log query to trackersql: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
