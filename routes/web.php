<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StreakController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StreakController::class, 'index'])->name('dashboard');
    Route::resource('streaks', StreakController::class)->only(['index', 'store', 'destroy']);
    Route::get('/streaks/{id}/calendar', [StreakController::class, 'calendar'])->name('streaks.calendar');
    Route::post('/streaks/{id}/done', [StreakController::class, 'markDone'])->name('streaks.done');
    Route::post('/streaks/{id}/skip', [StreakController::class, 'markSkipped'])->name('streaks.skip');
    Route::post('/streaks/mark-all-done', [StreakController::class, 'markAllDone'])->name('streaks.mark-all-done');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
