<?php

namespace App\Http\Controllers;

use App\Models\Relative;
use App\Models\Patient; 
use Illuminate\Http\Request;
use Inertia\Inertia;

class RelativeController extends Controller
{
    /**
     * Función helper para obtener el nombre completo y limpio de un objeto Patient.
     */
    protected function getFullName(Patient $patient): string
    {
        $names = array_filter([
            $patient->firstname_patient, 
            $patient->middlename_patient, 
            $patient->thirdname_patient
        ]);
        $lastnames = array_filter([
            $patient->lastname_patient, 
            $patient->secondlastname_patient, 
            $patient->thirdlastname_patient
        ]);
        $fullName = implode(' ', $names) . ' ' . implode(' ', $lastnames);
        return trim($fullName);
    }
    
    /**
     * Función helper para generar las opciones de paciente para los selects.
     */
    protected function getPatientOptions()
    {
        $patientColumns = [
            'id_patient', 'firstname_patient', 'middlename_patient', 'thirdname_patient', 
            'lastname_patient', 'secondlastname_patient', 'thirdlastname_patient'
        ];

        return Patient::select($patientColumns)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id_patient,
                    'name' => $this->getFullName($patient), 
                ];
            });
    }

    /**
     * Muestra una lista de relaciones de parentesco.
     * *** LÓGICA CORREGIDA ***
     * Implementamos la misma lógica del 'RelativeForm' para evitar fallos de '::with()'.
     */
    public function index()
    {
        // 1. Columnas que necesitamos para construir el nombre
        $patientColumns = [
            'id_patient', 'firstname_patient', 'middlename_patient', 'thirdname_patient', 
            'lastname_patient', 'secondlastname_patient', 'thirdlastname_patient'
        ];

        // 2. Obtenemos un "diccionario" de pacientes (ID => Objeto Paciente)
        // Usamos keyBy('id_patient') para que la búsqueda sea instantánea
        $patientMap = Patient::select($patientColumns)->get()->keyBy('id_patient');

        // 3. Obtenemos todas las relaciones (esta vez sin '::with()')
        $relativesData = Relative::get();

        // 4. Mapeamos (recorremos) las relaciones y adjuntamos los pacientes manualmente
        $relatives = $relativesData->map(function ($relative) use ($patientMap) {
            
            // Buscamos el ID principal en el mapa y lo asignamos a la propiedad 'principalPatient'
            $relative->principalPatient = $patientMap->get($relative->idprincipal_patient_relative);
            
            // Buscamos el ID secundario en el mapa y lo asignamos a la propiedad 'secondaryPatient'
            $relative->secondaryPatient = $patientMap->get($relative->idsecondary_patient_relative);
            
            return $relative;
        });

        // 5. Enviamos a Inertia
        return Inertia::render('settings/RelativeIndex', [
            'relatives' => $relatives,
        ]);
    }
    
    public function create()
    {
        return Inertia::render('settings/RelativeForm', [
            'patients' => $this->getPatientOptions(),
        ]);
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idprincipal_patient_relative' => 'required|integer|exists:patients,id_patient|different:idsecondary_patient_relative',
            'idsecondary_patient_relative' => 'required|integer|exists:patients,id_patient',
            'relationship_relative' => 'nullable|string|max:30',
            'idupdater_user_relative' => 'required|integer|exists:users,id',
        ]);

        $exists = Relative::where(function ($query) use ($validatedData) {
            $query->where('idprincipal_patient_relative', $validatedData['idprincipal_patient_relative'])
                  ->where('idsecondary_patient_relative', $validatedData['idsecondary_patient_relative']);
        })->orWhere(function ($query) use ($validatedData) {
            $query->where('idprincipal_patient_relative', $validatedData['idsecondary_patient_relative'])
                  ->where('idsecondary_patient_relative', $validatedData['idprincipal_patient_relative']);
        })->exists();

        if ($exists) {
            return back()->withErrors([
                'idprincipal_patient_relative' => 'La relación o su inversa ya existe entre estos pacientes.'
            ])->withInput();
        }

        Relative::create([
            'idprincipal_patient_relative' => $validatedData['idprincipal_patient_relative'],
            'idsecondary_patient_relative' => $validatedData['idsecondary_patient_relative'],
            'relationship_relative' => $validatedData['relationship_relative'],
            'idcreate_user_relative' => $validatedData['idupdater_user_relative'],
            'idupdater_user_relative' => $validatedData['idupdater_user_relative'],
        ]);

        return redirect()->route('relatives.index')->with('success', 'Relación de parentesco creada exitosamente.');
    }

    public function edit(Relative $relative)
    {
        return Inertia::render('settings/RelativeForm', [
            'relative' => $relative,
            'patients' => $this->getPatientOptions(),
        ]);
    }

    public function update(Request $request, Relative $relative)
    {
        $validatedData = $request->validate([
            'relationship_relative' => 'sometimes|nullable|string|max:30',
            'idupdater_user_relative' => 'required|integer|exists:users,id',
        ]);

        $relative->update($validatedData);
        return redirect()->route('relatives.index')->with('success', 'Relación de parentesco actualizada exitosamente.');
    }

    public function destroy(Relative $relative)
    {
        try {
            $relative->delete();
            return redirect()->route('relatives.index')->with('success', 'Relación eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar la relación. Puede estar siendo utilizada en otro registro.');
        }
    }
}