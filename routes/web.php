<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DokterDashboardController;
use App\Http\Controllers\PerawatDashboardController;
use App\Http\Controllers\ResepsionisDashboardController;
use App\Http\Controllers\ApotekerDashboardController;
use App\Http\Controllers\KasirDashboardController;
use App\Http\Controllers\GigiDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerawatHasilAnalisaController;
use App\Http\Controllers\PerawatProfileController;
use App\Http\Controllers\PasienController;
use \App\Http\Controllers\BidanProfileController;
use App\Http\Controllers\RawatinapUgdController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminObatController;
use App\Http\Controllers\AdminJadwalDokterController;
use App\Models\PasiensUgd;
use App\Models\Pasien;
use App\Models\User;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/ugd', function () {
        $pasiens_ugd = \App\Models\PasiensUgd::with('pasien')->where('status', 'Perlu Analisa')->get();
        return view('admin.ugd', compact('pasiens_ugd'));
    })->name('admin.ugd');

    Route::get('/admin/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

    Route::get('/rawatinap/ugd/detail/{no_rekam_medis}', [App\Http\Controllers\RawatinapUgdController::class, 'getUgdPatientDetail'])->name('rawatinap.ugd.detail');

    // Route::get('/admin/rawatinap', [\App\Http\Controllers\RawatinapUgdController::class, 'adminRawatinap'])->name('admin.rawatinap');

Route::get('/admin/datapasien', function () {
    $pasiens = \App\Models\Pasien::paginate(10);

    return view('admin.datapasien', compact('pasiens'));
})->name('admin.datapasien');

    Route::put('/admin/datapasien/{id}', [\App\Http\Controllers\PasienController::class, 'update'])->name('admin.datapasien.update');

    // New route for fetching riwayat berobat data for a pasien by pasien_id
    Route::get('/admin/datapasien/riwayat-berobat/{pasien_id}', [\App\Http\Controllers\RawatinapUgdController::class, 'getRiwayatBerobatByPasienId'])->name('admin.datapasien.riwayatberobat');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/datauser', [AdminUserController::class, 'index'])->name('admin.datauser');

    Route::get('/admin/obat', [AdminObatController::class, 'index'])->name('admin.obat');
    Route::post('/admin/obat/store', [AdminObatController::class, 'store'])->name('admin.obat.store');
    Route::get('/admin/obat/edit/{id}', [AdminObatController::class, 'edit'])->name('admin.obat.edit');
    Route::put('/admin/obat/update/{id}', [AdminObatController::class, 'update'])->name('admin.obat.update');

    Route::delete('/admin/obat/delete/{id}', [AdminObatController::class, 'destroy'])->name('admin.obat.destroy');

    Route::put('/admin/users/edit/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');

    Route::post('/admin/users/create', [AdminUserController::class, 'store'])->name('admin.users.store');

    Route::delete('/admin/users/delete/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/jadwaldokter', [AdminJadwalDokterController::class, 'index'])->name('admin.jadwaldokter');

    Route::post('/admin/jadwaldokter/store', [AdminJadwalDokterController::class, 'store'])->name('admin.jadwaldokter.store');

    Route::delete('/admin/jadwaldokter/delete/{id}', [AdminJadwalDokterController::class, 'destroy'])->name('admin.jadwaldokter.destroy');

    Route::get('/admin/jadwaldokter/edit/{id}', [AdminJadwalDokterController::class, 'edit'])->name('admin.jadwaldokter.edit');
    Route::put('/admin/jadwaldokter/update/{id}', [AdminJadwalDokterController::class, 'update'])->name('admin.jadwaldokter.update');

    // Added route for admin rawat jalan page
    Route::get('/admin/rawatjalan', [PasienController::class, 'index'])->middleware(['auth', 'role:admin'])->name('admin.rawatjalan');

    // Added route for admin pasien rawat jalan page with path matching sidebar URL
    // Re-adding route for admin pasien rawat jalan page with path matching sidebar URL
    Route::get('/admin/pasien/rawatjalan', [PasienController::class, 'index'])->middleware(['auth', 'role:admin'])->name('admin.pasien.rawatjalan');

    // Added route for admin pasien rawat inap page with path matching sidebar URL
    Route::get('/admin/pasien/rawatinap', [RawatinapUgdController::class, 'adminPasienRawatinap'])->middleware(['auth', 'role:admin'])->name('admin.pasien.rawatinap');

    // Routes for edit, update, destroy, and surat actions for rawatinap
    Route::get('/admin/rawatinap/{id}/edit', [RawatinapUgdController::class, 'edit'])->middleware(['auth', 'role:admin'])->name('admin.rawatinap.edit');
    Route::put('/admin/rawatinap/{id}', [RawatinapUgdController::class, 'update'])->middleware(['auth', 'role:admin'])->name('admin.rawatinap.update');
    Route::delete('/admin/rawatinap/{id}', [RawatinapUgdController::class, 'destroy'])->middleware(['auth', 'role:admin'])->name('admin.rawatinap.destroy');
    Route::get('/admin/rawatinap/{id}/surat', [RawatinapUgdController::class, 'surat'])->middleware(['auth', 'role:admin'])->name('admin.rawatinap.surat');

    // Routes for edit, update, destroy, and     surat actions for rawatjalan
    Route::get('/admin/rawatjalan/{id}/edit', [PasienController::class, 'edit'])->middleware(['auth', 'role:admin'])->name('admin.rawatjalan.edit');
    Route::put('/admin/rawatjalan/{id}', [PasienController::class, 'update'])->middleware(['auth', 'role:admin'])->name('admin.rawatjalan.update');
    Route::delete('/admin/rawatjalan/{id}', [PasienController::class, 'destroy'])->middleware(['auth', 'role:admin'])->name('admin.rawatjalan.destroy');
    Route::get('/admin/rawatjalan/{id}/surat', [PasienController::class, 'surat'])->middleware(['auth', 'role:admin'])->name('admin.rawatjalan.surat');

    Route::get('/admin/datapasien/{id}/surat', [PasienController::class, 'surat'])->middleware(['auth', 'role:admin'])->name('cetak.surat');
});

Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/pasien', [ApotekerDashboardController::class, 'pasien'])->name('apoteker.pasien');
    Route::get('/apoteker/obat', [ApotekerDashboardController::class, 'obat'])->name('apoteker.obat');
    Route::post('/apoteker/obat', [ApotekerDashboardController::class, 'storeObat'])->name('apoteker.obat.store');
    Route::get('/apoteker/antrian', [ApotekerDashboardController::class, 'antrian'])->name('apoteker.antrian');
    Route::get('/apoteker/obat/{id}', [ApotekerDashboardController::class, 'getObatDetail'])->name('apoteker.obat.detail');
    Route::post('/apoteker/antrian/update-status/{pasienId}', [ApotekerDashboardController::class, 'updateAntrianStatus'])->name('apoteker.antrian.updateStatus');
    Route::put('/apoteker/obat/{id}', [ApotekerDashboardController::class, 'updateObat'])->name('apoteker.obat.update');
    Route::delete('/apoteker/obat/{id}', [ApotekerDashboardController::class, 'destroyObat'])->name('apoteker.obat.destroy');

    // Export routes for obat
    Route::get('/apoteker/obat/export-pdf', [ApotekerDashboardController::class, 'exportPdf'])->name('apoteker.obat.exportPdf');
    Route::get('/apoteker/obat/export-excel', [ApotekerDashboardController::class, 'exportExcel'])->name('apoteker.obat.exportExcel');

    // Add route for apoteker getHasilPeriksa
    Route::get('/apoteker/hasil-periksa/{pasienId}', [ApotekerDashboardController::class, 'getHasilPeriksa'])->name('apoteker.hasil.periksa');

    // Add route for apoteker getTagihanApoteker
    Route::get('/apoteker/tagihan/{pasienId}', [ApotekerDashboardController::class, 'getTagihanApoteker'])->name('apoteker.tagihan');
});

Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter/pasien/{no_rekam_medis}/riwayat', [App\Http\Controllers\DokterDashboardController::class, 'riwayatBerobat'])
        ->name('dokter.riwayat');
    Route::get('/dokter/riwayat-berobat/{no_rekam_medis}/dates', [App\Http\Controllers\DokterDashboardController::class, 'getVisitDates'])->name('dokter.riwayat.dates');
    // API route to get hasil analisa and periksa data for a date
    Route::get('/dokter/riwayat-berobat/{no_rekam_medis}/{tanggal}', [App\Http\Controllers\DokterDashboardController::class, 'getVisitData'])->name('dokter.riwayat.data');
    Route::get('/dokter/hasil-analisa/{no_rekam_medis}', [\App\Http\Controllers\DokterDashboardController::class, 'hasilAnalisaAjax'])->name('dokter.hasilanalisa.ajax');
    // New route for medicine search API
    Route::get('/dokter/search-obat', [DokterDashboardController::class, 'searchObat'])->name('dokter.searchObat');
});

