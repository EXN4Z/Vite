<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DataModelController;
use App\Http\Controllers\DataRecordController;
use App\Http\Controllers\PageController;

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

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus.index');
    Route::post('/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');
    Route::post('/menus/reorder', [MenuController::class, 'reorder'])->name('admin.menus.reorder');

    Route::get('/data-models', [DataModelController::class, 'index'])->name('admin.data-models.index');
    Route::get('/data-models/create', [DataModelController::class, 'create'])->name('admin.data-models.create');
    Route::post('/data-models', [DataModelController::class, 'store'])->name('admin.data-models.store');
    Route::delete('/data-models/{dataModel}', [DataModelController::class, 'destroy'])->name('admin.data-models.destroy');

    Route::get('/data-models/{dataModel}/records', [DataRecordController::class, 'index'])->name('admin.data-records.index');
    Route::post('/data-models/{dataModel}/records', [DataRecordController::class, 'store'])->name('admin.data-records.store');
    Route::delete('/data-models/{dataModel}/records/{record}', [DataRecordController::class, 'destroy'])->name('admin.data-records.destroy');

    Route::get('/pages/{page}', [PageController::class, 'show'])->name('admin.pages.show');
});

require __DIR__.'/auth.php';