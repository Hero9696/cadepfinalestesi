<?php

namespace App\Http\Controllers;

// --- 1. AÑADE ESTAS IMPORTACIONES ---
use App\Models\Role; // <--- AÑADIR
use App\Models\State; // <--- AÑADIR
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia; // <--- AÑADIR

class UserController extends Controller
{
    /**
     * GET: Lista todos los usuarios (para la API interna).
     * Este método ESTÁ BIEN como está, tu UserIndex.vue
     * probablemente lo llama con Axios.
     */
    public function index()
    {
        $users = User::with([

            'creator:id,name',
            'updater:id,name'
        ])->get();

        return response()->json($users);
    }

    // --- 2. AÑADE EL MÉTODO CREATE (PARA MOSTRAR EL FORMULARIO) ---
    /**
     * GET: Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        // El string 'Settings/Users/UserForm' debe coincidir EXACTAMENTE
        // con la ruta del archivo en 'resources/js/pages/'
        return Inertia::render('settings/UserForm', [
            // 'roles' => Role::all(['id_role as value', 'name_role as label']), // Pasa los roles para un <select>
            // 'states' => State::all(['id_state as value', 'name_state as label']), // Pasa los estados para un <select>
        ]);
    }

    /**
     * POST: Crear usuario
     * Este método puede quedarse como está, PERO es mejor
     * que redirija con Inertia en lugar de devolver JSON.
     */
   public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255|unique:users',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'idupdater_user_user' => 'nullable|integer|exists:users,id',
    ]);

    $validatedData['password'] = Hash::make($request->password);
    $validatedData['idcreate_user_user'] = $validatedData['idupdater_user_user'] ?? null;

    User::create($validatedData);

    return redirect()->route('login')->with('success', 'Usuario creado exitosamente.');
}


    // --- 3. AÑADE EL MÉTODO EDIT (PARA MOSTRAR EL FORMULARIO) ---
    /**
     * GET: Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user) // <-- Usa Route Model Binding
    {
        return Inertia::render('settings/UserForm', [
            'user' => $user->load(['role', 'state']), // Pasa el usuario a editar
            // 'roles' => Role::all(['id_role as value', 'name_role as label']),
            // 'states' => State::all(['id_state as value', 'name_state as label']),
        ]);
    }


   /**
     * PUT/PATCH: Actualiza un usuario existente.
     */
    public function update(Request $request, User $user) // <-- $user es inyectado por Route Model Binding
    {
        // --- 1. VALIDACIÓN ---
        // Validamos los datos que vienen del UserForm.vue
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                 // Regla única: ignora el ID del usuario actual ($user->id)
                 // al comprobar si el 'name' ya existe.
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                 // Regla única: ignora el ID del usuario actual al comprobar 'email'.
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => [
                'nullable', // La contraseña es OPCIONAL en la edición
                'string',
                'min:8',
            ],
            // 'id_role_user' => 'required|integer|exists:roles,id_role',
            // 'id_state_user' => 'required|integer|exists:states,id_state',
            'idupdater_user_user' => 'required|integer|exists:users,id',
        ]);

        // --- 2. PREPARAR DATOS ---
        // Tomamos todos los datos validados EXCEPTO la contraseña por ahora.
        $dataToUpdate = $request->except('password');

        // --- 3. MANEJAR CONTRASEÑA ---
        // Solo actualizamos la contraseña si el usuario escribió una nueva.
        // Si el campo 'password' vino vacío o nulo, esta condición no se cumple.
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // --- 4. ACTUALIZAR EL MODELO ---
        // Actualizamos el usuario ($user) que Laravel encontró por nosotros.
        $user->update($dataToUpdate);

        // --- 5. REDIRIGIR ---
        // Regresamos a la página anterior (el formulario) con un mensaje de éxito.
       return Inertia::render('settings/UserIndex'); // <-- INERTIA
    }

    // ... (El resto de tus métodos de API pueden quedarse) ...

    public function indexPage()
    {
        // Esta es la conexión: le dice a Laravel que renderice
        // el archivo 'resources/js/pages/Settings/Users/UserIndex.vue'
        return Inertia::render('settings/UserIndex');
    }
}
