<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
// --- ¡AÑADE ESTAS IMPORTACIONES! ---
use App\Models\Department; // Para pasar los departamentos al formulario
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class MunicipalityController extends Controller
{
    /**
     * Muestra la página principal de municipios.
     * @return \Inertia\Response
     */
    public function index()
    {
        // Pasamos los municipios (con sus departamentos) como props a la página
        $municipalities = Municipality::with('department')->get();

        return Inertia::render('settings/MunicipalityIndex', [
            'municipalities' => $municipalities
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo municipio.
     * @return \Inertia\Response
     */
    public function create()
    {
        // Pasamos los departamentos (para el <select>) al formulario
        return Inertia::render('settings/MunicipalityForm', [
            'departments' => Department::all()
        ]);
    }

    /**
     * Almacena un nuevo municipio.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            // La PK 'id_municipality' debe ser única
            'id_municipality' => 'required|integer|unique:municipalities,id_municipality',
            'id_department_municipality' => 'required|integer|exists:departments,id_department',
            'name_municipality' => 'nullable|string|max:50',
        ]);

        Municipality::create($request->all());

        return redirect()->route('municipalities.index')->with('success', 'Municipio creado exitosamente.');
    }

    /**
     * Muestra un municipio específico (página de detalles, si la necesitas).
     * @param  \App\Models\Municipality  $municipality
     * @return \Inertia\Response
     */
    public function show(Municipality $municipality)
    {
        return Inertia::render('settings/MunicipalityShow', [
            'municipality' => $municipality->load('department')
        ]);
    }

    /**
     * Muestra el formulario para editar un municipio.
     * @param  \App\Models\Municipality  $municipality
     * @return \Inertia\Response
     */
    public function edit(Municipality $municipality)
    {
        // Pasamos el municipio a editar Y la lista de departamentos
        return Inertia::render('settings/MunicipalityForm', [
            'municipality' => $municipality,
            'departments' => Department::all()
        ]);
    }

    /**
     * Actualiza un municipio específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Municipality  $municipality
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Municipality $municipality)
    {
        $request->validate([
            // El ID no se puede cambiar, pero lo validamos por si acaso
            'id_municipality' => [
                'sometimes',
                'integer',
                Rule::in([$municipality->id_municipality]) // No debe cambiar
            ],
            'id_department_municipality' => 'sometimes|integer|exists:departments,id_department',
            'name_municipality' => 'nullable|string|max:50',
        ]);

        // Actualizamos solo los campos que vinieron en el request
        $municipality->update($request->all());

        return redirect()->route('municipalities.index')->with('success', 'Municipio actualizado.');
    }

    /**
     * Elimina un municipio específico.
     * @param  \App\Models\Municipality  $municipality
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Municipality $municipality)
    {
        try {
            $municipality->delete();
        } catch (\Exception $e) {
            // Manejar error de llave foránea (si un municipio no se puede borrar)
            return redirect()->back()->with('error', 'No se pudo eliminar el municipio, es probable que esté en uso.');
        }

        return redirect()->route('municipalities.index')->with('success', 'Municipio eliminado.');
    }
}
