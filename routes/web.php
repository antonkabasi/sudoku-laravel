<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SudokuController;
use App\Http\Controllers\LeaderboardController;

// Default Home Route (redirect to Sudoku)
Route::get('/', function () {
    return redirect('/sudoku');
})->name('home');

// Main Sudoku Game Routes
Route::get('/sudoku', [SudokuController::class, 'index'])->name('home');
Route::post('/sudoku/check', [SudokuController::class, 'check'])->name('check');
Route::post('/sudoku/solve', [SudokuController::class, 'solve'])->name('solve');
Route::post('/sudoku/validate', [SudokuController::class, 'validateSudoku'])->name('validate');
Route::get('/sudoku/stopwatch', [SudokuController::class, 'stopwatchTime'])->name('stopwatch');

Route::resource('leaderboard', LeaderboardController::class);
