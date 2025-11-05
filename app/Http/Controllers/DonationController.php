<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\RegisteredDonor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Patient;

class DonationController extends Controller
{
    /**
     * Helper para cargar datos comunes del formulario (Donantes y Pacientes).
     * @return array
     */
    private function loadFormData(): array
    {
        // 1. Cargar Donantes Simples (S)
        // NOTA: Usamos id_donor_int y concatenamos la 'S' para el frontend (Ej: S5)
        $simpleDonors = Donor::select('id_donor_int', 'firstname_donor', 'lastname_donor')
            ->get()
            ->map(fn ($d) => [
                // CLAVE: Concatenar el prefijo 'S' con el ID entero para Vue
                'id' => 'S' . $d->id_donor_int,
                'name' => 'Simple (S): ' . $d->firstname_donor . ' ' . $d->lastname_donor,
            ]);

        // 2. Cargar Donantes Registrados (R)
        // NOTA: Usamos id_registereddonor_int y concatenamos la 'R' para el frontend (Ej: R5)
        $registeredDonors = RegisteredDonor::select('id_registereddonor_int', 'firstname_registereddonor', 'lastname_registereddonor')
            ->get()
            ->map(fn ($d) => [
                // CLAVE: Concatenar el prefijo 'R' con el ID entero para Vue
                'id' => 'R' . $d->id_registereddonor_int,
                'name' => 'Registrado (R): ' . $d->firstname_registereddonor . ' ' . $d->lastname_registereddonor,
            ]);

        // 3. Combinar Donantes
        $allDonors = $simpleDonors->concat($registeredDonors)->sortBy('name')->values();

        // 4. Cargar Pacientes
        $patients = Patient::select('id_patient', 'firstname_patient', 'lastname_patient')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id_patient,
                'name' => $p->firstname_patient . ' ' . $p->lastname_patient,
            ]);

        return [
            'donors' => $allDonors,
            'patients' => $patients,
        ];
    }

    // --- MÉTODOS CRUD ---

    public function index(Request $request)
    {
        // En el índice, debemos buscar por id_donor_int cuando el tipo es 'S',
        // pero la base de datos solo guarda el valor numérico en id_donor_donation.
        $donations = Donation::with('patient')
            ->orderBy('createdate_donation', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($donation) {
                // Lógica para mostrar el donante en el índice
                $donor = null;
                if ($donation->donor_type_donation === 'S') {
                    // Si el ID guardado en donaciones es solo el número, buscamos por id_donor_int
                    $donor = Donor::where('id_donor_int', $donation->id_donor_donation)->first();
                } elseif ($donation->donor_type_donation === 'R') {
                    $donor = RegisteredDonor::where('id_registereddonor_int', $donation->id_donor_donation)->first();
                }

                return [
                    'id' => $donation->id_donation,
                    'type' => $donation->donor_type_donation,
                    'donor_name' => $donor ? ($donor->firstname_donor ?? $donor->firstname_registereddonor ?? 'N/A') . ' ' . ($donor->lastname_donor ?? $donor->lastname_registereddonor ?? '') : 'Donante No Encontrado',
                    'amount' => $donation->amount_donation,
                    'currency' => $donation->currency_donation,
                    'patient_id' => $donation->id_patient_donations,
                    'patient_name' => $donation->patient?->firstname_patient . ' ' . $donation->patient?->lastname_patient ?? 'N/A',
                    'created_at' => $donation->createdate_donation,
                ];
            });

        return Inertia::render('settings/DonationIndex', [
            'donations' => $donations,
        ]);
    }

    public function create()
    {
        return Inertia::render('settings/DonationForm', $this->loadFormData());
    }

    public function store(Request $request)
    {
        // 1. Reglas de validación de campos de la donación (incluyendo el ID combinado)
        $validationRules = [
            'donor_full_id' => 'required|string',
            'amount_donation' => 'required|numeric|min:0.01',
            'currency_donation' => 'required|string|max:10',
            'paymentmethod_donation' => 'nullable|string|max:50',
            'transactionid_donation' => 'nullable|string|max:100',
            'notes_donation' => 'nullable|string|max:1000',
            'id_patient_donations' => 'nullable|integer|exists:patients,id_patient',
        ];

        $validated = $request->validate($validationRules);

        // 2. Separar el ID y el tipo del campo combinado 'donor_full_id' (Ej: 'S5')
        $donorFullId = $validated['donor_full_id'];
        $donorType = substr($donorFullId, 0, 1); // Extrae 'S' o 'R'
        $donorId = substr($donorFullId, 1); // Extrae '5' (solo el ID sin el prefijo)

        // 3. Definir la tabla y columna para la validación de existencia (Paso 4)
        if ($donorType === 'S') {
            $table = 'donors';
            // CLAVE: CAMBIO DE id_donor a id_donor_int
            $column = 'id_donor_int';
        } elseif ($donorType === 'R') {
            $table = 'registereddonors';
            // CLAVE: CAMBIO DE id_registereddonor a id_registereddonor_int
            $column = 'id_registereddonor_int';
        } else {
            return Redirect::back()->withErrors(['donor_full_id' => 'Tipo de donante no válido.'])->withInput();
        }

        // 4. Validar la existencia del Donante (usando las variables definidas)
        $request->validate(['donor_full_id' => [
            Rule::exists($table, $column)->where(function ($query) use ($donorId, $column) {
                // NOTA: Usamos $donorId (el valor numérico) y buscamos en la nueva columna INT.
                return $query->where($column, $donorId);
            }),
        ]], ['donor_full_id.exists' => 'El donante seleccionado no existe.'], [
            'donor_full_id' => 'Donante'
        ]);


        $userId = Auth::id() ?? 1;

        Donation::create(array_merge($validated, [
            // Campos específicos de la relación
            'donor_type_donation' => $donorType,
            // CLAVE: Guardamos SOLO el ID numérico (Ej: '5') en id_donor_donation.
            'id_donor_donation' => $donorId,

            // Campos de auditoría
            'idcreate_user_donation' => $userId,
            'idupdater_user_donation' => $userId,
        ]));

        return Redirect::route('donations.index')->with('success', 'Donación registrada exitosamente.');
    }

    public function show(Donation $donation)
    {
        $donation->load('patient');
        return Inertia::render('settings/DonationShow', ['donation' => $donation]);
    }

    public function edit(Donation $donation)
    {
        // Envía la lista de donantes, pacientes y la donación a editar
        return Inertia::render('settings/DonationForm', array_merge($this->loadFormData(), [
            'donation' => $donation,
        ]));
    }

    public function update(Request $request, Donation $donation)
    {
        // En la edición, no permitimos cambiar el donante (id_donor_donation/donor_type_donation)
        $validated = $request->validate([
            'amount_donation' => 'sometimes|required|numeric|min:0.01',
            'currency_donation' => 'sometimes|required|string|max:10',
            'paymentmethod_donation' => 'nullable|string|max:50',
            'transactionid_donation' => 'nullable|string|max:100',
            'notes_donation' => 'nullable|string|max:1000',
            'id_patient_donations' => 'nullable|integer|exists:patients,id_patient',
        ]);

        $donation->update(array_merge($validated, [
            'idupdater_user_donation' => Auth::id() ?? 1,
        ]));

        return Redirect::back()->with('success', 'Donación ' . $donation->id_donation . ' actualizada.');
    }

    public function destroy(Donation $donation)
    {
        $donationId = $donation->id_donation;
        $donation->delete();
        return Redirect::route('donations.index')->with('success', 'Donación ' . $donationId . ' eliminada exitosamente.');
    }
}
