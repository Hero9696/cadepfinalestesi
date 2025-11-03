<?php

namespace App\Http\Controllers;

use App\Models\Week;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class WeekController extends Controller
{
    /**
     * Muestra la página de índice de los días de la semana.
     * @return \Inertia\Response
     */
    public function index()
    {
        // Pasamos la lista completa de días de la semana
        $weeks = Week::orderBy('id_week', 'asc')->get();

        return Inertia::render('settings/WeekIndex', [
            'weeks' => $weeks,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo día (solo nombre).
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('settings/WeekForm');
    }

    /**
     * Almacena un nuevo día de la semana.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_week' => 'required|string|max:20|unique:weeks,name_week',
        ]);

        Week::create($validatedData);

        return redirect()->route('week.index')->with('success', 'Día de la semana creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un día de la semana.
     * @param  \App\Models\Week  $week
     * @return \Inertia\Response
     */
    public function edit(Week $week)
    {
        // Pasa el objeto Week al formulario
        return Inertia::render('settings/WeekForm', [
            'week' => $week,
        ]);
    }

    /**
     * Actualiza un día de la semana específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Week  $week
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Week $week)
    {
        $validatedData = $request->validate([
            'name_week' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                // Ignora el nombre del registro actual al verificar la unicidad
                Rule::unique('weeks')->ignore($week->id_week, 'id_week')
            ],
        ]);

        $week->update($validatedData);

        return redirect()->route('week.index')->with('success', 'Día de la semana actualizado.');
    }

    /**
     * Elimina un día de la semana específico.
     * @param  \App\Models\Week  $week
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Week $week)
    {
        $week->delete();

        return redirect()->route('week.index')->with('success', 'Día de la semana eliminado.');
    }
}
