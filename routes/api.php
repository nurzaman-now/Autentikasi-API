<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\EmailVerifyController;
use App\Http\Controllers\API\Auth\ResetPassController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::get('logout', 'logout')->middleware('auth:api');
    Route::get('refresh', 'refresh')->middleware('auth:api');
});

// Email verification
Route::controller(EmailVerifyController::class)->group(function () {
    Route::get('/email/verify/notice', 'notice')->name('verification.notice')->middleware('auth:api');

    Route::post('email/verify', 'verify')
        ->name('verification.verify');

    Route::post('email/send', 'send')
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// reset password
Route::controller(ResetPassController::class)->group(function () {
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'reset');
});
