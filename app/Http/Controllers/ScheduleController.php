<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Muestra la página principal de horarios.
     * @return \Inertia\Response
     */
    public function index()
    {
        $schedules = Schedule::orderBy('hour_schedule', 'asc')->get();

        // Renderiza la página de Inertia y le pasa los horarios
        return Inertia::render('settings/ScheduleIndex', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo horario.
     * @return \Inertia\Response
     */
    public function create()
    {
        // No se pasa ninguna dependencia (states)
        return Inertia::render('settings/ScheduleForm');
    }

    /**
     * Almacena un nuevo horario.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // --- CAMPOS ACTUALIZADOS ---
            'hour_schedule' => 'required|date_format:H:i', // Esperamos HH:MM desde el frontend
            'date_schedule' => 'required|date',

            // Auditoría
            'idupdater_user_schedule' => 'required|integer|exists:users,id',
        ]);

        $dataToCreate = $validatedData;

        // El input[type="time"] de Vue solo envía HH:MM, así que forzamos a HH:MM:00 para el campo TIME
        $dataToCreate['hour_schedule'] .= ':00';

        // Auditoría
        $dataToCreate['idcreate_user_schedule'] = $request->idupdater_user_schedule;


        Schedule::create($dataToCreate);

        return redirect()->route('schedules.index')->with('success', 'Horario creado exitosamente.');
    }

    /**
     * Muestra un horario específico.
     * @param  \App\Models\Schedule  $schedule
     * @return \Inertia\Response
     */
    public function show(Schedule $schedule)
    {
        // El método show generalmente se usa para páginas de detalles
        return Inertia::render('settings/ScheduleShow', [
            'schedule' => $schedule
        ]);
    }

    /**
     * Muestra el formulario para editar un horario.
     * @param  \App\Models\Schedule  $schedule
     * @return \Inertia\Response
     */
    public function edit(Schedule $schedule)
    {
        return Inertia::render('settings/ScheduleForm', [
            // Pasamos el horario. El campo hour_schedule vendrá como HH:MM:SS de la DB.
            'schedule' => $schedule
        ]);
    }

    /**
     * Actualiza un horario específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Schedule $schedule)
    {
        // La validación ignora el horario del registro actual al verificar unicidad
        $validatedData = $request->validate([
            // --- CAMPOS ACTUALIZADOS ---
            'hour_schedule' => [
                'required',
                'date_format:H:i',
                Rule::unique('schedules')->ignore($schedule->id_schedule, 'id_schedule'),
            ],
            'date_schedule' => 'required|date',

            // Auditoría
            'idupdater_user_schedule' => 'required|integer|exists:users,id',
        ]);

        $dataToUpdate = $validatedData;

        // Aseguramos que los campos de tiempo tengan segundos (si el input solo envía HH:MM)
        $dataToUpdate['hour_schedule'] .= ':00';

        $schedule->update($dataToUpdate);

        return redirect()->route('schedules.index')->with('success', 'Horario actualizado.');
    }

    /**
     * Elimina un horario específico.
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Horario eliminado.');
    }
}