Route::middleware(['auth', 'role:resepsionis'])->group(function () {
    Route::get('/resepsionis/pasien/{no_rekam_medis}/riwayat', [App\Http\Controllers\ResepsionisDashboardController::class, 'riwayatBerobat'])
        ->name('resepsionis.riwayat');
    Route::get('/resepsionis/riwayat-berobat/{no_rekam_medis}/dates', [App\Http\Controllers\ResepsionisDashboardController::class, 'getVisitDates'])->name('resepsionis.riwayat.dates');
    // API route to get hasil analisa and periksa data for a date
    Route::get('/resepsionis/riwayat-berobat/{no_rekam_medis}/{tanggal}', [App\Http\Controllers\ResepsionisDashboardController::class, 'getVisitData'])->name('resepsionis.riwayat.data');

    Route::get('/resepsionis/hasil-periksa-detail/{no_rekam_medis}/{tanggal}', [ResepsionisDashboardController::class, 'getHasilPeriksaDetail'])->name('resepsionis.hasil.periksa.detail');
});

Route::middleware(['auth', 'role:doktergigi'])->group(function () {
    Route::get('/gigi/riwayat-berobat/{no_rekam_medis}', [\App\Http\Controllers\GigiDashboardController::class, 'getRiwayatBerobat'])->name('gigi.riwayat.berobat');
    Route::get('/gigi/hasil-periksa-detail/{no_rekam_medis}/{tanggal}', [\App\Http\Controllers\GigiDashboardController::class, 'getHasilPeriksaDetail'])->name('gigi.hasil.periksa.detail');
});

