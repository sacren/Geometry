<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('posts', PostController::class)
    ->only(['index', 'store', 'destroy'])
    ->middleware(['auth', 'verified']);

// 👇 LIKE/UNLIKE ROUTES — nested, semantic, and secured
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/posts/{post}/likes', [LikeController::class, 'store'])
        ->middleware('throttle:likes')
        ->name('posts.like');
    Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy'])
        ->middleware('throttle:likes')
        ->name('posts.unlike');
});

require __DIR__.'/settings.php';
