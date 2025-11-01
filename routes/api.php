<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ******************************************************
// ** 1. IMPORTACIONES CORRECTAS **
// ******************************************************
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;


// ***************************************************************
// ** 2. RUTA DE DEBUGGING SIMPLE (TEMPORAL) **
// ***************************************************************
// Si esta ruta funciona, el problema está en apiResource o el controlador.
// Si NO funciona, el problema está en la configuración de API o el servidor.
Route::get('/test', function () {
    return 'Ruta de prueba API OK.';
})->withoutMiddleware('auth:sanctum');

// La ruta de roles la dejaremos temporalmente como una función de cierre
Route::get('/roles', [RoleController::class, 'index'])->withoutMiddleware('auth:sanctum');

// Las rutas API resource las comentamos para aislar el problema
// Route::apiResource('roles', RoleController::class); 


// ***************************************************************
// ** 3. RUTAS PROTEGIDAS CON AUTH:SANCTUM **
// ***************************************************************
Route::middleware('auth:sanctum')->group(function () {
    // Rutas para la gestión de Usuarios
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store', 'update']);
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate']);

    // Rutas de soporte (estados)
    Route::apiResource('states', StateController::class)->only(['index']);
});