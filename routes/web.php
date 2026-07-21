<?php

use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\MatchResultController;
use App\Http\Controllers\Admin\PhaseController;
use App\Http\Controllers\Admin\QuestionBonusController as AdminQuestionBonusController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\ClassementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PronosticController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/pronostics', [PronosticController::class, 'index'])->name('pronostics.index');
    Route::post('/pronostics/{match}', [PronosticController::class, 'store'])->name('pronostics.store');

    Route::get('/classement', [ClassementController::class, 'index'])->name('classement.index');

    Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier.index');

    Route::get('/bonus', [BonusController::class, 'index'])->name('bonus.index');
    Route::post('/bonus/{question}', [BonusController::class, 'store'])->name('bonus.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/marquer-tout-lu', [NotificationController::class, 'marquerToutLu'])->name('notifications.marquer-tout-lu');
    Route::get('/notifications/{notification}/voir', [NotificationController::class, 'voir'])->name('notifications.voir');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('phases', PhaseController::class)->except('show');

    Route::resource('matches', MatchController::class)
        ->except('show')
        ->parameters(['matches' => 'match']);

    Route::put('matches/{match}/resultat', [MatchResultController::class, 'update'])
        ->name('matches.resultat.update');

    Route::resource('questions-bonus', AdminQuestionBonusController::class)
        ->except('show')
        ->parameters(['questions-bonus' => 'question']);

    Route::patch('questions-bonus/{question}/reponses/{reponse}', [AdminQuestionBonusController::class, 'accorderPoints'])
        ->name('questions-bonus.reponses.update');

    Route::get('utilisateurs', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('utilisateurs/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role.update');
    Route::delete('utilisateurs/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
