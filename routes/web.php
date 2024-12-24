<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarvelController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return inertia('Home');
    })->name('home');

    Route::get('/quadrinho', function () {
        return inertia('Quadrinho');
    })->name('quadrinho');

    Route::get('/heroi', function () {
        return inertia('Heroi');
    })->name('heroi');
});

Route::get('/user', function () {
    return Auth::user();
})->middleware('auth');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/api/marvel/comics', [MarvelController::class, 'getComics']);
Route::get('/api/marvel/characters', [MarvelController::class, 'getCharacters']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);
});
