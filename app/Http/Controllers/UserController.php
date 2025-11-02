<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * GET: Lista todos los usuarios con sus relaciones.
     */
    public function index()
    {
        $users = User::with([
            'role',
            'state',
            'creator:id,name',
            'updater:id,name'
        ])->get();

        return response()->json($users);
    }

    /**
     * POST: Crear usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:15|unique:users,name',
            'email'           => 'nullable|email|unique:users,email',
            'password'        => 'required|string|min:8|max:60',
            'id_role_user'    => 'required|integer|exists:roles,id_role',
            'id_state_user'   => 'required|integer|exists:states,id_state',
            'idupdater_user_user' => 'required|integer|exists:users,id', // quien creó y actualiza
        ]);

        $newUser = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'id_role_user'       => $request->id_role_user,
            'id_state_user'      => $request->id_state_user,
            'idcreate_user_user' => $request->idupdater_user_user,
            'idupdater_user_user'=> $request->idupdater_user_user,
        ]);

        return response()->json(
            $newUser->load(['role', 'state']),
            201
        );
    }

    /**
     * GET: Mostrar un usuario por ID
     */
    public function show($id)
    {
        $user = User::with([
            'role',
            'state',
            'creator:id,name',
            'updater:id,name'
        ])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * PUT: Actualizar un usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $request->validate([
            'name' => [
                'sometimes', 'required', 'string', 'max:15',
                Rule::unique('users')->ignore($id)
            ],
            'email' => [
                'nullable', 'email',
                Rule::unique('users')->ignore($id)
            ],
            'password'        => 'nullable|string|min:8|max:60',
            'id_role_user'    => 'sometimes|required|integer|exists:roles,id_role',
            'id_state_user'   => 'sometimes|required|integer|exists:states,id_state',
            'idupdater_user_user' => 'required|integer|exists:users,id'
        ]);

        $data = $request->only([
            'name',
            'email',
            'id_role_user',
            'id_state_user',
            'idupdater_user_user'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user->load(['role', 'state']));
    }

    /**
     * PUT: Desactivar usuario (estado = 2 por ejemplo)
     */
   public function deactivate(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // 1. Validar tanto el ID del actualizador como el nuevo estado
    $request->validate([
        'id_state_user'       => 'required|integer|exists:states,id_state', // Nuevo estado (1 o 2)
        'idupdater_user_user' => 'required|integer|exists:users,id'
    ]);

    // Determinar el mensaje para la respuesta
    $newStateId = $request->id_state_user;
    $messageAction = ($newStateId == 1) ? 'activated' : 'deactivated';

    // 2. Aplicar la actualización con el estado deseado
    $user->update([
        'id_state_user'       => $newStateId, // Lee el nuevo estado del request
        'idupdater_user_user' => $request->idupdater_user_user
    ]);

    return response()->json(['message' => "User {$messageAction} successfully"]);
}
}
