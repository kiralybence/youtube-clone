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

Route::resource('video.comments', App\Http\Controllers\Api\CommentController::class)->only(['index', 'store']);
Route::post('/comments/{comment}/rate', [App\Http\Controllers\Api\CommentController::class, 'rate']);
