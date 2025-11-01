<?php

namespace App\Http\Controllers;

use App\Models\Relative;
use Illuminate\Http\Request;

class RelativeController extends Controller
{
    /**
     * Muestra una lista de relaciones de parentesco.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $relatives = Relative::with(['principalPatient', 'secondaryPatient'])->get();
        return response()->json($relatives);
    }

    /**
     * Almacena una nueva relación de parentesco.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'idprincipal_patient_relative' => 'required|integer|exists:patients,id_patient|different:idsecondary_patient_relative',
            'idsecondary_patient_relative' => 'required|integer|exists:patients,id_patient',
            'relationship_relative' => 'nullable|string|max:30',
            'idupdater_user_relative' => 'required|integer|exists:users,id_user',
        ]);

        // Evitar duplicados (ejemplo: A es padre de B, evitar registrar B es hijo de A si ya existe A-B)
        $exists = Relative::where(function ($query) use ($request) {
            $query->where('idprincipal_patient_relative', $request->idprincipal_patient_relative)
                  ->where('idsecondary_patient_relative', $request->idsecondary_patient_relative);
        })->orWhere(function ($query) use ($request) {
            // Revisa la relación inversa para evitar redundancia
            $query->where('idprincipal_patient_relative', $request->idsecondary_patient_relative)
                  ->where('idsecondary_patient_relative', $request->idprincipal_patient_relative);
        })->exists();

        if ($exists) {
            return response()->json(['message' => 'The relationship or its inverse already exists.'], 409);
        }

        $relative = Relative::create([
            ...$request->except(['idcreate_user_relative']),
            'idcreate_user_relative' => $request->idupdater_user_relative,
        ]);

        return response()->json($relative, 201);
    }

    /**
     * Muestra una relación de parentesco específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $relative = Relative::with(['principalPatient', 'secondaryPatient'])->find($id);

        if (!$relative) {
            return response()->json(['message' => 'Relative relationship not found'], 404);
        }

        return response()->json($relative);
    }

    /**
     * Actualiza una relación de parentesco específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json(['message' => 'Relative relationship not found'], 404);
        }

        $request->validate([
            'relationship_relative' => 'sometimes|nullable|string|max:30',
            'idupdater_user_relative' => 'required|integer|exists:users,id_user',
            // Los IDs de paciente principal y secundario generalmente no deberían cambiar
        ]);

        $relative->update($request->all());

        return response()->json($relative);
    }

    /**
     * Elimina una relación de parentesco específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json(['message' => 'Relative relationship not found'], 404);
        }

        $relative->delete();

        return response()->json(['message' => 'Relative relationship deleted successfully'], 204);
    }
}
