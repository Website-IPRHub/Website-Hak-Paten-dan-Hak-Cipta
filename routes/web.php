<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;

use App\Http\Controllers\PatenController;
use App\Http\Controllers\PatenVerifController;
use App\Http\Controllers\HakCiptaController;

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FileUploadCiptaController;

use App\Http\Controllers\HakCiptaSubmitController;
use App\Http\Controllers\HakPatenSubmitController;
use App\Http\Controllers\HakPatenSubmit2Controller;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\IsiformController;
use App\Http\Controllers\InvensiController;
use App\Http\Controllers\PengalihanHakController;
use App\Http\Controllers\SkemaController;
use App\Http\Controllers\FormPendaftaranCiptaanController;
use App\Http\Controllers\PernyataanCiptaController;
use App\Http\Controllers\PengalihanHakCiptaController;

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::post('/password', [AuthController::class, 'updatePassword'])->name('admin.password.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::put('/paten/{id}/status', [AdminDashboardController::class, 'updateStatusPaten'])->name('admin.paten.updateStatus');
    Route::put('/cipta/{id}/status', [AdminDashboardController::class, 'updateStatusCipta'])->name('admin.cipta.updateStatus');

    Route::delete('/paten/{id}', [AdminDashboardController::class, 'destroyPaten'])->name('admin.paten.destroy');
    Route::delete('/cipta/{id}', [AdminDashboardController::class, 'destroyCipta'])->name('admin.cipta.destroy');

    Route::put('/status/{type}/{id}', [AdminDashboardController::class, 'updateStatusVerifikasi'])->name('admin.status.update');
    Route::post('/status/{type}/{id}/upload-sertifikat', [AdminDashboardController::class, 'uploadSertifikatVerifikasi'])->name('admin.status.uploadSertifikat');
    Route::post('/status/{type}/{id}/resend-email', [AdminDashboardController::class, 'resendEmail'])->name('admin.status.resendEmail');

    Route::post('/verifikasi-dokumen/{type}/{id}/set', [AdminDashboardController::class, 'setVerifikasiDokumen'])->name('admin.verifikasi_dokumen.set');
    Route::post('/verifikasi-dokumen/{type}/{id}/send-revisi', [AdminDashboardController::class, 'sendRevisiEmail'])->name('admin.verifikasi_dokumen.sendRevisi');
});

