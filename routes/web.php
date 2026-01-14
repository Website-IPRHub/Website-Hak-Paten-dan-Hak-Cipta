<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\HakCiptaController;
use App\Http\Controllers\PatenController;

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])
    ->name('admin.login.form');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->name('admin.login');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');

Route::put('/admin/paten/{id}/status', [AdminDashboardController::class, 'updateStatusPaten'])
    ->name('admin.paten.updateStatus');

Route::put('/admin/cipta/{id}/status', [AdminDashboardController::class, 'updateStatusCipta'])
    ->name('admin.cipta.updateStatus');

Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::post('/hak-cipta/store', [HakCiptaController::class, 'store']);
Route::post('/paten/store', [PatenController::class, 'store']);

use Illuminate\Support\Facades\File;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HakPatenSubmitController;

Route::get('/', fn () => view('welcome'))->name('welcome');
Route::get('/header', fn () => view('test-header'))->name('test-header');

// LANDING
Route::get('/hak-paten', fn () => view('hakpaten.hakpaten'))->name('hakpaten');
Route::get('/hak-cipta', fn () => view('hakcipta.hakcipta'))->name('hakcipta');

// START: bikin row paten + set session paten_id
Route::post('/hak-paten/start', [PatenController::class, 'start'])->name('paten.start');

// STEP PAGES (GET) - WAJIB MASUK GROUP INI (ANTI SKIP)
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

 // Submit
    Route::get('/hak-paten/sukses', fn () => view('hakpaten.sukses'))->name('hakpaten.sukses');

// UPLOAD PER STEP (POST)
Route::post('/hak-paten/upload-draft', [FileUploadController::class, 'draft'])->name('draftpaten.upload');
Route::post('/hak-paten/upload-form', [FileUploadController::class, 'form'])->name('formulirpermohonan.upload');
Route::post('/hak-paten/upload-surat-invensi', [FileUploadController::class, 'suratInvensi'])->name('kepemilikaninvensi.upload');
Route::post('/hak-paten/upload-surat-pengalihan-hak', [FileUploadController::class, 'suratPengalihan'])->name('pengalihanhak.upload');
Route::post('/hak-paten/upload-scanktp', [FileUploadController::class, 'scanKtp'])->name('scanktp.uploadScanKTP');
Route::post('/hak-paten/upload-surat-terima-berkas', [FileUploadController::class, 'tandaTerima'])->name('tandaterima.uploadFormSuratTandaTerimaBerkas');
Route::post('/hak-paten/upload-prototipe', [FileUploadController::class, 'gambarPrototipe'])->name('uploadgambarprototipe.uploadPrototipe');

// SUBMIT FINAL (POST)
Route::post('/hak-paten/submit', [HakPatenSubmitController::class, 'submit'])->name('hakpaten.submit');

// DOWNLOAD TEMPLATE ROUTES (GET)
Route::get('/hak-paten/download-template-draft', function () {
    $path = public_path('templates/Template Deskripsi Paten.docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Template Deskripsi Paten.docx');
})->name('download.template.draftpaten');

Route::get('/hak-paten/download-template-form', function () {
    $path = public_path('templates/Form Daftar Paten (2025).docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Form Daftar Paten (2025).docx');
})->name('download.template.Form Daftar Paten (2025)');

Route::get('/hak-paten/download-template-surat-invensi', function () {
    $path = public_path('templates/Surat Pernyataan Kepemilikan Invensi oleh Inventor.docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Surat Pernyataan Kepemilikan Invensi oleh Inventor.docx');
})->name('download.template.Surat Pernyataan Kepemilikan Invensi oleh Inventor');

Route::get('/hak-paten/download-template-surat-pengalihan-hak', function () {
    $path = public_path('templates/Surat Pernyataan Pengalihan Hak.docx');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'Surat Pernyataan Pengalihan Hak.docx');
})->name('download.template.Surat Pernyataan Pengalihan Hak');

Route::get('/hak-paten/download-template-surat-terima-berkas', function () {
    $path = public_path('templates/TANDA_TERIMA_BERKAS_HAKI.pdf');
    if (!File::exists($path)) abort(404, 'File Tidak Tersedia');
    return response()->download($path, 'TANDA_TERIMA_BERKAS_HAKI.pdf');
})->name('download.template.Surat Tanda Terima Berkas');