Route::middleware(['auth', 'role:perawat'])->group(function () {
    Route::get('/perawat/dashboard', function () {
        return view('perawat.dashboard');
    })->name('perawat.dashboard');

    // Hapus route antrian duplikat, gunakan controller agar konsisten
    Route::get('/perawat/antrian', [PerawatDashboardController::class, 'antrian'])->name('perawat.antrian');

    Route::get('/perawat/pasien', [PerawatDashboardController::class, 'pasien'])->name('perawat.pasien');
    Route::get('/perawat/pasien/detail/{no_rekam_medis}', [PerawatDashboardController::class, 'getPatientDetail'])->name('perawat.pasien.detail');
    Route::get('/perawat/pasien/detail-by-id/{pasien_id}', [PerawatDashboardController::class, 'getPatientDetailById'])->name('perawat.pasien.detailbyid');

    // Pastikan hanya satu route store hasil analisa
    Route::post('/perawat/hasilanalisa/store', [PerawatHasilAnalisaController::class, 'store'])->name('perawat.hasilanalisa.store');

    Route::get('/perawat/profile', [PerawatProfileController::class, 'show'])->name('perawat.profile');
    Route::patch('/perawat/profile', [PerawatProfileController::class, 'update'])->name('perawat.profile.update');

    Route::get('/perawat/jadwal-dokter', [PerawatDashboardController::class, 'jadwalDokter'])->name('perawat.jadwaldokter');
});