Route::get('/debug-mail', function () {
    return [
        'mailer'   => config('mail.default'),
        'host'     => config('mail.mailers.smtp.host'),
        'port'     => config('mail.mailers.smtp.port'),
        'username' => config('mail.mailers.smtp.username'),
        'from'     => config('mail.from.address'),
    ];
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Landing)
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('welcome');
Route::get('/header', fn () => view('test-header'))->name('test-header');

Route::get('/hak-paten', fn () => view('hakpaten.menuhakpaten'))->name('menuhakpaten');

Route::get('/hak-cipta/form', fn () => view('hakcipta.menucipta'))->name('menucipta');
Route::get('/hak-paten/form', fn () => view('hakpaten.hakpaten'))->name('hakpaten');

Route::get('/hak-cipta', fn () => view('hakcipta.hakcipta'))->name('hakcipta');

/*
|--------------------------------------------------------------------------
| API ROUTES (JSON) - optional
|--------------------------------------------------------------------------
*/
Route::post('/hak-cipta/store', [HakCiptaController::class, 'store']);
Route::post('/paten/store', [PatenController::class, 'store']);

/*
|--------------------------------------------------------------------------
| HAK PATEN - ISI FORMULIR (separate flow)
|--------------------------------------------------------------------------
*/
Route::prefix('hak-paten')->group(function () {
    Route::view('/draft-paten-isiformulir', 'hakpaten.isiformulir.draftpatenisiformulir')
        ->name('hakpaten.draftpatenisiformulir');

    Route::get('/isi-formulir', fn () => view('hakpaten.isiformulir.isiformulir'))
        ->name('hakpaten.isiformulir');

    Route::get('/invensi', fn () => view('hakpaten.isiformulir.invensiformulir'))
        ->name('hakpaten.invensiformulir');

    Route::get('/pengalihan', fn () => view('hakpaten.isiformulir.pengalihanhakformulir'))
        ->name('hakpaten.pengalihanhakformulir');

    Route::get('/peralihan', fn () => view('hakpaten.isiformulir.peralihankeverif'))
        ->name('hakpaten.peralihankeverif');
});

Route::post('/isiform', [IsiformController::class, 'store'])->name('isiform.store');
Route::post('/invensi', [InvensiController::class, 'store'])->name('invensi.store');
Route::post('/pengalihan', [PengalihanHakController::class, 'store'])->name('pengalihanhak.store');

/*
|--------------------------------------------------------------------------
| PATEN VERIF FLOW (verifikasi dokumen)
|--------------------------------------------------------------------------
*/
Route::view('/hak-paten/data-diri', 'hakpaten.verifikasidokumen.datadiri')->name('patenverif.datadiri');

// Step 1 submit -> create verif record
Route::post('/paten-verif', [PatenVerifController::class, 'start'])->name('patenverif.start');

// Step pages (GET)
Route::view('/hak-paten/data-diri', 'hakpaten.verifikasidokumen.datadiri')
  ->name('patenverif.datadiri');
Route::get('/paten-verif/{verif}/draft', [PatenVerifController::class, 'draft'])->name('patenverif.draft');
Route::get('/paten-verif/{verif}/formpermohonan', [PatenVerifController::class, 'formpermohonan'])->name('patenverif.formpermohonan');
Route::get('/paten-verif/{verif}/invensi', [PatenVerifController::class, 'invensi'])->name('patenverif.invensi');
Route::get('/paten-verif/{verif}/pengalihanhak', [PatenVerifController::class, 'pengalihanhak'])->name('patenverif.pengalihanhak');
Route::get('/paten-verif/{verif}/scanktp', [PatenVerifController::class, 'scanktp'])->name('patenverif.scanktp');
Route::get('/paten-verif/{verif}/uploadgambar', [PatenVerifController::class, 'uploadgambar'])->name('patenverif.uploadgambar');
Route::get('/paten-verif/{verif}/deskripsiprodukverif', [PatenVerifController::class, 'deskripsiprodukverif'])->name('patenverif.deskripsi');
Route::post('/paten-verif/{verif}/submit', [PatenVerifController::class, 'submitFinal'])->name('patenverif.submit.final');

// =========================
// HALAMAN HASIL SUBMIT
// =========================
Route::get('/paten-verif/{verif}/hasil', [PatenVerifController::class, 'hasilSubmit']
)->name('patenverif.hasil');

// Submit actions
Route::post('/paten-verif/{verif}/upload-draft', [PatenVerifController::class, 'uploadDraft'])->name('patenverif.upload.draft');
Route::post('/paten-verif/{verif}/submit-deskripsi', [PatenVerifController::class, 'submitDeskripsi'])->name('patenverif.submit');
Route::post('/paten-verif/{verif}/upload-form', [PatenVerifController::class, 'uploadForm'])
  ->name('patenverif.upload.form');
Route::post('/paten-verif/{verif}/upload-invensi', [PatenVerifController::class, 'uploadInvensi'])
  ->name('patenverif.upload.invensi');
Route::post('/paten-verif/{verif}/upload-pengalihan', [PatenVerifController::class, 'uploadPengalihan'])
  ->name('patenverif.upload.pengalihan');
Route::post('/paten-verif/{verif}/upload-ktp', [PatenVerifController::class, 'uploadKTP'])
  ->name('patenverif.upload.ktp');
Route::post('/paten-verif/{verif}/upload-gambar', [PatenVerifController::class, 'uploadGambarr'])
  ->name('patenverif.upload.gambar');


// Skema pengembangan
Route::get('/paten-verif/{verif}/skema', [SkemaController::class, 'showVerif'])->name('patenverif.skema.form');
Route::post('/paten-verif/{verif}/skema/download', [SkemaController::class, 'downloadVerif'])->name('patenverif.skema.download');
Route::post('/paten-verif/{verif}/skema/upload', [SkemaController::class, 'uploadVerif'])->name('patenverif.skema.upload');

/*
|--------------------------------------------------------------------------
| HAK PATEN FLOW
|--------------------------------------------------------------------------
*/
// START: bikin row paten + set session paten_id
Route::post('/hak-paten/start', [PatenController::class, 'start'])->name('paten.start');

// STEP PAGES (GET) - ANTI SKIP
Route::middleware('paten.seq')->group(function () {
    Route::get('/hak-paten/draftpaten', fn () => view('hakpaten.draftpaten'))->name('draftpaten');
    Route::get('/hak-paten/formulirpermohonan', fn () => view('hakpaten.formulirpermohonan'))->name('formulirpermohonan');
    Route::get('/hak-paten/kepemilikaninvensi', fn () => view('hakpaten.kepemilikaninvensi'))->name('kepemilikaninvensi');
    Route::get('/hak-paten/pengalihanhak', fn () => view('hakpaten.pengalihanhak'))->name('pengalihanhak');
    Route::get('/hak-paten/scanktp', fn () => view('hakpaten.scanktp'))->name('scanktp');
    Route::get('/hak-paten/tandaterima', fn () => view('hakpaten.tandaterima'))->name('tandaterima');
    Route::get('/hak-paten/uploadgambarprototipe', fn () => view('hakpaten.uploadgambarprototipe'))->name('uploadgambarprototipe');
    Route::get('/hak-paten/deskripsiproduk', fn () => view('hakpaten.deskripsiproduk'))->name('deskripsiproduk');
});

// sukses
Route::get('/hak-paten/sukses', fn () => view('hakpaten.sukses'))->name('hakpaten.sukses');


// PATEN (baru)
Route::get('/hak-paten/{paten}/skema', [SkemaController::class, 'showPaten'])->name('hakpaten.skema.form');
Route::post('/hak-paten/{paten}/skema/download', [SkemaController::class, 'downloadPaten'])->name('hakpaten.skema.download');
Route::post('/hak-paten/{paten}/skema/upload', [SkemaController::class, 'uploadPaten'])->name('hakpaten.skema.upload');

// UPLOAD PER STEP (POST)
Route::post('/hak-paten/upload-draft', [FileUploadController::class, 'draft'])->name('draftpaten.upload');
Route::post('/hak-paten/upload-form', [FileUploadController::class, 'form'])->name('formulirpermohonan.upload');
Route::post('/hak-paten/upload-surat-invensi', [FileUploadController::class, 'suratInvensi'])->name('kepemilikaninvensi.upload');
Route::post('/hak-paten/upload-surat-pengalihan-hak', [FileUploadController::class, 'pengalihanhak'])->name('pengalihanhak.upload');
Route::post('/hak-paten/upload-scanktp', [FileUploadController::class, 'scanKtp'])->name('scanktp.uploadScanKTP');
Route::post('/hak-paten/upload-surat-terima-berkas', [FileUploadController::class, 'tandaTerima'])->name('tandaterima.uploadFormSuratTandaTerimaBerkas');
Route::post('/hak-paten/upload-prototipe', [FileUploadController::class, 'gambarPrototipe'])->name('uploadgambarprototipe.uploadPrototipe');

// SUBMIT FINAL (POST)
Route::post('/hak-paten/submit', [HakPatenSubmit2Controller::class, 'submit'])->name('hakpaten.submit');

// DOWNLOAD TEMPLATE (GET)
Route::get('/hak-paten/download-template-draft', function () {
    $path = public_path('templates/Template Deskripsi Paten.docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Template Deskripsi Paten.docx');
})->name('download.template.draftpaten');

Route::get('/hak-paten/download-template-form', function () {
    $path = public_path('templates/Form Daftar Paten (2025).docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Form Daftar Paten (2025).docx');
})->name('download.template.formpaten');

Route::get('/hak-paten/download-template-surat-invensi', function () {
    $path = public_path('templates/Surat Pernyataan Kepemilikan Invensi oleh Inventor.docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Surat Pernyataan Kepemilikan Invensi oleh Inventor.docx');
})->name('download.template.surat_invensi');

Route::get('/hak-paten/download-template-surat-pengalihan-hak', function () {
    $path = public_path('templates/Surat Pernyataan Pengalihan Hak.docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Surat Pernyataan Pengalihan Hak.docx');
})->name('download.template.pengalihan_hak');

Route::get('/hak-paten/download-template-surat-terima-berkas', function () {
    $path = public_path('templates/TANDA_TERIMA_BERKAS_HAKI.pdf');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'TANDA_TERIMA_BERKAS_HAKI.pdf');
})->name('download.template.tanda_terima_paten');


