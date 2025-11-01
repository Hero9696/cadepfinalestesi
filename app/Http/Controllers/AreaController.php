// app/Http/Controllers/AreaController.php

<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Muestra una lista de áreas.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $areas = Area::with('state')->get();
        return response()->json($areas);
    }

    /**
     * Almacena una nueva área.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_area' => 'required|string|max:30|unique:areas,name_area',
            'description_area' => 'required|string',
            'duration_area' => 'nullable|date_format:H:i:s|default:00:30:00',
            'id_state_area' => 'required|integer|exists:states,id_state',
            'idupdater_user_area' => 'required|integer|exists:users,id_user',
        ]);

        $area = Area::create([
            'name_area' => $request->name_area,
            'description_area' => $request->description_area,
            'duration_area' => $request->duration_area,
            'id_state_area' => $request->id_state_area,
            'idcreate_user_area' => $request->idupdater_user_area,
            'idupdater_user_area' => $request->idupdater_user_area,
        ]);

        return response()->json($area->load('state'), 201);
    }

    /**
     * Muestra un área específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $area = Area::with(['state', 'creator', 'updater'])->find($id);

        if (!$area) {
            return response()->json(['message' => 'Area not found'], 404);
        }

        return response()->json($area);
    }

    /**
     * Actualiza un área específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $area = Area::find($id);

        if (!$area) {
            return response()->json(['message' => 'Area not found'], 404);
        }

        $request->validate([
            'name_area' => 'sometimes|required|string|max:30|unique:areas,name_area,' . $id . ',id_area',
            'description_area' => 'sometimes|required|string',
            'duration_area' => 'nullable|date_format:H:i:s',
            'id_state_area' => 'sometimes|required|integer|exists:states,id_state',
            'idupdater_user_area' => 'required|integer|exists:users,id_user',
        ]);

        $area->update($request->all());

        return response()->json($area->load('state'));
    }

    /**
     * Elimina un área específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $area = Area::find($id);

        if (!$area) {
            return response()->json(['message' => 'Area not found'], 404);
        }

        $area->delete();

        return response()->json(['message' => 'Area deleted successfully'], 204);
    }
}
