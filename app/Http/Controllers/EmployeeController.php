// app/Http/Controllers/EmployeeController.php

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Muestra una lista de empleados con sus relaciones clave.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $employees = Employee::with(['user', 'area', 'branch', 'state'])->get();
        return response()->json($employees);
    }

    /**
     * Almacena un nuevo empleado.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'cui_employee' => 'nullable|string|max:15|unique:employees,cui_employee',
            'firstname_employee' => 'required|string|max:30',
            // ... (otras validaciones de nombres y datos personales)
            'profession_employee' => 'required|string',
            'phone_employee' => 'required|string|max:25',
            'id_user_employee' => 'required|integer|exists:users,id_user|unique:employees,id_user_employee', // Asegurar 1:1 con users
            'birthdate_employee' => 'required|date',
            'idbirth_department_employee' => 'required|integer|exists:departments,id_department',
            'idbirth_municipality_employee' => 'required|integer|exists:municipalities,id_municipality',
            'age_employee' => 'required|integer|max:150',
            'id_department_employee' => 'required|integer|exists:departments,id_department',
            'id_municipality_employee' => 'required|integer|exists:municipalities,id_municipality',
            'address_employee' => 'required|string|max:255',
            'gender_employee' => 'required|in:Masculino,Femenino,Otro',
            'maritalstatus_employee' => 'required|string|max:20',
            'id_area_employee' => 'required|integer|exists:areas,id_area',
            'id_branch_employee' => 'required|integer|exists:branches,id_branch',
            'id_state_employee' => 'required|integer|exists:states,id_state',
            'idupdater_user_employee' => 'required|integer|exists:users,id_user',
        ]);

        $employee = Employee::create([
            ...$request->except(['idcreate_user_employee']),
            'idcreate_user_employee' => $request->idupdater_user_employee,
        ]);

        return response()->json($employee->load(['user', 'area']), 201);
    }

    /**
     * Muestra un empleado específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $employee = Employee::with([
            'user', 'area', 'branch', 'state',
            'birthDepartment', 'birthMunicipality',
            'locationDepartment', 'locationMunicipality'
        ])->find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee);
    }

    /**
     * Actualiza un empleado específico.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $request->validate([
            'cui_employee' => ['nullable', 'string', 'max:15', Rule::unique('employees', 'cui_employee')->ignore($id, 'id_employee')],
            'id_user_employee' => ['sometimes', 'required', 'integer', Rule::unique('employees', 'id_user_employee')->ignore($id, 'id_employee')],
            'id_area_employee' => 'sometimes|required|integer|exists:areas,id_area',
            // ... (otras validaciones 'sometimes|required')
            'idupdater_user_employee' => 'required|integer|exists:users,id_user',
        ]);

        $employee->update($request->all());

        return response()->json($employee);
    }

    /**
     * Elimina un empleado específico (Usar cambio de estado es preferible).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully'], 204);
    }
}
