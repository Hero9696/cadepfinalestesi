<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Muestra una lista de pacientes con sus ubicaciones.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $patients = Patient::with([
            'birthDepartment', 'birthMunicipality',
            'locationDepartment', 'locationMunicipality',
            'branch', 'state'
        ])->get();
        return response()->json($patients);
    }

    /**
     * Almacena un nuevo paciente.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_patient' => 'required|integer|unique:patients,id_patient',
            'cui_patient' => 'nullable|string|max:15|unique:patients,cui_patient',
            'firstname_patient' => 'required|string|max:30',
            'lastname_patient' => 'required|string|max:30',
            // ... (otras validaciones de campos)
            'birthdate_patient' => 'required|date',
            'idbirth_department_patient' => 'required|integer|exists:departments,id_department',
            'idbirth_municipality_patient' => 'required|integer|exists:municipalities,id_municipality',
            'age_patient' => 'required|integer|max:150',
            'weight_patient' => 'required|numeric|max:999.99',
            'schooling_patient' => 'required|string|max:30',
            'phone_patient' => 'required|string|max:25',
            'id_department_patient' => 'required|integer|exists:departments,id_department',
            'id_municipality_patient' => 'required|integer|exists:municipalities,id_municipality',
            'address_patient' => 'required|string|max:255',
            'gender_patient' => 'required|in:Masculino,Femenino,Otro',
            'religion_patient' => 'required|string|max:25',
            'maritalstatus_patient' => 'required|string|max:20',
            'dependentfamily_patient' => 'required|boolean',
            'reasonforconsultation_patient' => 'required|string',
            'referreddoctor_patient' => 'required|string|max:60',
            'id_branch_patient' => 'required|integer|exists:branches,id_branch',
            'id_state_patient' => 'required|integer|exists:states,id_state',
            'idupdater_user_patient' => 'required|integer|exists:users,id_user',
        ]);

        $patient = Patient::create([
            ...$request->except(['idcreate_user_patient']),
            'idcreate_user_patient' => $request->idupdater_user_patient,
        ]);

        return response()->json($patient, 201);
    }

    /**
     * Muestra un paciente específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $patient = Patient::with([
            'birthDepartment', 'birthMunicipality',
            'locationDepartment', 'locationMunicipality',
            'branch', 'state'
        ])->find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Actualiza un paciente específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $request->validate([
            // La validación de unicidad ignora el ID actual
            'cui_patient' => 'nullable|string|max:15|unique:patients,cui_patient,' . $id . ',id_patient',
            // ... (otras validaciones 'sometimes|required' para campos que pueden ser actualizados)
            'idupdater_user_patient' => 'required|integer|exists:users,id_user',
        ]);

        $patient->update($request->all());

        return response()->json($patient);
    }

    /**
     * Elimina un paciente específico (o cambia de estado).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        // Recomendación: No eliminar pacientes, sino cambiar el id_state_patient.
        // Aquí se implementa una eliminación directa, pero ten cuidado con FKs.
        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully'], 204);
    }
}
