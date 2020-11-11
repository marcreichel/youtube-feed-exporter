<?php

use App\Http\Controllers\YouTubeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [YouTubeController::class, 'index']);

Route::get('oauth/youtube/redirect', [YouTubeController::class, 'redirectToProvider']);
Route::get('oauth/youtube/handle', [YouTubeController::class, 'handleProviderCallback']);
