<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Muestra una lista de usuarios con sus roles, estados y empleados asociados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with(['role', 'state', 'employee:id_employee,firstname_employee,lastname_employee,id_user_employee'])->get();
        return response()->json($users);
    }

    /**
     * Almacena un nuevo usuario.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:users,name',
            // Si has incluido 'email' en tu DB, agrega: 'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:60',
            'id_role_user' => 'required|integer|exists:roles,id_role',
            'id_state_user' => 'required|integer|exists:states,id_state',
            'idupdater_user_user' => 'required|integer|exists:users,id_user',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ?? null, // Usar null si no existe el campo en el request
            'password' => Hash::make($request->password),
            'id_role_user' => $request->id_role_user,
            'id_state_user' => $request->id_state_user,

            // Auditoría: El creador y el actualizador inicial son el mismo
            'idcreate_user_user' => $request->idupdater_user_user,
            'idupdater_user_user' => $request->idupdater_user_user,
        ]);

        return response()->json($user->load(['role', 'state']), 201);
    }

    /**
     * Muestra un usuario específico, incluyendo detalles del empleado y sus ubicaciones.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $user = User::with([
            'role',
            'state',
            'employee.area',
            'employee.branch',
            // Cargar el creador y el actualizador
            'creator:id_user,name',
            'updater:id_user,name'
        ])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Actualiza un usuario específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:15', Rule::unique('users', 'name')->ignore($id, 'id_user')],
            'password' => 'nullable|string|min:8|max:60',
            'id_role_user' => 'sometimes|required|integer|exists:roles,id_role',
            'id_state_user' => 'sometimes|required|integer|exists:states,id_state',
            'idupdater_user_user' => 'required|integer|exists:users,id_user',
        ]);

        $data = $request->only(['name', 'id_role_user', 'id_state_user', 'idupdater_user_user']);

        // Actualización condicional de contraseña
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user->load(['role', 'state']));
    }

    /**
     * Desactiva un usuario específico (Mejora de la seguridad sobre la eliminación).
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(int $id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate(['idupdater_user_user' => 'required|integer|exists:users,id_user']);

        // Asumiendo que el estado con ID 2 es 'Inactivo' o 'Desactivado'
        $INACTIVE_STATE_ID = 2;

        $user->update([
            'id_state_user' => $INACTIVE_STATE_ID,
            'idupdater_user_user' => $request->idupdater_user_user
        ]);

        return response()->json(['message' => 'User deactivated successfully'], 200);
    }
}
