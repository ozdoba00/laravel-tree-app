<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NodeController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::group([
    'middleware'=> ['api'],
    'prefix' => "node"
], function ($router){

    Route::post('/add', [NodeController::class, 'store']);
    Route::get('/get', [NodeController::class, 'index']);
    Route::get('/get/{id}', [NodeController::class, 'show']);
    Route::delete('/remove/{id}', [NodeController::class, 'destroy']);
    Route::put('/edit/{id}', [NodeController::class, 'update']);
    Route::put("/move/{id}", [NodeController::class, 'move']);

});
