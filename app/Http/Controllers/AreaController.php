<?php

namespace App\Http\Controllers;

use App\Models\Area;
// --- ¡AÑADE ESTAS IMPORTACIONES! ---
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException; // Para el borrado seguro

class AreaController extends Controller
{
    /**
     * Muestra la página de índice de áreas.
     * @return \Inertia\Response
     */
    public function index()
    {
        $areas = Area::with('state')->get();

        return Inertia::render('settings/AreaIndex', [
            'areas' => $areas
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva área.
     * @return \Inertia\Response
     */
    public function create()
    {
        // Pasamos los 'states' para el <select> del formulario
        return Inertia::render('settings/AreaForm', [
            'states' => State::all(['id_state', 'name_state'])
        ]);
    }

    /**
     * Almacena una nueva área.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_area' => 'required|string|max:30|unique:areas,name_area',
            'description_area' => 'required|string',
            // --- CORRECCIÓN AQUÍ: Se eliminó la regla 'default:...' que no es válida ---
            'duration_area' => 'nullable|date_format:H:i:s',
            'id_state_area' => 'required|integer|exists:states,id_state',
            // 'idupdater_user_area' y 'idcreate_user_area' se obtienen de Auth
        ]);

        // Añadimos los campos de auditoría automáticamente
        $dataToCreate = $validatedData;
        $dataToCreate['idcreate_user_area'] = Auth::id();
        $dataToCreate['idupdater_user_area'] = Auth::id();

        Area::create($dataToCreate);

        return redirect()->route('areas.index')->with('success', 'Área creada exitosamente.');
    }

    /**
     * Muestra un área específica (opcional, para una página de detalles).
     * @param  \App\Models\Area  $area
     * @return \Inertia\Response
     */
    public function show(Area $area)
    {
        return Inertia::render('settings/AreaShow', [
            'area' => $area->load(['state', 'creator', 'updater'])
        ]);
    }

    /**
     * Muestra el formulario para editar un área.
     * @param  \App\Models\Area  $area
     * @return \Inertia\Response
     */
    public function edit(Area $area)
    {
        // Pasamos el área específica Y los estados
        return Inertia::render('settings/AreaForm', [
            'area' => $area,
            'states' => State::all(['id_state', 'name_state'])
        ]);
    }

    /**
     * Actualiza un área específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Area $area)
    {
        $validatedData = $request->validate([
            'name_area' => [
                'sometimes', 'required', 'string', 'max:30',
                // Aseguramos que el nombre sea único, ignorando el área actual
                Rule::unique('areas')->ignore($area->id_area, 'id_area')
            ],
            'description_area' => 'sometimes|required|string',
            'duration_area' => 'nullable|date_format:H:i:s',
            'id_state_area' => 'sometimes|required|integer|exists:states,id_state',
            // 'idupdater_user_area' se obtiene de Auth
        ]);

        // Añadimos el campo de auditoría
        $dataToUpdate = $validatedData;
        $dataToUpdate['idupdater_user_area'] = Auth::id();

        $area->update($dataToUpdate);

        // Redirigimos de vuelta al índice
        return redirect()->route('areas.index')->with('success', 'Área actualizada.');
    }

    /**
     * Elimina un área específica.
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Area $area)
    {
        try {
            $area->delete();
        } catch (QueryException $e) {
            // Captura errores de llave foránea (si el área está en uso)
            return redirect()->back()->with('error', 'No se puede eliminar el área, tiene registros asociados.');
        }

        return redirect()->route('areas.index')->with('success', 'Área eliminada.');
    }
}

