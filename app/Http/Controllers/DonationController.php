// app/Http/Controllers/DonationController.php

<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    /**
     * Muestra una lista de donaciones.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $donations = Donation::with('patient')->get();
        // Nota: Cargar el donante requiere un loop o un accessor debido a la relación polimórfica manual.
        return response()->json($donations);
    }

    /**
     * Almacena una nueva donación.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'donor_type_donation' => ['required', Rule::in(['S', 'R'])],
            'id_donor_donation' => [
                'required',
                'string',
                // Validación condicional para asegurar que el ID del donante existe en la tabla correcta
                Rule::when($request->donor_type_donation === 'S', ['exists:donors,id_donor']),
                Rule::when($request->donor_type_donation === 'R', ['exists:registereddonors,id_registereddonor']),
            ],
            'amount_donation' => 'required|numeric|min:0.01',
            'currency_donation' => 'required|string|max:10',
            'paymentmethod_donation' => 'nullable|string|max:50',
            'transactionid_donation' => 'nullable|string|max:100',
            'id_patient_donations' => 'nullable|integer|exists:patients,id_patient',
            'idupdater_user_donation' => 'nullable|integer|exists:users,id_user',
        ]);

        $donation = Donation::create([
            ...$request->except(['idcreate_user_donation']),
            'idcreate_user_donation' => $request->idupdater_user_donation,
        ]);

        return response()->json($donation->load('patient'), 201);
    }

    /**
     * Muestra una donación específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $donation = Donation::with('patient')->find($id);

        if (!$donation) {
            return response()->json(['message' => 'Donation not found'], 404);
        }

        // Cargar el donante manualmente (si es necesario)
        $donation->donor = $donation->donor();

        return response()->json($donation);
    }

    /**
     * Actualiza una donación específica.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json(['message' => 'Donation not found'], 404);
        }

        $request->validate([
            'amount_donation' => 'sometimes|required|numeric|min:0.01',
            'currency_donation' => 'sometimes|required|string|max:10',
            'id_patient_donations' => 'nullable|integer|exists:patients,id_patient',
            'idupdater_user_donation' => 'required|integer|exists:users,id_user',
            // No permitir cambiar el donante o el tipo de donante después de la creación
        ]);

        $donation->update($request->all());

        return response()->json($donation);
    }

    /**
     * Elimina una donación específica.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json(['message' => 'Donation not found'], 404);
        }

        $donation->delete();

        return response()->json(['message' => 'Donation deleted successfully'], 204);
    }
}
