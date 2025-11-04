<?php

namespace App\Http\Controllers;

use App\Models\Patient;
// --- DEPENDENCIAS ---
use App\Models\Branch;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
// --- FIN DEPENDENCIAS ---

class PatientController extends Controller
{
    /**
     * Muestra la página principal de gestión de pacientes.
     * @return \Inertia\Response
     */
    public function index()
    {
        // Cargamos las relaciones clave para la tabla principal
        $patients = Patient::with([
            'birthDepartment', 'birthMunicipality',
            'locationDepartment', 'locationMunicipality',
            'branch', 'state'
        ])->get();

        return Inertia::render('settings/PatientIndex', [
            'patients' => $patients,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo paciente.
     * @return \Inertia\Response
     */
    public function create()
    {
        // Pasamos todas las dependencias necesarias al formulario
        return Inertia::render('settings/PatientForm', [
            'branches' => Branch::all(['id_branch', 'name_branch']),
            'states' => State::all(['id_state', 'name_state']),
            'departments' => Department::all(['id_department', 'name_department']),
            // Se pasan todos los municipios para la lógica de filtrado en cascada en Vue
            'municipalities' => Municipality::all(['id_municipality', 'name_municipality', 'id_department_municipality']),
        ]);
    }

    /**
     * Almacena un nuevo paciente.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validamos todos los campos del formulario
        $validatedData = $request->validate([
            'id_patient' => 'required|integer|unique:patients,id_patient',
            'cui_patient' => 'nullable|string|max:15|unique:patients,cui_patient',
            'firstname_patient' => 'required|string|max:30',

            // Nombres Opcionales
            'middlename_patient' => 'nullable|string|max:30',
            'thirdname_patient' => 'nullable|string|max:30',

            'lastname_patient' => 'required|string|max:30',

            // Apellidos Opcionales
            'secondlastname_patient' => 'nullable|string|max:30',
            'thirdlastname_patient' => 'nullable|string|max:30',

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
        ]);

        $patient = Patient::create([
            ...$validatedData,
            'idcreate_user_patient' => Auth::id(),
            'idupdater_user_patient' => Auth::id(),
        ]);

        return redirect()->route('patients.index')->with('success', 'Paciente registrado con éxito.');
    }

    /**
     * Muestra el formulario para editar un paciente.
     * @param  \App\Models\Patient  $patient
     * @return \Inertia\Response
     */
    public function edit(Patient $patient)
    {
        // Pasamos el paciente y las dependencias (igual que en create)
        return Inertia::render('settings/PatientForm', [
            'patient' => $patient,
            'branches' => Branch::all(['id_branch', 'name_branch']),
            'states' => State::all(['id_state', 'name_state']),
            'departments' => Department::all(['id_department', 'name_department']),
            'municipalities' => Municipality::all(['id_municipality', 'name_municipality', 'id_department_municipality']),
        ]);
    }

    /**
     * Actualiza un paciente específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Patient $patient)
    {
        $validatedData = $request->validate([
            // La validación de unicidad ignora el ID actual del paciente
            'cui_patient' => ['nullable', 'string', 'max:15', Rule::unique('patients', 'cui_patient')->ignore($patient->id_patient, 'id_patient')],

            // Campos que pueden cambiar (usamos sometimes|required)
            'firstname_patient' => 'sometimes|required|string|max:30',
            'lastname_patient' => 'sometimes|required|string|max:30',
            'birthdate_patient' => 'sometimes|required|date',
            'age_patient' => 'sometimes|required|integer|max:150',
            'weight_patient' => 'sometimes|required|numeric|max:999.99',
            'schooling_patient' => 'sometimes|required|string|max:30',
            'phone_patient' => 'sometimes|required|string|max:25',
            'address_patient' => 'sometimes|required|string|max:255',
            'gender_patient' => 'sometimes|required|in:Masculino,Femenino,Otro',
            'religion_patient' => 'sometimes|required|string|max:25',
            'maritalstatus_patient' => 'sometimes|required|string|max:20',
            'dependentfamily_patient' => 'sometimes|required|boolean',
            'reasonforconsultation_patient' => 'sometimes|required|string',
            'referreddoctor_patient' => 'sometimes|required|string|max:60',
            'id_branch_patient' => 'sometimes|required|integer|exists:branches,id_branch',
            'id_state_patient' => 'sometimes|required|integer|exists:states,id_state',

            // Campos geográficos (siempre deben existir si se van a actualizar)
            'idbirth_department_patient' => 'sometimes|required|integer|exists:departments,id_department',
            'idbirth_municipality_patient' => 'sometimes|required|integer|exists:municipalities,id_municipality',
            'id_department_patient' => 'sometimes|required|integer|exists:departments,id_department',
            'id_municipality_patient' => 'sometimes|required|integer|exists:municipalities,id_municipality',

            // Campos opcionales
            'middlename_patient' => 'nullable|string|max:30',
            'thirdname_patient' => 'nullable|string|max:30',
            'secondlastname_patient' => 'nullable|string|max:30',
            'thirdlastname_patient' => 'nullable|string|max:30',
        ]);

        $patient->update([
            ...$validatedData,
            'idupdater_user_patient' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Paciente actualizado con éxito.');
    }

    /**
     * Elimina un paciente específico.
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Paciente eliminado con éxito.');
    }
}
