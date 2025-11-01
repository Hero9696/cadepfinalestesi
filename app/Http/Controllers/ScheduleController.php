// app/Http/Controllers/ScheduleController.php

<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Muestra una lista de horarios disponibles.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $schedules = Schedule::orderBy('hour_schedule', 'asc')->get();
        return response()->json($schedules);
    }

    /**
     * Almacena un nuevo horario.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'hour_schedule' => 'required|date_format:H:i:s|unique:schedules,hour_schedule',
            'idupdater_user_schedule' => 'required|integer|exists:users,id_user',
        ]);

        $schedule = Schedule::create([
            'hour_schedule' => $request->hour_schedule,
            'idcreate_user_schedule' => $request->idupdater_user_schedule,
            'idupdater_user_schedule' => $request->idupdater_user_schedule,
        ]);

        return response()->json($schedule, 201);
    }

    /**
     * Muestra un horario específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        return response()->json($schedule);
    }

    /**
     * Actualiza un horario específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $request->validate([
            'hour_schedule' => 'sometimes|required|date_format:H:i:s|unique:schedules,hour_schedule,' . $id . ',id_schedule',
            'idupdater_user_schedule' => 'required|integer|exists:users,id_user',
        ]);

        $schedule->update($request->all());

        return response()->json($schedule);
    }

    /**
     * Elimina un horario específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        // Se recomienda verificar si hay dependencias activas en structureappointments antes de eliminar.
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully'], 204);
    }
}