Route::middleware(['auth', 'role:bidan'])->group(function () {
    Route::get('/bidan/dashboard', function () {
        return view('bidan.dashboard');
    })->name('bidan.dashboard');

    Route::get('/bidan/antrian', [\App\Http\Controllers\BidanProfileController::class, 'antrian'])->name('bidan.antrian');

    Route::post('/bidan/hasilanalisa/store', [\App\Http\Controllers\BidanProfileController::class, 'storeHasilAnalisa'])->name('bidan.hasilanalisa.store');

    Route::post('/bidan/hasilperiksa-anak/store', [\App\Http\Controllers\BidanProfileController::class, 'storeHasilPeriksaAnak'])->name('bidan.hasilperiksa.anak.store');

    Route::get('/bidan/jadwaldokter', function () {
        $jadwalDokters = \App\Models\JadwalDokter::all();
        return view('bidan.jadwaldokter', compact('jadwalDokters'));
    })->name('bidan.jadwaldokter');

    Route::get('/bidan/pasien', [\App\Http\Controllers\BidanProfileController::class, 'pasien'])->name('bidan.pasien');

    Route::get('/bidan/pasien/detail/{no_rekam_medis}', function ($no_rekam_medis) {
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
        if (!$pasien) {
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        return response()->json($pasien);
    })->name('bidan.pasien.detail');

    Route::get('/bidan/profile', function () {
        return view('bidan.profile');
    })->name('bidan.profile');

    Route::patch('/bidan/profile/update', [BidanProfileController::class, 'update'])->middleware('auth')->name('bidan.profile.update');

    // Bidan - Riwayat Berobat Pasien
    Route::get('/bidan/pasien/{no_rekam_medis}/riwayat', [App\Http\Controllers\BidanProfileController::class, 'riwayatBerobat'])
        ->name('bidan.riwayat');

    // API route to get visit dates as JSON
    Route::get('/bidan/riwayat-berobat/{no_rekam_medis}/dates', [App\Http\Controllers\BidanProfileController::class, 'getVisitDates'])->name('bidan.riwayat.dates');

    // API route to get hasil analisa and periksa data for a date
    Route::get('/bidan/riwayat-berobat/{no_rekam_medis}/{tanggal}', [App\Http\Controllers\BidanProfileController::class, 'getVisitData'])->name('bidan.riwayat.data');

    Route::get('/bidan/hasil-analisa/{no_rekam_medis}', [\App\Http\Controllers\BidanProfileController::class, 'hasilAnalisaAjax'])->name('bidan.hasilanalisa.ajax');
});

Route::middleware(['auth', 'role:rawatinap'])->group(function () {
    Route::get('/rawatinap/dashboard', function () {
        $ugd_pasien = PasiensUgd::whereIn('status', ['Perlu Analisa', 'UGD'])->get();
        $rawatinap_pasien = PasiensUgd::where('status', 'Rawat Inap')->get();
        return view('rawatinap.dashboard', compact('ugd_pasien', 'rawatinap_pasien'));
    })->name('rawatinap.dashboard');

    Route::patch('/rawatinap/profile', [App\Http\Controllers\RawatinapUgdController::class, 'updateProfile'])->name('rawatinap.profile.update');

    Route::get('/pasienRawatinap/export-pdf', [RawatinapUgdController::class, 'exportPdf'])->name('rawatinap.pasien.exportPdf');

    Route::get('/pasienRawatinap/export-excel', [RawatinapUgdController::class, 'exportExcel'])->name('rawatinap.pasien.exportExcel');

    Route::post('/pasienRawatinap/tambah', [RawatinapUgdController::class, 'store'])->name('rawatinap.pasien.tambah');

    Route::get('/rawatinap/profile', [App\Http\Controllers\RawatinapUgdController::class, 'profile'])->name('rawatinap.profile');

    Route::post('/rawatinap/hasilanalisa/store', [App\Http\Controllers\RawatinapUgdController::class, 'storeAnalisa'])->name('rawatinap.hasilanalisa.store');

    Route::get('/rawatinap/pasien/by-id/{id}', [App\Http\Controllers\RawatinapUgdController::class, 'getPatientById'])->name('rawatinap.pasien.byid');

    // Add route for pasienrawatinap update to fix 404 error
    Route::post('/pasienrawatinap/update/{id}', [RawatinapUgdController::class, 'update'])->name('pasienrawatinap.update');

    Route::get('/rawatinap', function () {
        return redirect()->route('rawatinap.ugd');
    })->name('rawatinap');

    Route::get('/rawatinap/ugd', function () {
        $pasiens_ugd = PasiensUgd::whereIn('status', ['Perlu Analisa', 'UGD'])->paginate(10);
        $pasiens = Pasien::all();
        $users = \App\Models\User::all();
        return view('rawatinap.ugd', compact('pasiens_ugd', 'pasiens', 'users'));
    })->name('rawatinap.ugd');

    Route::get('/rawatinap/pasien/riwayat-berobat/{no_rekam_medis}', [RawatinapUgdController::class, 'getRiwayatBerobatByPasienId'])->name('rawatinap.pasien.riwayatberobat');

    Route::post('/rawatinap/ugd/store', [RawatinapUgdController::class, 'store'])->name('rawatinap.ugd.store');

    Route::post('/rawatinap/periksa/store', [RawatinapUgdController::class, 'storeHasilPeriksa'])->name('rawatinap.periksa.store');
    Route::get('/rawatinap/hasilperiksa/data/{pasien_id}', [RawatinapUgdController::class, 'getHasilPeriksaByPasienId'])->name('rawatinap.hasilperiksa.data');

    Route::get('/rawatinap/antrian', function () {
        // You can add logic to get rawatinap antrian data here
        return view('rawatinap.antrian');
    })->name('rawatinap.antrian');

    Route::get('/rawatinap/pasien', [PasienController::class, 'rawatinapPasien'])->name('rawatinap.pasien');
    Route::post('/rawatinap/update/{no_rekam_medis}', [RawatinapUgdController::class, 'updatePasien'])->name('rawatinap.pasien.update');

    Route::get('/rawatinap/pasien/detail/{no_rekam_medis}', [PasienController::class, 'getPatientDetail'])->name('rawatinap.pasien.detail');

    Route::post('/rawatinap/pasien/store', [PasienController::class, 'store'])->name('rawatinap.pasien.store');

    Route::get('/rawatinap/pasien/cari-nomor-kepesertaan', [App\Http\Controllers\PasienController::class, 'cariNomorKepesertaan']);
    Route::get('/rawatinap/hasilanalisa/riwayat/{pasien_id}', [App\Http\Controllers\RawatinapUgdController::class, 'getRiwayatAnalisaRawatinap']);

    // Detail pasien UGD by no_rekam_medis (ambil dari tabel pasiens)
    Route::get('/rawatinap/ugd/detail/{no_rekam_medis}', [App\Http\Controllers\RawatinapUgdController::class, 'getUgdPatientDetail'])->name('rawatinap.ugd.detail');

    Route::get('/rawatinap/riwayat-berobat/{no_rekam_medis}/dates', [App\Http\Controllers\RawatinapUgdController::class, 'getVisitDates'])->name('rawatinap.riwayat.dates');
    // API route to get hasil analisa and periksa data for a date
    Route::get('/rawatinap/riwayat-berobat/{no_rekam_medis}/{tanggal}', [App\Http\Controllers\RawatinapUgdController::class, 'getVisitData'])->name('rawatinap.riwayat.data');

    Route::get('/rawatinap/rawatinap', function () {
        $pasiens_ugd = \App\Models\PasiensUgd::where('status', 'Rawat Inap')->paginate(10);
        $users = User::all();
        return view('rawatinap.rawatinap', compact('pasiens_ugd', 'users'));
    })->name('rawatinap.rawatinap');
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
        case 'admin':
            return redirect()->route('admin.dashboard');
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
        case 'bidan':
            return redirect()->route('bidan.dashboard');
        case 'rawatinap':
            return redirect()->route('rawatinap.dashboard');
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



require __DIR__ . '/auth.php';

Route::middleware(['auth', 'role:resepsionis'])->group(function () {
    Route::get('/resepsionis', [ResepsionisDashboardController::class, 'index'])->name('resepsionis.dashboard');

    Route::get('/resepsionis/profile', [App\Http\Controllers\ResepsionisDashboardController::class, 'profile'])->name('resepsionis.profile');

    Route::patch('/resepsionis/profile', [App\Http\Controllers\ResepsionisDashboardController::class, 'updateProfile'])->name('resepsionis.profile.update');

    // Add route for antrian.store
    Route::get('/antrian', [\App\Http\Controllers\ResepsionisDashboardController::class, 'antrian'])->name('resepsionis.antrian');
    Route::post('/antrian/store', [\App\Http\Controllers\AntrianController::class, 'store'])->name('antrian.store');

    Route::middleware(['auth', 'role:rawatinap'])->group(function () {
        Route::get('/cari-pasien/{nomorKepesertaan}', [\App\Http\Controllers\AntrianController::class, 'searchPasien']);
    });

    Route::get('/pasienResepsionis', [ResepsionisDashboardController::class, 'pasien'])->name('resepsionis.pasien');

    Route::get('/pasienResepsionis/export-pdf', [ResepsionisDashboardController::class, 'exportPdf'])->name('resepsionis.pasien.exportPdf');

    Route::get('/pasienResepsionis/export-excel', [ResepsionisDashboardController::class, 'exportExcel'])->name('resepsionis.pasien.exportExcel');

    // Route::post('/pasienResepsionis/import-excel', [ResepsionisDashboardController::class, 'importExcel'])->name('resepsionis.pasien.importExcel');

    Route::post('/pasienResepsionis/tambah', [ResepsionisDashboardController::class, 'tambahPasien'])->name('resepsionis.pasien.tambah');

    Route::post('/pasienResepsionis/update/{no_rekam_medis}', [ResepsionisDashboardController::class, 'updatePasien'])->name('resepsionis.pasien.update');

    Route::get('/pasienResepsionis/detail/{no_rekam_medis}', [ResepsionisDashboardController::class, 'getPasienDetail'])->name('resepsionis.pasien.detail');
});
