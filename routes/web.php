<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WeekController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RelativeController;
use App\Http\Controllers\DonorController;






/*
|--------------------------------------------------------------------------
| PÁGINA DE INICIO
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| RUTAS WEB AUTENTICADAS (INERTIA + API)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USUARIOS
    |--------------------------------------------------------------------------
    */

    // Páginas (Inertia)
    Route::get('/users', [UserController::class, 'indexPage'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    // API / JSON / Acciones
    Route::get('/users-json', [UserController::class, 'index'])->name('users.json');
    Route::get('/users/{id}/show-json', [UserController::class, 'show'])->name('users.show.json');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');


    /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */

    // Páginas (Inertia)
    Route::get('/roles', [RoleController::class, 'indexPage'])->name('role.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('role.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');

    // Acciones
    Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('role.destroy');

    // API / JSON
    Route::get('/roles-json', [RoleController::class, 'index'])->name('role.json');
    Route::get('/roles/{id}/show-json', [RoleController::class, 'show'])->name('role.show.json');


    /*
    |--------------------------------------------------------------------------
    | ESTADOS
    |--------------------------------------------------------------------------
    */

    Route::get('/states-json', [StateController::class, 'index'])->name('states.json');


    /*
    |--------------------------------------------------------------------------
    | CITAS (APPOINTMENTS)
    |--------------------------------------------------------------------------
    */

    // Páginas
    Route::get('/appointmentsindex', [AppointmentController::class, 'indexPage'])->name('appointments.index');
    Route::get('/appointments', [AppointmentController::class, 'create'])->name('appointments.create');

    // API
    Route::get('/appointments/events', [AppointmentController::class, 'getCalendarEvents'])->name('appointments.events');
    Route::post('/appointments', [AppointmentController::class, 'storeAppointment'])->name('appointments.store');
    Route::put('/appointments/{id}', [AppointmentController::class, 'updateAppointment'])->name('appointments.update');

    /*
    |--------------------------------------------------------------------------
    | DEPARTAMENTOS (DEPARTMENTS)
    |--------------------------------------------------------------------------
    */

   // 1. INDEX (GET): Muestra la lista de departamentos
    Route::get('/departments', [DepartmentController::class, 'indexPage'])->name('departments.index');

    // 2. CREAR (GET): Muestra el formulario vacío
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');

    // 3. GUARDAR (POST): Procesa la creación de un nuevo departamento
    Route::post('/departments/store', [DepartmentController::class, 'store'])->name('departments.store');

    // 4. EDITAR (GET): Muestra el formulario con los datos a editar
    Route::get('/departments/{id}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');

    // 5. ACTUALIZAR (PUT/PATCH): Procesa la actualización de un departamento existente
    Route::put('/departments/{id}/update', [DepartmentController::class, 'update'])->name('departments.update');

    // 6. ELIMINAR (DELETE): Procesa la eliminación de un departamento
    Route::delete('admin/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');


     /*
    |--------------------------------------------------------------------------
    | MUNICIPIOS (MUNICIPALITIES)
    |--------------------------------------------------------------------------
    */
    Route::resource('municipalities', MunicipalityController::class);


/*
    |--------------------------------------------------------------------------
    | SUCURSALES (BRANCHES)
    |--------------------------------------------------------------------------
    */

Route::resource('branches', BranchController::class)->names('branches');


/*
    |--------------------------------------------------------------------------
    | ÁREAS (AREAS)
    |--------------------------------------------------------------------------
    */

Route::resource('areas', AreaController::class)->names('areas');

});

/*
    |--------------------------------------------------------------------------
    | HORARIOS (SCHEDULES)
    |--------------------------------------------------------------------------
    */

Route::resource('schedules', ScheduleController::class)->names('schedules');

/*
    |--------------------------------------------------------------------------
    | SEMANAS (WEEKS)
    |--------------------------------------------------------------------------
    */

Route::resource('week', WeekController::class)->names('week');

/*
    |--------------------------------------------------------------------------
    | EMPLEADOS (EMPLOYEES)
    |--------------------------------------------------------------------------
    */

Route::resource('employees', EmployeeController::class)->names('employees');

/*
    |--------------------------------------------------------------------------
    | PACIENTES (PATIENTS)
    |--------------------------------------------------------------------------
    */


Route::resource('patients', PatientController::class)->names('patients');

/*
    |--------------------------------------------------------------------------
    | PARENTESCOS (RELATIVES)
    |--------------------------------------------------------------------------
    */

Route::resource('relatives', RelativeController::class)->names('relatives');

/*
    |--------------------------------------------------------------------------
    | DONANTES (DONORS)
    |--------------------------------------------------------------------------
    */

Route::resource('/donors', DonorController::class)->names('donors');

/* bf
|--------------------------------------------------------------------------
| CONFIGURACIONES EXTERNAS
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
