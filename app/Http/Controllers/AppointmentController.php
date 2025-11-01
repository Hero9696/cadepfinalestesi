<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Muestra una lista de citas.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $appointments = Appointment::with(['employee.user', 'state'])->get();
        return response()->json($appointments);
    }

    /**
     * Almacena una nueva cita (la estructura de la cita se maneja en StructureAppointmentController).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_employee_appointment' => 'required|integer|exists:employees,id_employee',
            'date_appointment' => 'nullable|string|max:100', // El campo es VARCHAR(100)
            'id_state_appointment' => 'required|integer|exists:states,id_state',
            'idupdater_user_appointment' => 'required|integer|exists:users,id_user',
        ]);

        $appointment = Appointment::create([
            ...$request->except(['idcreate_user_appointment']),
            'idcreate_user_appointment' => $request->idupdater_user_appointment,
        ]);

        return response()->json($appointment->load(['employee', 'state']), 201);
    }

    /**
     * Muestra una cita específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $appointment = Appointment::with(['employee.user', 'state', 'report', 'structure'])->find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json($appointment);
    }

    /**
     * Actualiza una cita específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $request->validate([
            'id_employee_appointment' => 'sometimes|required|integer|exists:employees,id_employee',
            'date_appointment' => 'nullable|string|max:100',
            'id_state_appointment' => 'sometimes|required|integer|exists:states,id_state',
            'idupdater_user_appointment' => 'required|integer|exists:users,id_user',
        ]);

        $appointment->update($request->all());

        return response()->json($appointment);
    }

    /**
     * Elimina una cita específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully'], 204);
    }
}
