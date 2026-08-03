<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MenuController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->group(function () {
    Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus.index');
    Route::post('/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');
    Route::post('/menus/reorder', [MenuController::class, 'reorder'])->name('admin.menus.reorder');
});

require __DIR__.'/auth.php';
