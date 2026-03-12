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
use App\Http\Controllers\Empleado\AuthController as EmpleadoAuthController;
use App\Http\Controllers\Empleado\DashboardController as EmpleadoDashboardController;
use App\Http\Controllers\Empleado\FichajeController as EmpleadoFichajeController;
use App\Http\Controllers\Empleado\AusenciaController as EmpleadoAusenciaController;

// Public - Fichaje by PIN
Route::get('/', [FichajeController::class, 'index'])->name('fichaje.index');
Route::post('/fichaje', [FichajeController::class, 'store'])->name('fichaje.store');

// Empleado auth
Route::get('/empleado/login', [EmpleadoAuthController::class, 'showLogin'])->name('empleado.login');
Route::post('/empleado/login', [EmpleadoAuthController::class, 'login'])->name('empleado.login.post');
Route::post('/empleado/logout', [EmpleadoAuthController::class, 'logout'])->name('empleado.logout');

// Empleado protected routes
Route::prefix('empleado')->middleware('empleado')->name('empleado.')->group(function () {
    Route::get('/', [EmpleadoDashboardController::class, 'index'])->name('dashboard');
    Route::get('fichajes', [EmpleadoFichajeController::class, 'index'])->name('fichajes.index');
    Route::get('ausencias', [EmpleadoAusenciaController::class, 'index'])->name('ausencias.index');
    Route::get('ausencias/create', [EmpleadoAusenciaController::class, 'create'])->name('ausencias.create');
    Route::post('ausencias', [EmpleadoAusenciaController::class, 'store'])->name('ausencias.store');
});

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
    Route::get('informes/pdf', [InformeController::class, 'pdf'])->name('informes.pdf');

    // Log de actividad (solo lectura)
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Ausencias
    Route::resource('ausencias', AusenciaController::class);
    Route::post('ausencias/{ausencia}/aprobar', [AusenciaController::class, 'aprobar'])->name('ausencias.aprobar');
    Route::post('ausencias/{ausencia}/rechazar', [AusenciaController::class, 'rechazar'])->name('ausencias.rechazar');
});
