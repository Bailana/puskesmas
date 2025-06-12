<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DokterDashboardController;
use App\Http\Controllers\PerawatDashboardController;
use App\Http\Controllers\ResepsionisDashboardController;
use App\Http\Controllers\ApotekerDashboardController;
use App\Http\Controllers\KasirDashboardController;
use App\Http\Controllers\GigiDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/pasien', [ApotekerDashboardController::class, 'pasien'])->name('apoteker.pasien');
    Route::get('/apoteker/obat', [ApotekerDashboardController::class, 'obat'])->name('apoteker.obat');
    Route::post('/apoteker/obat', [ApotekerDashboardController::class, 'storeObat'])->name('apoteker.obat.store');
    Route::get('/apoteker/antrian', [ApotekerDashboardController::class, 'antrian'])->name('apoteker.antrian');
    Route::get('/apoteker/obat/{id}', [ApotekerDashboardController::class, 'getObatDetail'])->name('apoteker.obat.detail');
    Route::put('/apoteker/obat/{id}', [ApotekerDashboardController::class, 'updateObat'])->name('apoteker.obat.update');
    Route::delete('/apoteker/obat/{id}', [ApotekerDashboardController::class, 'destroyObat'])->name('apoteker.obat.destroy');

    // Add route for apoteker getHasilPeriksa
    Route::get('/apoteker/hasil-periksa/{pasienId}', [ApotekerDashboardController::class, 'getHasilPeriksa'])->name('apoteker.hasil.periksa');
});

Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter/riwayat-berobat/{no_rekam_medis}', [DokterDashboardController::class, 'getRiwayatBerobat'])->name('dokter.riwayat.berobat');
    Route::get('/dokter/hasil-periksa-detail/{no_rekam_medis}/{tanggal}', [DokterDashboardController::class, 'getHasilPeriksaDetail'])->name('dokter.hasil.periksa.detail');
});

Route::middleware(['auth', 'role:resepsionis'])->group(function () {
    Route::get('/resepsionis/riwayat-berobat/{no_rekam_medis}', [ResepsionisDashboardController::class, 'getRiwayatBerobat'])->name('resepsionis.riwayat.berobat');
    Route::get('/resepsionis/hasil-periksa-detail/{no_rekam_medis}/{tanggal}', [ResepsionisDashboardController::class, 'getHasilPeriksaDetail'])->name('resepsionis.hasil.periksa.detail');
});

Route::middleware(['auth', 'role:doktergigi'])->group(function () {
    Route::get('/gigi/riwayat-berobat/{no_rekam_medis}', [\App\Http\Controllers\GigiDashboardController::class, 'getRiwayatBerobat'])->name('gigi.riwayat.berobat');
    Route::get('/gigi/hasil-periksa-detail/{no_rekam_medis}/{tanggal}', [\App\Http\Controllers\GigiDashboardController::class, 'getHasilPeriksaDetail'])->name('gigi.hasil.periksa.detail');
});

Route::get('/', function () {
    return redirect()->route('login');
});

