// app/Http/Controllers/WeekController.php

<?php

namespace App\Http\Controllers;

use App\Models\Week;
use Illuminate\Http\Request;

class WeekController extends Controller
{
    /**
     * Muestra una lista de días de la semana.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Ordenar por ID es útil si los IDs están en el orden cronológico de los días (1=Domingo, 2=Lunes, etc.)
        $weeks = Week::orderBy('id_week', 'asc')->get();
        return response()->json($weeks);
    }

    /**
     * Almacena un nuevo día de la semana (generalmente solo se usa para la siembra inicial).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_week' => 'required|string|max:20|unique:weeks,name_week',
        ]);

        $week = Week::create($request->all());

        return response()->json($week, 201);
    }

    /**
     * Muestra un día de la semana específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $week = Week::find($id);

        if (!$week) {
            return response()->json(['message' => 'Week day not found'], 404);
        }

        return response()->json($week);
    }

    /**
     * Actualiza un día de la semana específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $week = Week::find($id);

        if (!$week) {
            return response()->json(['message' => 'Week day not found'], 404);
        }

        $request->validate([
            'name_week' => 'sometimes|required|string|max:20|unique:weeks,name_week,' . $id . ',id_week',
        ]);

        $week->update($request->all());

        return response()->json($week);
    }

    /**
     * Elimina un día de la semana (Usar con extrema precaución, ya que es fundamental para el agendamiento).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $week = Week::find($id);

        if (!$week) {
            return response()->json(['message' => 'Week day not found'], 404);
        }

        $week->delete();

        return response()->json(['message' => 'Week day deleted successfully'], 204);
    }
}
