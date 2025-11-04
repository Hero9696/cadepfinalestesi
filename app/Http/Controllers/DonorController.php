<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // NECESARIO para generar el ID único

class DonorController extends Controller
{
    /**
     * Muestra una lista paginada de donantes y renderiza la vista de Inertia.
     */
    public function index(Request $request)
    {
        // Parámetros de filtro y búsqueda
        $search = $request->input('search');

        $donors = Donor::query()
            ->when($search, function ($query, $search) {
                // Filtro simple por nombre, apellido, email o ID
                $query->where('firstname_donor', 'like', '%' . $search . '%')
                      ->orWhere('lastname_donor', 'like', '%' . $search . '%')
                      ->orWhere('email_donor', 'like', '%' . $search . '%')
                      ->orWhere('id_donor', 'like', '%' . $search . '%');
            })
            // Asegúrate de que 'createdate_donor' existe si se usa para ordenar
            ->orderBy('createdate_donor', 'desc')
            ->paginate(10) // Paginar los resultados
            ->withQueryString() // Mantener los parámetros de la URL (incluido el 'search')
            ->through(fn ($donor) => [
                // DATOS AJUSTADOS PARA COINCIDIR CON LA INTERFAZ DonorItem DE VUE
                'id_donor' => $donor->id_donor,
                'email_donor' => $donor->email_donor,
                'firstname_donor' => $donor->firstname_donor,
                'lastname_donor' => $donor->lastname_donor,
                'country_donor' => $donor->country_donor,
                'city_donor' => $donor->city_donor,
                'phone_donor' => $donor->phone_donor,
            ]);

        // Renderiza la página de Inertia Donors/Index y pasa los datos
        return Inertia::render('settings/DonorIndex', [
            'donors' => $donors,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo donante.
     */
    public function create()
    {
        return Inertia::render('settings/DonorForm');
    }

    /**
     * Almacena un nuevo donante en la base de datos.
     */
    public function store(Request $request)
    {
        // NOTA: Se ha quitado la validación de 'id_donor' porque se genera en el servidor.
        $validated = $request->validate([
            'email_donor' => ['required', 'email', 'max:255', 'unique:donors,email_donor'],
            'title_donor' => ['nullable', 'string', 'max:10'],
            'firstname_donor' => ['required', 'string', 'max:255'],
            'lastname_donor' => ['required', 'string', 'max:255'],
            'country_donor' => ['required', 'string', 'max:255'],
            // Se asume que estos campos deben ser obligatorios si están en el formulario de Vue
            'zipcode_donor' => ['required', 'string', 'max:50'],
            'state_donor' => ['required', 'string', 'max:255'],
            'address_donor' => ['required', 'string', 'max:500'],
            'unit_donor' => ['nullable', 'string', 'max:255'],
            'city_donor' => ['required', 'string', 'max:255'],
            'phone_donor' => ['nullable', 'string', 'max:50'],
            'mobile_donor' => ['nullable', 'string', 'max:50'],
        ]);

        // Generar un ID único para la clave primaria (ya que no es autoincremental)
        // CORRECCIÓN: Se reemplaza Str::uuid() (36 caracteres) por una cadena más corta,
        // ya que el error indica que 36 caracteres son demasiado largos para la columna 'id_donor'.
        // IMPORTANTE: DEBES REEMPLAZAR ESTA LÓGICA CON TU GENERADOR DE IDS SECUENCIALES ('S1', 'S2', etc.).
        $newId = 'S-' . Str::random(8); // Genera algo como S-aB3dE7gH (10 caracteres)

        // Crear el donante
        $donor = Donor::create(array_merge($validated, [
            'id_donor' => $newId, // Asignar el ID generado
            // Asignar el ID del usuario que crea/actualiza (se asume autenticación)
            'idupdater_user_donor' => Auth::id() ?? 'SYSTEM',
        ]));

        // Redirigir a la lista de donantes con un mensaje flash
        return Redirect::route('donors.index')->with('success', 'Donante ' . $donor->id_donor . ' creado exitosamente.');
    }

    /**
     * Muestra los detalles de un donante específico.
     */
    public function show(Donor $donor)
    {
        // Cargar las relaciones necesarias (ej: donaciones)
        $donor->load('donations');

        return Inertia::render('settings/DonorShow', [
            'donor' => $donor,
        ]);
    }

    /**
     * Muestra el formulario para editar un donante existente.
     */
    public function edit(Donor $donor)
    {
        return Inertia::render('settings/DonorForm', [
            'donor' => $donor,
        ]);
    }

    /**
     * Actualiza un donante específico en la base de datos.
     */
    public function update(Request $request, Donor $donor)
    {
        // NOTA: Se ha quitado la validación de 'id_donor' ya que es la clave primaria.
        $validated = $request->validate([
            // Email debe ser único, excepto para el donante actual
            'email_donor' => ['required', 'email', 'max:255', Rule::unique('donors', 'email_donor')->ignore($donor->id_donor, 'id_donor')],
            'title_donor' => ['nullable', 'string', 'max:10'],
            'firstname_donor' => ['required', 'string', 'max:255'],
            'lastname_donor' => ['required', 'string', 'max:255'],
            'country_donor' => ['required', 'string', 'max:255'],
            'zipcode_donor' => ['required', 'string', 'max:50'],
            'state_donor' => ['required', 'string', 'max:255'],
            'address_donor' => ['required', 'string', 'max:500'],
            'unit_donor' => ['nullable', 'string', 'max:255'],
            'city_donor' => ['required', 'string', 'max:255'],
            'phone_donor' => ['nullable', 'string', 'max:50'],
            'mobile_donor' => ['nullable', 'string', 'max:50'],
        ]);

        // Actualizar el donante con los datos validados y el ID del actualizador
        $donor->update(array_merge($validated, [
            'idupdater_user_donor' => Auth::id() ?? 'SYSTEM',
        ]));

        // Redirigir de vuelta a la página de edición con un mensaje flash
        return Redirect::back()->with('success', 'Donante ' . $donor->id_donor . ' actualizado exitosamente.');
    }

    /**
     * Elimina un donante de la base de datos.
     */
    public function destroy(Donor $donor)
    {
        $donorId = $donor->id_donor;

        // Eliminar el donante
        $donor->delete();

        // Redirigir a la lista de donantes con un mensaje flash
        return Redirect::route('donors.index')->with('success', 'Donante ' . $donorId . ' eliminado exitosamente.');
    }
}
