<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarvelController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return inertia('Home');
    })->name('home');

    Route::get('/comics', function () {
        return inertia('Comics');
    })->name('comics');

    Route::get('/characters', function () {
        return inertia('Characters');
    })->name('characters');

    Route::get('/favorites', function () {
        return inertia('Favorites');
    })->name('favorites');

    Route::get('/user', function () {
        return inertia('User');
    })->name('user');
});

Route::get('/api/user', function () {
    return Auth::user();
})->middleware('auth');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/api/marvel/comics', [MarvelController::class, 'getComics']);
Route::get('/api/marvel/characters', [MarvelController::class, 'getCharacters']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/comics', [FavoriteController::class, 'storeComic']);
    Route::post('/favorites/characters', [FavoriteController::class, 'storeCharacter']);
    Route::delete('/api/favorites/{type}/{id}', [FavoriteController::class, 'destroy']);
    Route::put('/api/user', [UserController::class, 'update']);
    Route::delete('/api/user', [UserController::class, 'destroy']);
    Route::post('/api/user/verify-password', [UserController::class, 'verifyPassword']);
    Route::get('/api/user/favorites', [MarvelController::class, 'getUserFavorites']);
});