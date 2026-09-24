<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('auth')->name('api.auth.')->group(function () {
    // POST /api/auth/register -- daftar lembaga baru + admin pertamanya.
    // Status company otomatis "pending" sampai link aktivasi di email diklik.
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register');

    // POST /api/auth/resend-activation -- kirim ulang email aktivasi.
    Route::post('/resend-activation', [AuthController::class, 'resendActivation'])
        ->middleware('throttle:3,1')
        ->name('resend-activation');

    // GET /api/auth/tenant/{subdomain} -- cek subdomain lembaga (dan status
    // aktivasinya) sebelum user isi email/password. Setara gerbang "cari
    // lembaga" di web.
    Route::get('/tenant/{subdomain}', [AuthController::class, 'tenant'])->name('tenant');

    // POST /api/auth/login -- subdomain wajib utk user lembaga, kosong
    // khusus superadmin. Dibatasi rate limit supaya tahan brute force.
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});