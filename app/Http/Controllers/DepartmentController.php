// app/Http/Controllers/DepartmentController.php

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Muestra una lista de departamentos.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $departments = Department::all();
        return response()->json($departments);
    }

    /**
     * Almacena un nuevo departamento.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_department' => 'required|integer|unique:departments,id_department',
            'name_department' => 'nullable|string|max:40',
        ]);

        $department = Department::create($request->all());

        return response()->json($department, 201);
    }

    /**
     * Muestra un departamento específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json($department);
    }

    /**
     * Actualiza un departamento específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        // Valida que el ID no se intente cambiar o si se cambia, que sea el mismo ID actual.
        $request->validate([
            'id_department' => 'sometimes|integer|in:' . $id,
            'name_department' => 'nullable|string|max:40',
        ]);

        $department->update($request->all());

        return response()->json($department);
    }

    /**
     * Elimina un departamento específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $department->delete();

        return response()->json(['message' => 'Department deleted successfully'], 204);
    }
}
