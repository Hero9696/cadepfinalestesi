<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
// --- ¡AÑADE ESTAS IMPORTACIONES! ---
use App\Models\Employee;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    /**
     * Muestra la página principal de citas.
     * @return \Inertia\Response
     */
    public function indexPage()
    {
        // Pasamos las citas como props a la página de Inertia
        $appointments = Appointment::with(['employee.user', 'state'])->get();
        
        return Inertia::render('settings/AppointmentIndex', [
            'appointments' => $appointments
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva cita.
     * @return \Inertia\Response
     */
    public function create()
    {
        // Pasamos las dependencias (empleados, estados) al formulario
        return Inertia::render('settings/AppointmentForm', [
            'employees' => Employee::with('user')->get(), // Asume que quieres mostrar el nombre de usuario del empleado
            'states' => State::all()
        ]);
    }

    /**
     * Almacena una nueva cita en la base de datos.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validamos solo los datos que vienen del formulario
        $validatedData = $request->validate([
            'id_employee_appointment' => 'required|integer|exists:employees,id_employee',
            'date_appointment' => 'nullable|string|max:100',
            'id_state_appointment' => 'required|integer|exists:states,id_state',
        ]);

        // Añadimos los datos de auditoría (el admin logueado)
        $dataToCreate = $validatedData;
        $dataToCreate['idcreate_user_appointment'] = Auth::id();
        $dataToCreate['idupdater_user_appointment'] = Auth::id();

        Appointment::create($dataToCreate);

        return redirect()->route('settings/AppointmentIndex')->with('success', 'Cita creada exitosamente.');
    }

    /**
     * Muestra una cita específica (página de detalles).
     * @param  \App\Models\Appointment  $appointment
     * @return \Inertia\Response
     */
    public function show(Appointment $appointment)
    {
        // Pasamos la cita específica (con todas sus relaciones) a la página de "Show"
       // return Inertia::render('Appointments/Show', [
         //   'appointment' => $appointment->load(['employee.user', 'state', 'report', 'structure'])
        //]);
    }

    /**
     * Muestra el formulario para editar una cita.
     * @param  \App\Models\Appointment  $appointment
     * @return \Inertia\Response
     */
    public function edit(Appointment $appointment)
    {
        // Pasamos la cita a editar Y las dependencias
        return Inertia::render('settings/AppointmentForm', [
            'appointment' => $appointment,
            'employees' => Employee::with('user')->get(),
            'states' => State::all()
        ]);
    }

    /**
     * Actualiza una cita específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Validamos los datos que vienen del formulario
        $validatedData = $request->validate([
            'id_employee_appointment' => 'sometimes|required|integer|exists:employees,id_employee',
            'date_appointment' => 'nullable|string|max:100',
            'id_state_appointment' => 'sometimes|required|integer|exists:states,id_state',
        ]);

        // Añadimos el ID del admin que está actualizando
        $dataToUpdate = $validatedData;
        $dataToUpdate['idupdater_user_appointment'] = Auth::id();

        $appointment->update($dataToUpdate);

        // Redirige de vuelta al formulario de edición
        return redirect()->back()->with('success', 'Cita actualizada.');
    }

    /**
     * Elimina una cita específica.
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('settings/AppointmentIndex')->with('success', 'Cita eliminada.');
    }
}