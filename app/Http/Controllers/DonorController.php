// app/Http/Controllers/DonorController.php

<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorController extends Controller
{
    /**
     * Muestra una lista de donadores no registrados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $donors = Donor::all();
        return response()->json($donors);
    }

    /**
     * Almacena un nuevo donador no registrado.
     * El campo 'id_donor' se gestiona mediante un TRIGGER en la BD.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Nota: El campo id_donor (PK) y id_donor_int (AI) se generan por el trigger en la BD.
        $request->validate([
            'email_donor' => 'required|email|max:255',
            'title_donor' => 'nullable|string|max:50',
            'firstname_donor' => 'required|string|max:100',
            'lastname_donor' => 'required|string|max:100',
            'country_donor' => 'required|string|max:100',
            'zipcode_donor' => 'required|string|max:20',
            'state_donor' => 'required|string|max:100',
            'address_donor' => 'required|string|max:255',
            'unit_donor' => 'nullable|string|max:100',
            'city_donor' => 'required|string|max:100',
            'phone_donor' => 'nullable|string|max:50',
            'mobile_donor' => 'nullable|string|max:50',
            'idupdater_user_donor' => 'nullable|integer|exists:users,id_user',
        ]);

        $donor = Donor::create($request->all());

        return response()->json($donor, 201);
    }

    /**
     * Muestra un donador no registrado específico (usando id_donor).
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $donor = Donor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Donor not found'], 404);
        }

        return response()->json($donor);
    }

    /**
     * Actualiza un donador no registrado específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $donor = Donor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Donor not found'], 404);
        }

        $request->validate([
            'email_donor' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('donors', 'email_donor')->ignore($id, 'id_donor')],
            'title_donor' => 'nullable|string|max:50',
            'firstname_donor' => 'sometimes|required|string|max:100',
            'lastname_donor' => 'sometimes|required|string|max:100',
            'country_donor' => 'sometimes|required|string|max:100',
            'zipcode_donor' => 'sometimes|required|string|max:20',
            'state_donor' => 'sometimes|required|string|max:100',
            'address_donor' => 'sometimes|required|string|max:255',
            'unit_donor' => 'nullable|string|max:100',
            'city_donor' => 'sometimes|required|string|max:100',
            'phone_donor' => 'nullable|string|max:50',
            'mobile_donor' => 'nullable|string|max:50',
            'idupdater_user_donor' => 'nullable|integer|exists:users,id_user',
        ]);

        $donor->update($request->all());

        return response()->json($donor);
    }

    /**
     * Elimina un donador no registrado.
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $donor = Donor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Donor not found'], 404);
        }

        $donor->delete();

        return response()->json(['message' => 'Donor deleted successfully'], 204);
    }
}
