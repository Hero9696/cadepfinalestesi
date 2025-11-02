<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;

// PAGINA DE INICIO
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// DASHBOARD
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ✅ ✅ TODO LO QUE INERTIA NECESITA (rutas web autenticadas)
Route::middleware(['auth'])->group(function () {
    
    // --- RUTAS DE PÁGINA (Devuelven Inertia::render) ---
    // Estas eran las que te faltaban y causaban el error 'Page not found'

    // Muestra el formulario para crear un usuario
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create'); // <-- AÑADIDA

    // Muestra el formulario para editar un usuario existente
    // Usa Route Model Binding (User $user) en el controlador
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // <-- AÑADIDA


    // --- RUTAS DE API (Devuelven response()->json) ---
    // Estas son las que ya tenías y que probablemente usas con axios

    Route::get('/users-json', [UserController::class, 'index'])->name('users.json');
    Route::get('/users/{id}/show-json', [UserController::class, 'show'])->name('users.show.json');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    
    Route::get('/roles-json', [RoleController::class, 'index']);
    Route::get('/states-json', [StateController::class, 'index']);

    Route::get('/users', [UserController::class, 'indexPage'])->name('users.index');
  

    // ===============================================
// RUTAS PARA ROLES (CONTROLADOR INERTIA + API)
// ===============================================

// PÁGINAS (las que devuelven Inertia::render)
Route::get('/roles', [App\Http\Controllers\RoleController::class, 'indexPage'])->name('role.index');
Route::get('/roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('role.create');
Route::get('/roles/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');

// ACCIONES (las que redirigen de vuelta)
Route::post('/roles', [App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
Route::put('/roles/{role}', [App\Http\Controllers\RoleController::class, 'update'])->name('role.update');
Route::delete('/roles/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('role.destroy');

// API / JSON (las que usas con axios)
Route::get('/roles-json', [App\Http\Controllers\RoleController::class, 'index'])->name('role.json');
Route::get('/roles/{id}/show-json', [App\Http\Controllers\RoleController::class, 'show'])->name('role.show.json');

});

require __DIR__.'/settings.php';