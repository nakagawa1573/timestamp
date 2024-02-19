<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [StampController::class, 'index']);
    Route::get('/attendance', [TimeController::class, 'index']);
    Route::get('/mypage', [MyPageController::class, 'index']);
    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::get('/attendance/user', [TimeController::class, 'user'])->name('attendance.user');

    // 勤務時間の処理
    Route::post('/', [StampController::class, 'startWork']);
    Route::patch('/', [StampController::class, 'finishWork']);

    //休憩時間の処理
    Route::post('/rest', [StampController::class, 'startRest']);
    Route::patch('/rest', [StampController::class, 'finishRest']);

    //日付別勤怠ページ
    Route::post('/attendance/next', [TimeController::class, 'next']);
    Route::post('/attendance/prev', [TimeController::class, 'prev']);

    //個人勤怠表の処理
    Route::post('/attendance/user-next', [TimeController::class, 'nextForUser']);
    Route::post('/attendance/user-prev', [TimeController::class, 'prevForUser']);
});

//メール確認用ルート
Route::get('/verify', [VerifyEmailController::class, 'index'])->middleware('auth')->name('verification.notice');
Route::get('/verify/{id}/{hash}', [VerifyEmailController::class, 'confirm'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [VerifyEmailController::class, 'send'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