/*
|--------------------------------------------------------------------------
| HAK CIPTA FLOW ISI FORM
|--------------------------------------------------------------------------
*/
Route::prefix('hak-cipta')->group(function () {
    Route::view('/pendaftaranCiptaan', 'hakcipta.isiform.formpendaftaranciptaan')
        ->name('formpendaftarancipta');

    Route::view('/suratpernyataan', 'hakcipta.isiform.suratpernyataan')
        ->name('suratpernyataan');

    Route::view('/pengalihanhakcipta', 'hakcipta.isiform.pengalihanhakcipta')
        ->name('pengalihanhakcipta');
        Route::get('/peralihancipta', fn () => view('hakcipta.isiform.peralihanverifcipta'))
        ->name('peralihanverifcipta');
});


Route::post('/hak-cipta/isiform', [FormPendaftaranCiptaanController::class, 'store'])
  ->name('hakcipta.isiform.store');
Route::post('/hak-cipta/suratpernyataan', [PernyataanCiptaController::class, 'store'])
  ->name('hakcipta.suratpernyataan.store');
Route::post('/hak-cipta/pengalihanhakcipta', [PengalihanHakCiptaController::class, 'store'])
  ->name('hakcipta.pengalihanhakcipta.store');
/*
|--------------------------------------------------------------------------
| HAK CIPTA FLOW (session + anti-skip)
|--------------------------------------------------------------------------
*/
// Step 1 (create row + set session cipta_id)
Route::post('/hak-cipta/start', [HakCiptaController::class, 'start'])->name('hakcipta.start');

