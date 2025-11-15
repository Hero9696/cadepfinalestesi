<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (ACCESO LIBRE)
|--------------------------------------------------------------------------
| Estas rutas son necesarias para cargar los selectores en el formulario de Registro.
*/

// GET /api/states: Cargar estados (Necesario para el registro inicial del usuario).
Route::get('/states', [StateController::class, 'index'])->withoutMiddleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (REQUIEREN AUTH:SANCTUM)
|--------------------------------------------------------------------------
| CRUD de Usuarios y Roles solo para usuarios logueados.
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. RUTAS DE ROLES (GET /api/roles)
    // Se protege el GET para que solo los administradores logueados puedan ver la lista.
    Route::get('/roles', [RoleController::class, 'index']); // GET index
    Route::apiResource('roles', RoleController::class)->only(['store', 'update', 'destroy', 'show']);

    // 2. RUTAS DE USUARIOS (CRUD COMPLETO)
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store', 'update']);
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate']);
});


/*
|--------------------------------------------------------------------------
| NOTA: Si necesitas que /api/roles sea público para el registro (para llenar el select),
| usa el código que te di anteriormente. Si lo usas dentro de una página autenticada, 
| esta configuración es la correcta.
|--------------------------------------------------------------------------
*/