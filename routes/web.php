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
