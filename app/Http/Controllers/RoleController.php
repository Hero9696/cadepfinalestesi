<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Muestra una lista de roles.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::with(['state', 'creator', 'updater'])->get();
        return response()->json($roles);
    }

    /**
     * Almacena un nuevo rol.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_role' => 'required|string|max:15|unique:roles,name_role',
            'description_role' => 'required|string',
            'id_state_role' => 'required|integer|exists:states,id_state',
            // Asume que el usuario creador/actualizador es el usuario autenticado
            'idupdater_user_role' => 'required|integer|exists:users,id_user',
        ]);

        $role = Role::create([
            'name_role' => $request->name_role,
            'description_role' => $request->description_role,
            'id_state_role' => $request->id_state_role,
            'idcreate_user_role' => $request->idupdater_user_role, // Se usa el mismo para la creación inicial
            'idupdater_user_role' => $request->idupdater_user_role,
        ]);

        return response()->json($role, 201);
    }

    /**
     * Muestra un rol específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $role = Role::with(['state', 'creator', 'updater'])->find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json($role);
    }

    /**
     * Actualiza un rol específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $request->validate([
            'name_role' => 'sometimes|required|string|max:15|unique:roles,name_role,' . $id . ',id_role',
            'description_role' => 'sometimes|required|string',
            'id_state_role' => 'sometimes|required|integer|exists:states,id_state',
            'idupdater_user_role' => 'required|integer|exists:users,id_user', // El usuario que realiza la actualización
        ]);

        $role->update($request->all());

        return response()->json($role);
    }

    /**
     * Elimina un rol específico (Usar con precaución en producción).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 204);
    }
}
