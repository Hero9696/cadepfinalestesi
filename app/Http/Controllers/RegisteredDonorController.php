<?php

namespace App\Http\Controllers;

use App\Models\RegisteredDonor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisteredDonorController extends Controller
{
    /**
     * Muestra una lista de donadores registrados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $donors = RegisteredDonor::all();
        return response()->json($donors);
    }

    /**
     * Almacena un nuevo donador registrado.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'email_registereddonor' => 'required|email|max:255|unique:registereddonors,email_registereddonor',
            'username_registereddonor' => 'required|string|max:100|unique:registereddonors,username_registereddonor',
            'password_registereddonor' => 'required|string|min:8|max:256',
            'title_registereddonor' => 'nullable|string|max:50',
            'firstname_registereddonor' => 'required|string|max:100',
            'lastname_registereddonor' => 'required|string|max:100',
            'country_registereddonor' => 'required|string|max:100',
            'zipcode_registereddonor' => 'required|string|max:20',
            'state_registereddonor' => 'required|string|max:100',
            'address_registereddonor' => 'required|string|max:255',
            'unit_registereddonor' => 'nullable|string|max:100',
            'city_registereddonor' => 'required|string|max:100',
            'phone_registereddonor' => 'nullable|string|max:50',
            'mobile_registereddonor' => 'nullable|string|max:50',
            'idupdater_user_registereddonor' => 'nullable|integer|exists:users,id_user',
        ]);

        $donor = RegisteredDonor::create([
            'email_registereddonor' => $request->email_registereddonor,
            'username_registereddonor' => $request->username_registereddonor,
            'password_registereddonor' => Hash::make($request->password_registereddonor), // Hashear la contraseña
            'title_registereddonor' => $request->title_registereddonor,
            'firstname_registereddonor' => $request->firstname_registereddonor,
            'lastname_registereddonor' => $request->lastname_registereddonor,
            'country_registereddonor' => $request->country_registereddonor,
            'zipcode_registereddonor' => $request->zipcode_registereddonor,
            'state_registereddonor' => $request->state_registereddonor,
            'address_registereddonor' => $request->address_registereddonor,
            'unit_registereddonor' => $request->unit_registereddonor,
            'city_registereddonor' => $request->city_registereddonor,
            'phone_registereddonor' => $request->phone_registereddonor,
            'mobile_registereddonor' => $request->mobile_registereddonor,
            'idcreate_user_registereddonor' => $request->idupdater_user_registereddonor,
            'idupdater_user_registereddonor' => $request->idupdater_user_registereddonor,
        ]);

        return response()->json($donor, 201);
    }

    /**
     * Muestra un donador registrado específico.
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $donor = RegisteredDonor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Registered Donor not found'], 404);
        }

        return response()->json($donor);
    }

    /**
     * Actualiza un donador registrado específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $donor = RegisteredDonor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Registered Donor not found'], 404);
        }

        $request->validate([
            'email_registereddonor' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('registereddonors', 'email_registereddonor')->ignore($id, 'id_registereddonor')],
            'username_registereddonor' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('registereddonors', 'username_registereddonor')->ignore($id, 'id_registereddonor')],
            'password_registereddonor' => 'nullable|string|min:8|max:256',
            'idupdater_user_registereddonor' => 'required|integer|exists:users,id_user',
        ]);

        $data = $request->except(['password_registereddonor']);

        if ($request->filled('password_registereddonor')) {
            $data['password_registereddonor'] = Hash::make($request->password_registereddonor);
        }

        $donor->update($data);

        return response()->json($donor);
    }

    /**
     * Elimina un donador registrado.
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $donor = RegisteredDonor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Registered Donor not found'], 404);
        }

        $donor->delete();

        return response()->json(['message' => 'Registered Donor deleted successfully'], 204);
    }
}
