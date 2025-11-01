<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

        // --- NUEVAS RUTAS DE CONFIGURACIÓN ADMINISTRATIVA (Ej: /settings/admin/users) ---

    // Ruta principal para la configuración administrativa
    // Esta ruta cargará el componente Vue con las pestañas de administración (AdminConfigIndex)
    Route::get('settings/admin', function () {
        return Inertia::render('Settings/AdminConfigIndex');
    })->name('admin.config.index');

    // Rutas específicas para que Inertia pueda navegar directamente (Opcional, pero útil)
    Route::get('settings/admin/users', function () {
        return Inertia::render('Settings/AdminConfigIndex', ['activeTab' => 'users']);
    })->name('admin.users.index');

    Route::get('settings/admin/roles', function () {
        return Inertia::render('Settings/AdminConfigIndex', ['activeTab' => 'roles']);
    })->name('admin.roles.index');

    Route::get('settings/admin/states', function () {
        return Inertia::render('Settings/AdminConfigIndex', ['activeTab' => 'states']);
    })->name('admin.states.index');

    Route::get('settings/admin/departments', function () {
        return Inertia::render('Settings/AdminConfigIndex', ['activeTab' => 'departments']);
    })->name('admin.departments.index');

    // Puedes agregar una redirección fácil:
    Route::redirect('settings/admin', '/settings/admin/users');
});
