// app/Http/Controllers/ArchiveController.php

<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArchiveController extends Controller
{
    /**
     * Muestra una lista de archivos.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $archives = Archive::with('patient')->get();
        return response()->json($archives);
    }

    /**
     * Almacena un nuevo archivo (expediente).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_patient_archive' => 'required|integer|exists:patients,id_patient|unique:archives,id_patient_archive', // 1:1 con Patient
            'idupdater_user_archive' => 'required|integer|exists:users,id_user',
        ]);

        $archive = Archive::create([
            'id_patient_archive' => $request->id_patient_archive,
            'idcreate_user_archive' => $request->idupdater_user_archive,
            'idupdater_user_archive' => $request->idupdater_user_archive,
        ]);

        return response()->json($archive->load('patient'), 201);
    }

    /**
     * Muestra un archivo específico y sus reportes.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $archive = Archive::with(['patient', 'reports'])->find($id);

        if (!$archive) {
            return response()->json(['message' => 'Archive not found'], 404);
        }

        return response()->json($archive);
    }

    /**
     * Actualiza un archivo específico (limitado, ya que el ID de paciente es único).
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $archive = Archive::find($id);

        if (!$archive) {
            return response()->json(['message' => 'Archive not found'], 404);
        }

        $request->validate([
            // Solo actualiza el usuario modificador
            'idupdater_user_archive' => 'required|integer|exists:users,id_user',
        ]);

        $archive->update(['idupdater_user_archive' => $request->idupdater_user_archive]);

        return response()->json($archive);
    }

    /**
     * Elimina un archivo específico (solo si el paciente se elimina, o cambiar estado de paciente).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $archive = Archive::find($id);

        if (!$archive) {
            return response()->json(['message' => 'Archive not found'], 404);
        }

        $archive->delete();

        return response()->json(['message' => 'Archive deleted successfully'], 204);
    }
}
