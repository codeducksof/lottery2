<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LotteryController;

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

Route::get('/numbers', [LotteryController::class, 'index']);


// ////////////////////////////////////////////////////////////
// Route::get('/lottory-game', 'App\Http\Controllers\LotteryController@getLottoryGame');
// Route::get('/getPreLottory', 'App\Http\Controllers\getPreLottory');
// Route::post('/url_api_happybot', 'App\Http\Controllers\testUsers');
// Route::post('/sendlotto', 'App\Http\Controllers\sendLotto');

Route::post('/getAllData', [LotteryController::class, 'getAllData']);

// Route::post('/getUserHistroy', 'App\Http\Controllers\getUserHistroy');
// Route::post('/getPersonalStatistic', 'App\Http\Controllers\getPersonalStatistic');
// Route::post('/chk_answer', 'App\Http\Controllers\chk_answer');
// Route::post('/saveWinComment', 'App\Http\Controllers\saveWinComment');
// Route::post('/showWinComment', 'App\Http\Controllers\showWinComment');


