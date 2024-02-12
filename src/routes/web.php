<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth')->group(function(){
    Route::get('/', [StampController::class, 'index']);
    Route::get('/attendance', [TimeController::class, 'index']);
    Route::get('/mypage', [MyPageController::class, 'index']);
    Route::get('/mypage/two_factor_setting', [MyPageController::class, 'tfa']);
});

// 勤務時間の処理
Route::post('/', [StampController::class, 'startWork']);
Route::patch('/', [StampController::class, 'finishWork']);

//休憩時間の処理
Route::post('/rest', [StampController::class, 'startRest']);
Route::patch('/rest', [StampController::class, 'finishRest']);

//日付別勤怠ページ
Route::post('/attendance/next', [TimeController::class, 'next']);
Route::post('/attendance/prev', [TimeController::class, 'prev']);

//ユーザー一覧ページ
Route::get('/users', [UsersController::class, 'index']);


