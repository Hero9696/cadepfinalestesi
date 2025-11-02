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
    Route::get('/users-json', [UserController::class, 'index'])->name('users.json');
    Route::get('/users/{id}/show-json', [UserController::class, 'show'])->name('users.show.json');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
     Route::get('/roles-json', [RoleController::class, 'index']);
    Route::get('/states-json', [StateController::class, 'index']);
});

require __DIR__.'/settings.php';
