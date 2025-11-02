<?php

namespace App\Http\Controllers;

use App\Models\Branch;
// --- ¡AÑADE ESTAS IMPORTACIONES! ---
use App\Models\Municipality;
use App\Models\Department;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException; // Para el borrado seguro

class BranchController extends Controller
{
    /**
     * Muestra la página de índice de sucursales.
     * @return \Inertia\Response
     */
    public function index()
    {
        $branches = Branch::with(['municipality', 'department', 'state'])->get();

        return Inertia::render('settings/BranchIndex', [
            'branches' => $branches
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva sucursal.
     * @return \Inertia\Response
     */
    public function create()
    {
        // Pasamos las dependencias para los <select> del formulario
        return Inertia::render('settings/BranchForm', [
            // --- CORRECCIÓN AQUÍ: Añadir 'id_department_municipality' para el filtro ---
            'municipalities' => Municipality::all(['id_municipality', 'name_municipality', 'id_department_municipality']),
            'departments' => Department::all(['id_department', 'name_department']),
            'states' => State::all(['id_state', 'name_state'])
        ]);
    }

    /**
     * Almacena una nueva sucursal.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_branch' => 'required|string|max:50',
            'phone_branch' => 'required|string|max:25',
            'id_municipality_branch' => 'required|integer|exists:municipalities,id_municipality',
            'id_department_branch' => 'required|integer|exists:departments,id_department',
            'id_state_branch' => 'required|integer|exists:states,id_state',
            // 'idupdater_user_branch' se obtiene de Auth, no del request
        ]);

        // Añadimos los campos de auditoría automáticamente
        $dataToCreate = $validatedData;
        $dataToCreate['idcreate_user_branch'] = Auth::id();
        $dataToCreate['idupdater_user_branch'] = Auth::id();

        Branch::create($dataToCreate);

        return redirect()->route('branches.index')->with('success', 'Sucursal creada exitosamente.');
    }

    /**
     * Muestra el formulario para editar una sucursal.
     * @param  \App\Models\Branch  $branch
     * @return \Inertia\Response
     */
    public function edit(Branch $branch)
    {
        // Pasamos la sucursal específica Y las dependencias al formulario
        return Inertia::render('settings/BranchForm', [
            'branch' => $branch,
            // --- CORRECCIÓN AQUÍ: Añadir 'id_department_municipality' para el filtro ---
            'municipalities' => Municipality::all(['id_municipality', 'name_municipality', 'id_department_municipality']),
            'departments' => Department::all(['id_department', 'name_department']),
            'states' => State::all(['id_state', 'name_state'])
        ]);
    }

    /**
     * Actualiza una sucursal específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Branch $branch)
    {
        $validatedData = $request->validate([
            'name_branch' => 'sometimes|required|string|max:50',
            'phone_branch' => 'sometimes|required|string|max:25',
            'id_municipality_branch' => 'sometimes|required|integer|exists:municipalities,id_municipality',
            'id_department_branch' => 'sometimes|required|integer|exists:departments,id_department',
            'id_state_branch' => 'sometimes|required|integer|exists:states,id_state',
             // 'idupdater_user_branch' se obtiene de Auth
        ]);

        // Añadimos el campo de auditoría
        $dataToUpdate = $validatedData;
        $dataToUpdate['idupdater_user_branch'] = Auth::id();

        $branch->update($dataToUpdate);

        // Redirigimos de vuelta al índice
        return redirect()->route('branches.index')->with('success', 'Sucursal actualizada.');
    }

    /**
     * Elimina una sucursal específica.
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Branch $branch)
    {
        try {
            $branch->delete();
        } catch (QueryException $e) {
            // Captura errores de llave foránea (si la sucursal está en uso)
            return redirect()->back()->with('error', 'No se puede eliminar la sucursal, tiene registros asociados.');
        }

        return redirect()->route('branches.index')->with('success', 'Sucursal eliminada.');
    }
}
