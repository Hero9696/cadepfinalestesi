// app/Http/Controllers/BranchController.php

<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Muestra una lista de sucursales con sus relaciones.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $branches = Branch::with(['municipality', 'department', 'state'])->get();
        return response()->json($branches);
    }

    /**
     * Almacena una nueva sucursal.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_branch' => 'required|string|max:50',
            'phone_branch' => 'required|string|max:25',
            'id_municipality_branch' => 'required|integer|exists:municipalities,id_municipality',
            'id_department_branch' => 'required|integer|exists:departments,id_department',
            'id_state_branch' => 'required|integer|exists:states,id_state',
            'idupdater_user_branch' => 'required|integer|exists:users,id_user',
        ]);

        $branch = Branch::create([
            'name_branch' => $request->name_branch,
            'phone_branch' => $request->phone_branch,
            'id_municipality_branch' => $request->id_municipality_branch,
            'id_department_branch' => $request->id_department_branch,
            'id_state_branch' => $request->id_state_branch,
            'idcreate_user_branch' => $request->idupdater_user_branch,
            'idupdater_user_branch' => $request->idupdater_user_branch,
        ]);

        return response()->json($branch->load(['municipality', 'department', 'state']), 201);
    }

    /**
     * Muestra una sucursal específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $branch = Branch::with(['municipality', 'department', 'state', 'creator', 'updater'])->find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        return response()->json($branch);
    }

    /**
     * Actualiza una sucursal específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $request->validate([
            'name_branch' => 'sometimes|required|string|max:50',
            'phone_branch' => 'sometimes|required|string|max:25',
            'id_municipality_branch' => 'sometimes|required|integer|exists:municipalities,id_municipality',
            'id_department_branch' => 'sometimes|required|integer|exists:departments,id_department',
            'id_state_branch' => 'sometimes|required|integer|exists:states,id_state',
            'idupdater_user_branch' => 'required|integer|exists:users,id_user',
        ]);

        $branch->update($request->all());

        return response()->json($branch->load(['municipality', 'department', 'state']));
    }

    /**
     * Elimina una sucursal específica (Usar con precaución).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully'], 204);
    }
}
