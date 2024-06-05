<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('/user', function (Request $request) {
//     return $request->user();
// })->middleware(Authenticate::using('sanctum'));

Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');
//posts
Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);
Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');

Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');

/**
 * route "/user"
 * @method "GET"
 */
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);