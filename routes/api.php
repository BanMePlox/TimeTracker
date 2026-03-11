<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FichajePublicoController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\FichajeController;
use App\Http\Controllers\Api\PresenciaController;
use App\Http\Controllers\Api\AusenciaController;
use App\Http\Controllers\Api\ActivityLogController;
use Illuminate\Support\Facades\Route;

// ── Pública: fichar por PIN ────────────────────────────────────────────────
Route::post('/fichaje', [FichajePublicoController::class, 'store']);

// ── Autenticación ──────────────────────────────────────────────────────────
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Usuarios
    Route::apiResource('usuarios', UsuarioController::class);

    // Fichajes (admin CRUD)
    Route::get('fichajes', [FichajeController::class, 'index']);
    Route::get('fichajes/{fichaje}', [FichajeController::class, 'show']);
    Route::put('fichajes/{fichaje}', [FichajeController::class, 'update']);
    Route::delete('fichajes/{fichaje}', [FichajeController::class, 'destroy']);

    // Presencia
    Route::get('presencia', [PresenciaController::class, 'index']);

    // Ausencias
    Route::apiResource('ausencias', AusenciaController::class);
    Route::post('ausencias/{ausencia}/aprobar', [AusenciaController::class, 'aprobar']);
    Route::post('ausencias/{ausencia}/rechazar', [AusenciaController::class, 'rechazar']);

    // Log de actividad (solo lectura)
    Route::get('activity-log', [ActivityLogController::class, 'index']);
});
