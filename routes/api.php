<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PodcastsController;
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

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('/create', [AdminController::class, 'admin_create']);
        Route::post('/login', [AdminController::class, 'admin_login']);
    });
    Route::prefix('podcast')->group(function () {
        Route::group(['middleware' => ['AdminMiddleware']], function () {
            Route::post('/create', [PodcastsController::class, 'podcast_create']);
            Route::post('/edit', [PodcastsController::class, 'podcast_edit']);
            Route::post('/delete', [PodcastsController::class, 'podcast_delete']);
            Route::get('/list', [PodcastsController::class, 'list_podcasts']);
        });
    });
});