use Illuminate\Support\Facades\Auth;

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }
    switch ($user->role) {
        case 'dokter':
            return redirect()->route('dokter.dashboard');
        case 'doktergigi':
            return redirect('/gigi');
        case 'perawat':
            return redirect('/perawat');
        case 'resepsionis':
            return redirect()->route('resepsionis.dashboard');
        case 'apoteker':
            return redirect()->route('apoteker.dashboard');
        case 'kasir':
            return redirect()->route('kasir.dashboard');
        default:
            return redirect()->route('login');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dokter', [DokterDashboardController::class, 'index'])->middleware('role:dokter')->name('dokter.dashboard');
    Route::get('/dokter/pasien', [DokterDashboardController::class, 'pasien'])->middleware('role:dokter')->name('dokter.pasien');
    Route::get('/dokter/antrian', [DokterDashboardController::class, 'antrian'])->middleware('role:dokter')->name('dokter.antrian');

    // Tambahkan route profile dokter
    Route::get('/dokter/profile', [App\Http\Controllers\DokterDashboardController::class, 'profile'])->middleware('role:dokter')->name('dokter.profile');
    Route::patch('/dokter/profile', [App\Http\Controllers\DokterDashboardController::class, 'updateProfile'])->middleware('role:dokter')->name('dokter.profile.update');

    // Tambahkan route untuk dokter gigi
    Route::patch('/gigi/profile', [App\Http\Controllers\GigiDashboardController::class, 'updateProfile'])->middleware('role:doktergigi')->name('gigi.profile.update');
    Route::get('/gigi/profile', [App\Http\Controllers\GigiDashboardController::class, 'profile'])->middleware('role:doktergigi')->name('gigi.profile');
    Route::get('/gigi', [App\Http\Controllers\GigiDashboardController::class, 'index'])->middleware('role:doktergigi')->name('gigi.dashboard');
    Route::get('/gigi/pasien', [App\Http\Controllers\GigiDashboardController::class, 'pasien'])->middleware('role:doktergigi')->name('gigi.pasien');
    Route::get('/gigi/antrian', [App\Http\Controllers\GigiDashboardController::class, 'antrian'])->middleware('role:doktergigi')->name('gigi.antrian');
    Route::post('/gigi/hasilperiksa/store', [App\Http\Controllers\GigiDashboardController::class, 'storeHasilPeriksa'])->middleware('role:doktergigi')->name('gigi.hasilperiksa.store');
    Route::post('/gigi/hasilperiksa-gigi/store', [App\Http\Controllers\GigiDashboardController::class, 'storeHasilPeriksaGigi'])->middleware('role:doktergigi')->name('gigi.hasilperiksagigi.store');

    Route::post('/dokter/hasilperiksa/store', [DokterDashboardController::class, 'storeHasilPeriksa'])->middleware('role:dokter')->name('dokter.hasilperiksa.store');

    Route::get('/perawat', [PerawatDashboardController::class, 'index'])->middleware('role:perawat')->name('perawat.dashboard');
    Route::get('/perawat/antrian', [PerawatDashboardController::class, 'antrian'])->middleware('role:perawat')->name('perawat.antrian');
    Route::get('/apoteker', [ApotekerDashboardController::class, 'index'])->middleware('role:apoteker')->name('apoteker.dashboard');

    Route::get('/apoteker/profile', [ApotekerDashboardController::class, 'profile'])->middleware(['auth', 'role:apoteker'])->name('profileapoteker');

    Route::patch('/apoteker/profile', [ApotekerDashboardController::class, 'updateProfile'])->middleware(['auth', 'role:apoteker'])->name('apoteker.profile.update');

    // Kasir routes
    Route::middleware(['auth', 'role:kasir'])->group(function () {
        Route::get('/kasir', [KasirDashboardController::class, 'index'])->name('kasir.dashboard');
        Route::get('/kasir/dashboard', [KasirDashboardController::class, 'index'])->name('kasir.dashboard');
        Route::get('/kasir/pasien', [KasirDashboardController::class, 'pasien'])->name('kasir.pasien');
        Route::get('/kasir/antrian', [KasirDashboardController::class, 'antrian'])->name('kasir.antrian');
        Route::get('/kasir/hasil-periksa/{pasienId}', [KasirDashboardController::class, 'getHasilPeriksa'])->name('kasir.hasil.periksa');
        Route::get('/kasir/tagihan/{pasienId}', [KasirDashboardController::class, 'getTagihan'])->name('kasir.tagihan');
        Route::post('/kasir/tagihan/bayar', [KasirDashboardController::class, 'bayarTagihan'])->name('kasir.tagihan.bayar');
        Route::get('/kasir/profile', [KasirDashboardController::class, 'profile'])->name('kasir.profile');
        Route::patch('/kasir/profile', [KasirDashboardController::class, 'updateProfile'])->name('kasir.profile.update');
        Route::get('/kasir/dana', [KasirDashboardController::class, 'dana'])->name('kasir.dana');
        Route::get('/kasir/dana/export-pdf', [KasirDashboardController::class, 'exportPdf'])->name('kasir.dana.exportPdf');
        Route::get('/kasir/dana/export-excel', [KasirDashboardController::class, 'exportExcel'])->name('kasir.dana.exportExcel');
    });
});



require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:resepsionis'])->group(function () {
    Route::get('/resepsionis', [ResepsionisDashboardController::class, 'index'])->name('resepsionis.dashboard');

    Route::get('/resepsionis/profile', [App\Http\Controllers\ResepsionisDashboardController::class, 'profile'])->name('resepsionis.profile');

    Route::patch('/resepsionis/profile', [App\Http\Controllers\ResepsionisDashboardController::class, 'updateProfile'])->name('resepsionis.profile.update');

    // Add route for antrian.store
    Route::get('/antrian', [\App\Http\Controllers\ResepsionisDashboardController::class, 'antrian'])->name('resepsionis.antrian');
    Route::post('/antrian/store', [\App\Http\Controllers\AntrianController::class, 'store'])->name('antrian.store');

    Route::get('/cari-pasien/{nomorKepesertaan}', [\App\Http\Controllers\AntrianController::class, 'searchPasien']);

    Route::get('/pasienResepsionis', [ResepsionisDashboardController::class, 'pasien'])->name('resepsionis.pasien');

    Route::get('/pasienResepsionis/export-pdf', [ResepsionisDashboardController::class, 'exportPdf'])->name('resepsionis.pasien.exportPdf');

    Route::get('/pasienResepsionis/export-excel', [ResepsionisDashboardController::class, 'exportExcel'])->name('resepsionis.pasien.exportExcel');

    // Route::post('/pasienResepsionis/import-excel', [ResepsionisDashboardController::class, 'importExcel'])->name('resepsionis.pasien.importExcel');

    Route::post('/pasienResepsionis/tambah', [ResepsionisDashboardController::class, 'tambahPasien'])->name('resepsionis.pasien.tambah');

    Route::post('/pasienResepsionis/update/{no_rekam_medis}', [ResepsionisDashboardController::class, 'updatePasien'])->name('resepsionis.pasien.update');

    Route::get('/pasienResepsionis/detail/{no_rekam_medis}', [ResepsionisDashboardController::class, 'getPasienDetail'])->name('resepsionis.pasien.detail');

});
