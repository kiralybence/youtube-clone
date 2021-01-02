<?php

use Illuminate\Http\Request;
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

// Comments
Route::resource('video.comments', App\Http\Controllers\Api\CommentController::class)->only(['index', 'store']);
Route::post('/comments/{comment}/rate', [App\Http\Controllers\Api\CommentController::class, 'rate']);

// Video ratings
Route::get('/video/{video}', [App\Http\Controllers\Api\VideoController::class, 'show']);
Route::post('/video/{video}/rate', [App\Http\Controllers\Api\VideoController::class, 'rate']);

// Subscriptions
Route::get('/users/{user}/substatus', [App\Http\Controllers\Api\UserController::class, 'subStatus']);
Route::post('/users/{user}/subscribe', [App\Http\Controllers\Api\UserController::class, 'subscribe']);
Route::post('/users/{user}/unsubscribe', [App\Http\Controllers\Api\UserController::class, 'unsubscribe']);
