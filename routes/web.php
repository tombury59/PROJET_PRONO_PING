<?php

use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\MatchResultController;
use App\Http\Controllers\Admin\PhaseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('phases', PhaseController::class)->except('show');

    Route::resource('matches', MatchController::class)
        ->except('show')
        ->parameters(['matches' => 'match']);

    Route::put('matches/{match}/resultat', [MatchResultController::class, 'update'])
        ->name('matches.resultat.update');
});

require __DIR__.'/auth.php';
