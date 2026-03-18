<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Role based dashboards
    Route::view('admin', 'dashboard')->name('admin.dashboard');
    Route::view('user', 'dashboard')->name('user.dashboard');

    // User management
    Route::get('users', \App\Livewire\User\Index::class)->name('users.index');
});

// Language switcher (works for guests too)
Route::get('lang/{locale}', function (string $locale) {
    $supported = ['en', 'id'];
    if (!in_array($locale, $supported)) {
        abort(400);
    }
    return back()->withCookie(cookie()->forever('locale', $locale));
})->name('lang.switch');

Route::middleware(['auth'])->group(function () {
    Route::view('modul', 'modul.index')->name('modul.index');
    Route::get('modul/registrasi-pasien', \App\Livewire\Modul\RegistrasiPasien\Index::class)->name('modul.registrasi-pasien.index');
    Route::get('modul/registrasi-pasien/create', \App\Livewire\Modul\RegistrasiPasien\Create::class)->name('modul.registrasi-pasien.create');
    Route::get('modul/registrasi-pasien/new', \App\Livewire\Modul\RegistrasiPasien\NewPatient::class)->name('modul.registrasi-pasien.new');
    Route::get('modul/registrasi-pasien/{no_rawat}', \App\Livewire\Modul\RegistrasiPasien\Show::class)->name('modul.registrasi-pasien.show')->where('no_rawat', '.*');

    Route::get('modul/pasien', \App\Livewire\Modul\Pasien\Index::class)->name('modul.pasien.index');
    Route::get('modul/pasien/{no_rkm_medis}', \App\Livewire\Modul\Pasien\Show::class)->name('modul.pasien.show')->where('no_rkm_medis', '.*');
});

require __DIR__ . '/settings.php';