// Step pages (GET) - anti skip
// Route::middleware('cipta.seq')->group(function () {
//     Route::get('/hak-cipta/permohonan-pendaftaran', fn () => view('hakcipta.permohonanpendaftaran'))->name('hakcipta.permohonanpendaftaran');
//     Route::get('/hak-cipta/suratpernyataan', fn () => view('hakcipta.suratpernyataan'))->name('hakcipta.suratpernyataan');
//     Route::get('/hak-cipta/pengalihanhak', fn () => view('hakcipta.pengalihanhak'))->name('hakcipta.pengalihanhak');
//     Route::get('/hak-cipta/tandaterima', fn () => view('hakcipta.tandaterima'))->name('hakcipta.tandaterima');
//     Route::get('/hak-cipta/scanktp', fn () => view('hakcipta.scanktp'))->name('hakcipta.scanktp');
//     Route::get('/hak-cipta/hasilciptaan', fn () => view('hakcipta.hasilciptaan'))->name('hakcipta.hasilciptaan');
//     Route::get('/hak-cipta/linkciptaan', fn () => view('hakcipta.linkciptaan'))->name('hakcipta.linkciptaan');
// });

// Upload per step (POST)
Route::post('/hak-cipta/upload-permohonan', [FileUploadCiptaController::class, 'suratPermohonan'])->name('hakcipta.permohonanpendaftaran.upload');
Route::post('/hak-cipta/upload-pernyataan', [FileUploadCiptaController::class, 'suratPernyataan'])->name('hakcipta.suratpernyataan.upload');
Route::post('/hak-cipta/upload-pengalihan', [FileUploadCiptaController::class, 'suratPengalihan'])->name('hakcipta.pengalihanhak.upload');
Route::post('/hak-cipta/upload-scanktp', [FileUploadCiptaController::class, 'scanKtp'])->name('hakcipta.scanktp.uploadScanKTP');
Route::post('/hak-cipta/upload-tandaterima', [FileUploadCiptaController::class, 'tandaTerima'])->name('hakcipta.tandaterima.upload');
Route::post('/hak-cipta/upload-hasilciptaan', [FileUploadCiptaController::class, 'hasilCiptaan'])->name('hakcipta.hasilciptaan.uploadScanKTP');
Route::post('/hak-cipta/simpan-link', [FileUploadCiptaController::class, 'linkCiptaan'])->name('hakcipta.linkciptaan.store');

// Download template hak cipta
Route::prefix('hak-cipta')->group(function () {
    Route::get('/download-template-permohonan', function () {
        $path = public_path('templates/Permohonan Pendaftaran Ciptaan 2021.docx');
        if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
        return response()->download($path, 'Permohonan Pendaftaran Ciptaan 2021.docx');
    })->name('hakcipta.download.template.permohonan');

    Route::get('/download-template-pernyataan', function () {
        $path = public_path('templates/Surat Pernyataan Hak Cipta 2021.docx');
        if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
        return response()->download($path, 'Surat Pernyataan Hak Cipta 2021.docx');
    })->name('hakcipta.download.template.pernyataan');

    Route::get('/download-template-pengalihan', function () {
        $path = public_path('templates/Surat Pengalihan Hak Cipta 2025.docx');
        if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
        return response()->download($path, 'Surat Pengalihan Hak Cipta 2025.docx');
    })->name('hakcipta.download.template.pengalihan');

    Route::get('/download-template-tandaterima', function () {
        $path = public_path('templates/TANDA_TERIMA_BERKAS_HAKI.pdf');
        if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
        return response()->download($path, 'TANDA_TERIMA_BERKAS_HAKI.pdf');
    })->name('hakcipta.download.template.tandaterima');
});

// SUBMIT FINAL (POST) + sukses
Route::post('/hak-cipta/submit', [HakCiptaSubmitController::class, 'submit'])->name('hakcipta.submit');
Route::get('/hak-cipta/sukses', fn () => view('hakcipta.sukses'))->name('hakcipta.sukses');

/*
|--------------------------------------------------------------------------
| TRACKING
|--------------------------------------------------------------------------
*/
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
