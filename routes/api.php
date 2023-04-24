<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalController;

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

//had to comment out ->prefix('api) from RouteServiceProvider class
Route::prefix('/')->controller(SignalController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::get('/unregister', 'unregister')->name('unregister');
    Route::get('version', 'version')->name('version');
    Route::get('verify/{code}', 'verify')->name('verify');
    Route::get('receive', 'receive')->name('receive');
    Route::post('sendmessage', 'sendmessage')->name('sendmessage');
    // Route::get('/', 'index')->name('showArticles');
    // Route::get('/{id}', 'show')->name('singleArticle');
    // Route::post('/{id}/comment', 'comment')->name('addComment');
    // Route::get('/{id}/like', 'like')->name('addLike');
    // Route::get('/{id}/view', 'view')->name('addView');
});
