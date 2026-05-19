<?php

declare(strict_types=1);

use App\Http\Controllers\GraduationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 1. Full Resourceful CRUD routes for Parents (Graduations)
    Route::resource('graduations', GraduationController::class);

    // 2. Custom Actions for Sub-Resources (MUST come BEFORE the nested student resource line)
    Route::get(
        'graduations/{graduation}/students/export',
        [StudentController::class, 'export']
    )->name('graduations.students.export');

    Route::post(
        'graduations/{graduation}/students/import',
        [StudentController::class, 'import']
    )->name('graduations.students.import');

    Route::post(
        'graduations/{graduation}/students/bulk',
        [StudentController::class, 'bulk']
    )->name('graduations.students.bulk');

    // 3. Full Resourceful Nested CRUD routes for Students
    Route::resource('graduations.students', StudentController::class);

    // 4. Custom Inline Direct Action Route
    Route::patch(
        'graduations/{graduation}/students/{student}/verify',
        [StudentController::class, 'verify']
    )->name('graduations.students.verify');
});

require __DIR__ . '/auth.php';