<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\State; // <-- AÑADIDO: Necesario para los formularios
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // <-- AÑADIDO: Para validación unique en update
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Muestra una lista de roles (API).
     * Esta ruta la usas con axios para obtener los datos.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::with(['state', 'creator', 'updater'])->get();
        return response()->json($roles);
    }

    /**
     * Muestra la PÁGINA de índice de roles.
     * @return \Inertia\Response
     */
    public function indexPage()
    {
        // Esta es la conexión: le dice a Laravel que renderice
        // el archivo 'resources/js/pages/settings/RoleIndex.vue'
        return Inertia::render('settings/RoleIndex');
    }

    /**
     * Muestra la PÁGINA para crear un nuevo rol.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('settings/RoleForm', [
            // Pasamos los estados para el <select> del formulario
            'states' => State::all(['id_state as value', 'name_state as label'])
        ]);
    }

    /**
     * Almacena un nuevo rol.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_role' => 'required|string|max:15|unique:roles,name_role',
            'description_role' => 'required|string',
            'id_state_role' => 'required|integer|exists:states,id_state',
        ]);

        $userId = Auth::id(); // Obtenemos el ID del usuario autenticado

        Role::create([
            'name_role' => $request->name_role,
            'description_role' => $request->description_role,
            'id_state_role' => $request->id_state_role,
            'idcreate_user_role' => $userId, // Usamos el ID del usuario logueado
            'idupdater_user_role' => $userId, // Usamos el ID del usuario logueado
        ]);

        // Redirigimos de vuelta a la página de índice de roles
        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Muestra un rol específico (API).
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
     * Muestra la PÁGINA para editar un rol.
     * @param  \App\Models\Role  $role
     * @return \Inertia\Response
     */
    public function edit(Role $role) // <-- Usamos Route Model Binding
    {
        return Inertia::render('settings/RoleForm', [
            'role' => $role, // Pasamos el rol que se va a editar
            'states' => State::all(['id_state as value', 'name_state as label'])
        ]);
    }

    /**
     * Actualiza un rol específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role) // <-- Usamos Route Model Binding
    {
        $request->validate([
            'name_role' => [
                'sometimes', 'required', 'string', 'max:15',
                Rule::unique('roles')->ignore($role->id_role, 'id_role') // Validar unique ignorando el actual
            ],
            'description_role' => 'sometimes|required|string',
            'id_state_role' => 'sometimes|required|integer|exists:states,id_state',
        ]);

        // Preparamos los datos, asegurándonos de actualizar el 'updater'
        $data = $request->all();
        $data['idupdater_user_role'] = Auth::id(); // Actualizamos con el ID del usuario logueado

        $role->update($data);

        // Redirigimos de vuelta a la página de índice
        return redirect()->route('role.index')->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Elimina un rol específico.
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role) // <-- Usamos Route Model Binding
    {
        $role->delete();

        return redirect()->route('role.index')->with('success', 'Rol eliminado exitosamente.');
    }
}