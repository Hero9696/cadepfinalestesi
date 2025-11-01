// app/Http/Controllers/AppointmentReportController.php

<?php

namespace App\Http\Controllers;

use App\Models\AppointmentReport;
use Illuminate\Http\Request;

class AppointmentReportController extends Controller
{
    /**
     * Muestra una lista de reportes de citas.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $reports = AppointmentReport::with(['archive.patient', 'appointment.employee'])->get();
        return response()->json($reports);
    }

    /**
     * Almacena un nuevo reporte de cita.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_archive_appointmentreport' => 'required|integer|exists:archives,id_archive',
            'id_appointment_appointmentreport' => 'required|integer|exists:appointments,id_appointment|unique:appointmentreports,id_appointment_appointmentreport', // 1:1
            'objective_appointmentreport' => 'nullable|string|max:100',
            'observations_appointmentreport' => 'nullable|string',
            'height_appointmentreport' => 'nullable|numeric|max:99.99',
            'weight_appointmentreport' => 'nullable|numeric|max:999.99',
            'idupdater_user_appointmentreport' => 'required|integer|exists:users,id_user',
        ]);

        $report = AppointmentReport::create([
            ...$request->except(['idcreate_user_appointmentreport']),
            'idcreate_user_appointmentreport' => $request->idupdater_user_appointmentreport,
        ]);

        return response()->json($report, 201);
    }

    /**
     * Muestra un reporte de cita específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $report = AppointmentReport::with(['archive.patient', 'appointment.employee'])->find($id);

        if (!$report) {
            return response()->json(['message' => 'Appointment Report not found'], 404);
        }

        return response()->json($report);
    }

    /**
     * Actualiza un reporte de cita específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $report = AppointmentReport::find($id);

        if (!$report) {
            return response()->json(['message' => 'Appointment Report not found'], 404);
        }

        $request->validate([
            'objective_appointmentreport' => 'sometimes|nullable|string|max:100',
            'observations_appointmentreport' => 'sometimes|nullable|string',
            'height_appointmentreport' => 'sometimes|nullable|numeric|max:99.99',
            'weight_appointmentreport' => 'sometimes|nullable|numeric|max:999.99',
            'idupdater_user_appointmentreport' => 'required|integer|exists:users,id_user',
        ]);

        $report->update($request->all());

        return response()->json($report);
    }

    /**
     * Elimina un reporte de cita específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $report = AppointmentReport::find($id);

        if (!$report) {
            return response()->json(['message' => 'Appointment Report not found'], 404);
        }

        $report->delete();

        return response()->json(['message' => 'Appointment Report deleted successfully'], 204);
    }
}
