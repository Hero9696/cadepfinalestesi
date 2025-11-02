<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
// --- ¡AÑADE ESTAS IMPORTACIONES! ---
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Muestra la página principal de departamentos.
     * @return \Inertia\Response
     */
    public function indexPage()
    {
        // Pasa los departamentos como props a la página de Inertia
        $departments = Department::all();
        
        return Inertia::render('settings/DepartmentIndex', [
            'departments' => $departments
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo departamento.
     * @return \Inertia\Response
     */
    public function create()
    {
        // Renderiza el componente de formulario (asumiendo la ruta)
        return Inertia::render('settings/DepartmentForm');
    }

    /**
     * Almacena un nuevo departamento.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Mantenemos tu validación original
        $request->validate([
            'id_department' => 'required|integer|unique:departments,id_department',
            'name_department' => 'nullable|string|max:40',
        ]);

        // !! IMPORTANTE !!
        // Esto solo funcionará si 'id_department' y 'name_department'
        // están en el array '$fillable' de tu modelo App\Models\Department.
        Department::create($request->all());

        // Redirige de vuelta al índice con un mensaje
        return redirect()->route('departments.index')->with('success', 'Departamento creado exitosamente.');
    }

    /**
     * Muestra un departamento específico.
     * @param  int  $department (Este ID viene de la ruta {department})
     * @return \Inertia\Response
     */
    public function show(int $department)
    {
        $dept = Department::find($department);

        if (!$dept) {
            abort(404); // Aborta si no se encuentra
        }

        // Renderiza la página de "ver" (si la tienes)
        return Inertia::render('settings/DepartmentShow', [
            'department' => $dept
        ]);
    }

    /**
     * Muestra el formulario para editar un departamento.
     * @param  int  $department
     * @return \Inertia\Response
     */
    public function edit(int $department)
    {
        $dept = Department::find($department);

        if (!$dept) {
            abort(404);
        }

        // Renderiza el formulario, pasando el departamento a editar
        return Inertia::render('settings/DepartmentForm', [
            'department' => $dept
        ]);
    }

    /**
     * Actualiza un departamento específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $department (Este ID viene de la ruta {department})
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $department)
    {
        $dept = Department::find($department);

        if (!$dept) {
            abort(404);
        }

        // Mantenemos tu validación original (corregida para la variable $department)
        $request->validate([
            'id_department' => 'sometimes|integer|in:' . $department, // No permite cambiar el ID
            'name_department' => 'nullable|string|max:40',
        ]);

        $dept->update($request->all());

        // Redirige de vuelta al formulario de edición con un mensaje
        return redirect()->route('departments.index')->with('success', 'Departamento Actualizado.');
    }

    /**
     * Elimina un departamento específico.
     * @param  int  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $department)
    {
        $dept = Department::find($department);

        if (!$dept) {
            abort(404);
        }

        $dept->delete();

        // Redirige al índice con un mensaje
        return redirect()->route('departments.index')->with('success', 'Departamento eliminado.');
    }
}
