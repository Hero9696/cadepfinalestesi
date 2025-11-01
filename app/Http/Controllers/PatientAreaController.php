<?php

namespace App\Http\Controllers;

use App\Models\PatientArea;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientAreaController extends Controller
{
    /**
     * Muestra la lista de asignaciones de áreas a pacientes.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $patientAreas = PatientArea::with(['patient', 'area'])->get();
        return response()->json($patientAreas);
    }

    /**
     * Asigna una nueva área a un paciente.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_patient_patientarea' => 'required|integer|exists:patients,id_patient',
            'id_area_patientarea' => 'required|integer|exists:areas,id_area',
            'idupdater_user_patientarea' => 'required|integer|exists:users,id_user',
        ]);

        // Asegurar que la combinación no exista ya
        $exists = PatientArea::where('id_patient_patientarea', $request->id_patient_patientarea)
                             ->where('id_area_patientarea', $request->id_area_patientarea)
                             ->exists();

        if ($exists) {
            return response()->json(['message' => 'Patient already assigned to this area.'], 409);
        }

        $patientArea = PatientArea::create([
            'id_patient_patientarea' => $request->id_patient_patientarea,
            'id_area_patientarea' => $request->id_area_patientarea,
            'idcreate_user_patientarea' => $request->idupdater_user_patientarea,
            'idupdater_user_patientarea' => $request->idupdater_user_patientarea,
        ]);

        return response()->json($patientArea, 201);
    }

    /**
     * Muestra una asignación específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $patientArea = PatientArea::with(['patient', 'area'])->find($id);

        if (!$patientArea) {
            return response()->json(['message' => 'Patient Area assignment not found'], 404);
        }

        return response()->json($patientArea);
    }

    /**
     * La actualización aquí solo puede cambiar el usuario que actualiza (o sus timestamps).
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $patientArea = PatientArea::find($id);

        if (!$patientArea) {
            return response()->json(['message' => 'Patient Area assignment not found'], 404);
        }

        $request->validate([
            'idupdater_user_patientarea' => 'required|integer|exists:users,id_user',
        ]);

        // Simplemente actualiza el timestamp y el usuario que modifica
        $patientArea->update(['idupdater_user_patientarea' => $request->idupdater_user_patientarea]);

        return response()->json($patientArea);
    }

    /**
     * Desasigna (elimina) un área de un paciente.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $patientArea = PatientArea::find($id);

        if (!$patientArea) {
            return response()->json(['message' => 'Patient Area assignment not found'], 404);
        }

        $patientArea->delete();

        return response()->json(['message' => 'Patient Area assignment deleted successfully'], 204);
    }
}
