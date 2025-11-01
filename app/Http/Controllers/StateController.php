// app/Http/Controllers/StateController.php

<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Muestra una lista de estados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $states = State::all();
        return response()->json($states);
    }

    /**
     * Almacena un nuevo estado.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_state' => 'required|string|max:50|unique:states,name_state',
        ]);

        $state = State::create($request->all());

        return response()->json($state, 201);
    }

    /**
     * Muestra un estado específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $state = State::find($id);

        if (!$state) {
            return response()->json(['message' => 'State not found'], 404);
        }

        return response()->json($state);
    }

    /**
     * Actualiza un estado específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $state = State::find($id);

        if (!$state) {
            return response()->json(['message' => 'State not found'], 404);
        }

        $request->validate([
            'name_state' => 'sometimes|required|string|max:50|unique:states,name_state,' . $id . ',id_state',
        ]);

        $state->update($request->all());

        return response()->json($state);
    }

    /**
     * Elimina un estado específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $state = State::find($id);

        if (!$state) {
            return response()->json(['message' => 'State not found'], 404);
        }

        // Recomendación: Considera usar soft-deletes o un cambio de estado en lugar de la eliminación física.
        $state->delete();

        return response()->json(['message' => 'State deleted successfully'], 204);
    }
}
