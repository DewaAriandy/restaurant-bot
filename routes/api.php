<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// AUTH
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/verify', [UserController::class, 'verifyOtp']);
    Route::post('/register', [UserController::class, 'register']);
});
Route::group(['middleware' => 'jwt.verify', 'prefix' => 'auth'], function ($router) {
    Route::post('/logout', [UserController::class, 'logout']);
});