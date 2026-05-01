<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'))->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Role based dashboards
    Route::get('admin', \App\Livewire\Dashboard::class)->name('admin.dashboard');
    Route::get('user', \App\Livewire\Dashboard::class)->name('user.dashboard');

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

    Route::get('modul/rawat-inap', \App\Livewire\Modul\RawatInap\Index::class)->name('modul.rawat-inap.index');
    Route::get('modul/rawat-inap/{no_rawat}/perawatan-tindakan', \App\Livewire\Modul\RawatInap\PerawatanTindakan\Index::class)->name('modul.rawat-inap.perawatan-tindakan')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/riwayat-pasien', \App\Livewire\Modul\RawatInap\SubRawatInap\RiwayatPasien\Index::class)->name('modul.rawat-inap.sub-rawat-inap.riwayat-pasien')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/resume', \App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien\Index::class)->name('modul.rawat-inap.sub-rawat-inap.resume')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/resume/create', \App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien\Create::class)->name('modul.rawat-inap.sub-rawat-inap.resume-create')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/resume/edit', \App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien\Edit::class)->name('modul.rawat-inap.sub-rawat-inap.resume-edit')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/resume/detail', \App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien\Detail::class)->name('modul.rawat-inap.sub-rawat-inap.resume-detail')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/resep-dokter', \App\Livewire\Modul\RawatInap\SubRawatInap\ResepDokter\Index::class)->name('modul.rawat-inap.sub-rawat-inap.resep-dokter')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/permintaan-lab', \App\Livewire\Modul\RawatInap\SubRawatInap\PermintaanLaboratorium\Index::class)->name('modul.rawat-inap.sub-rawat-inap.permintaan-laboratorium')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/pindah', \App\Livewire\Modul\RawatInap\SubRawatInap\PindahKamar\Index::class)->name('modul.rawat-inap.sub-rawat-inap.pindah')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/permintaan-radiologi', \App\Livewire\Modul\RawatInap\SubRawatInap\PermintaanRadiologi\Index::class)->name('modul.rawat-inap.sub-rawat-inap.permintaan-radiologi')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}/pulang', \App\Livewire\Modul\RawatInap\SubRawatInap\CheckOut\Index::class)->name('modul.rawat-inap.sub-rawat-inap.pulang')->where('no_rawat', '.*');
    Route::get('modul/rawat-inap/{no_rawat}', \App\Livewire\Modul\RawatInap\Show::class)->name('modul.rawat-inap.show')->where('no_rawat', '.*');

    Route::get('modul/rawat-jalan', \App\Livewire\Modul\RawatJalan\Index::class)->name('modul.rawat-jalan.index');
    Route::get('modul/rawat-jalan/{no_rawat}/perawatan-tindakan', \App\Livewire\Modul\RawatJalan\PerawatanTindakan\Index::class)->name('modul.rawat-jalan.perawatan-tindakan')->where('no_rawat', '.*');
    Route::get('modul/rawat-jalan/{no_rawat}', \App\Livewire\Modul\RawatJalan\Show::class)->name('modul.rawat-jalan.show')->where('no_rawat', '.*');

    Route::get('modul/casemix-rawat-jalan', \App\Livewire\Modul\CasemixRawatJalan\Index::class)->name('modul.casemix-rawat-jalan.index');
    Route::get('modul/casemix-rawat-jalan/{no_rawat}/resume', \App\Livewire\Modul\CasemixRawatJalan\Resume\Index::class)->name('modul.casemix-rawat-jalan.resume')->where('no_rawat', '.*');
    Route::get('modul/casemix-rawat-jalan/{no_rawat}/resume/form', \App\Livewire\Modul\CasemixRawatJalan\Resume\Form::class)->name('modul.casemix-rawat-jalan.resume.form')->where('no_rawat', '.*');
    
    Route::get('modul/casemix-rawat-inap', \App\Livewire\Modul\CasemixRawatInap\Index::class)->name('modul.casemix-rawat-inap.index');
    Route::get('modul/casemix-rawat-inap/{no_rawat}/resume', \App\Livewire\Modul\CasemixRawatInap\Resume\Index::class)->name('modul.casemix-rawat-inap.resume')->where('no_rawat', '.*');
    Route::get('modul/casemix-rawat-inap/{no_rawat}/resume/form', \App\Livewire\Modul\CasemixRawatInap\Resume\Form::class)->name('modul.casemix-rawat-inap.resume.form')->where('no_rawat', '.*');

    Route::get('modul/pasien', \App\Livewire\Modul\Pasien\Index::class)->name('modul.pasien.index');
    Route::get('modul/pasien/{no_rkm_medis}/edit', \App\Livewire\Modul\Pasien\Edit::class)->name('modul.pasien.edit')->where('no_rkm_medis', '.*');
    Route::get('modul/pasien/{no_rkm_medis}', \App\Livewire\Modul\Pasien\Show::class)->name('modul.pasien.show')->where('no_rkm_medis', '.*');

    Route::view('master-data', 'master-data.index')->name('master-data.index');
    Route::get('admin/settings', \App\Livewire\Admin\Settings::class)->name('admin.settings');
    Route::get('master-data/penjamin', \App\Livewire\MasterData\Penjamin\Index::class)->name('master-data.penjamin.index');
    Route::get('master-data/kabupaten', \App\Livewire\MasterData\Kabupaten\Index::class)->name('master-data.kabupaten.index');
    Route::get('master-data/kecamatan', \App\Livewire\MasterData\Kecamatan\Index::class)->name('master-data.kecamatan.index');
    Route::get('master-data/kelurahan', \App\Livewire\MasterData\Kelurahan\Index::class)->name('master-data.kelurahan.index');
    Route::get('master-data/perusahaan-pasien', \App\Livewire\MasterData\PerusahaanPasien\Index::class)->name('master-data.perusahaan-pasien.index');
    Route::get('master-data/suku-bangsa', \App\Livewire\MasterData\SukuBangsa\Index::class)->name('master-data.suku-bangsa.index');
    Route::get('master-data/bahasa-pasien', \App\Livewire\MasterData\BahasaPasien\Index::class)->name('master-data.bahasa-pasien.index');
    Route::get('master-data/cacat-fisik', \App\Livewire\MasterData\CacatFisik\Index::class)->name('master-data.cacat-fisik.index');
    Route::get('master-data/provinsi', \App\Livewire\MasterData\Provinsi\Index::class)->name('master-data.provinsi.index');
    Route::get('master-data/dokter', \App\Livewire\MasterData\Dokter\Index::class)->name('master-data.dokter.index');
    Route::get('master-data/poliklinik', \App\Livewire\MasterData\Poliklinik\Index::class)->name('master-data.poliklinik.index');
    Route::get('master-data/perujuk', \App\Livewire\MasterData\Perujuk\Index::class)->name('master-data.perujuk.index');

    // Bridging
    Route::get('bridging/erm-bpjs', \App\Livewire\Bridging\ErmBpjs\Index::class)->name('bridging.erm-bpjs.index');
});

require __DIR__ . '/settings.php';
