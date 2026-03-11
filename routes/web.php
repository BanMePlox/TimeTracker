<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FichajeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AusenciaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FichajeController as AdminFichajeController;
use App\Http\Controllers\Admin\InformeController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PresenciaController;
use App\Http\Controllers\Admin\UserController;

// Public - Fichaje by PIN
Route::get('/', [FichajeController::class, 'index'])->name('fichaje.index');
Route::post('/fichaje', [FichajeController::class, 'store'])->name('fichaje.store');

// Admin auth
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin protected routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/regenerar-pin', [UserController::class, 'regenerarPin'])->name('users.regenerar-pin');

    // Fichajes
    Route::get('fichajes', [AdminFichajeController::class, 'index'])->name('fichajes.index');
    Route::get('fichajes/export', [AdminFichajeController::class, 'export'])->name('fichajes.export');
    Route::get('fichajes/{fichaje}/edit', [AdminFichajeController::class, 'edit'])->name('fichajes.edit');
    Route::put('fichajes/{fichaje}', [AdminFichajeController::class, 'update'])->name('fichajes.update');
    Route::delete('fichajes/{fichaje}', [AdminFichajeController::class, 'destroy'])->name('fichajes.destroy');

    // Presencia
    Route::get('presencia', [PresenciaController::class, 'index'])->name('presencia.index');

    // Informes
    Route::get('informes', [InformeController::class, 'index'])->name('informes.index');
    Route::get('informes/export', [InformeController::class, 'export'])->name('informes.export');

    // Log de actividad (solo lectura)
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Ausencias
    Route::resource('ausencias', AusenciaController::class);
    Route::post('ausencias/{ausencia}/aprobar', [AusenciaController::class, 'aprobar'])->name('ausencias.aprobar');
    Route::post('ausencias/{ausencia}/rechazar', [AusenciaController::class, 'rechazar'])->name('ausencias.rechazar');
});
