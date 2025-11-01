// app/Http/Controllers/StructureAppointmentController.php

<?php

namespace App\Http\Controllers;

use App\Models\StructureAppointment;
use Illuminate\Http\Request;

class StructureAppointmentController extends Controller
{
    /**
     * Muestra una lista de estructuras de citas.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $structures = StructureAppointment::with(['appointment', 'user', 'patient', 'schedule', 'week'])->get();
        return response()->json($structures);
    }

    /**
     * Almacena una nueva estructura de cita (Asigna una cita).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_appointment_structureappointment' => 'required|integer|exists:appointments,id_appointment|unique:structureappointments,id_appointment_structureappointment', // 1:1 con Appointment
            'id_user_structureappointment' => 'required|integer|exists:users,id_user',
            'id_patient_structureappointment' => 'required|integer|exists:patients,id_patient',
            'id_schedule_structureappointment' => 'required|integer|exists:schedules,id_schedule',
            'id_week_structureappointment' => 'required|integer|exists:weeks,id_week',
            'active_structureappointment' => 'boolean',
            'idupdater_user_structureappointment' => 'required|integer|exists:users,id_user',
        ]);

        $structure = StructureAppointment::create([
            ...$request->except(['idcreate_user_structureappointment']),
            'idcreate_user_structureappointment' => $request->idupdater_user_structureappointment,
            'active_structureappointment' => $request->active_structureappointment ?? 1,
        ]);

        return response()->json($structure, 201);
    }

    /**
     * Muestra una estructura de cita específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $structure = StructureAppointment::with(['appointment', 'user', 'patient', 'schedule', 'week'])->find($id);

        if (!$structure) {
            return response()->json(['message' => 'Appointment Structure not found'], 404);
        }

        return response()->json($structure);
    }

    /**
     * Actualiza una estructura de cita específica (ej. para reasignación).
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $structure = StructureAppointment::find($id);

        if (!$structure) {
            return response()->json(['message' => 'Appointment Structure not found'], 404);
        }

        $request->validate([
            'id_user_structureappointment' => 'sometimes|required|integer|exists:users,id_user',
            'id_patient_structureappointment' => 'sometimes|required|integer|exists:patients,id_patient',
            'id_schedule_structureappointment' => 'sometimes|required|integer|exists:schedules,id_schedule',
            'id_week_structureappointment' => 'sometimes|required|integer|exists:weeks,id_week',
            'active_structureappointment' => 'sometimes|boolean',
            'idupdater_user_structureappointment' => 'required|integer|exists:users,id_user',
        ]);

        $structure->update($request->all());

        return response()->json($structure);
    }

    /**
     * Elimina una estructura de cita específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $structure = StructureAppointment::find($id);

        if (!$structure) {
            return response()->json(['message' => 'Appointment Structure not found'], 404);
        }

        $structure->delete();

        return response()->json(['message' => 'Appointment Structure deleted successfully'], 204);
    }
}
