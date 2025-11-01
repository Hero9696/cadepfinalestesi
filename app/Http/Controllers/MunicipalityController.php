// app/Http/Controllers/MunicipalityController.php

<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    /**
     * Muestra una lista de municipios, incluyendo su departamento.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $municipalities = Municipality::with('department')->get();
        return response()->json($municipalities);
    }

    /**
     * Almacena un nuevo municipio.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_municipality' => 'required|integer|unique:municipalities,id_municipality',
            'id_department_municipality' => 'required|integer|exists:departments,id_department',
            'name_municipality' => 'nullable|string|max:50',
        ]);

        $municipality = Municipality::create($request->all());

        return response()->json($municipality, 201);
    }

    /**
     * Muestra un municipio específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $municipality = Municipality::with('department')->find($id);

        if (!$municipality) {
            return response()->json(['message' => 'Municipality not found'], 404);
        }

        return response()->json($municipality);
    }

    /**
     * Actualiza un municipio específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $municipality = Municipality::find($id);

        if (!$municipality) {
            return response()->json(['message' => 'Municipality not found'], 404);
        }

        $request->validate([
            'id_municipality' => 'sometimes|integer|in:' . $id,
            'id_department_municipality' => 'sometimes|integer|exists:departments,id_department',
            'name_municipality' => 'nullable|string|max:50',
        ]);

        $municipality->update($request->all());

        return response()->json($municipality);
    }

    /**
     * Elimina un municipio específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $municipality = Municipality::find($id);

        if (!$municipality) {
            return response()->json(['message' => 'Municipality not found'], 404);
        }

        $municipality->delete();

        return response()->json(['message' => 'Municipality deleted successfully'], 204);
    }
}
