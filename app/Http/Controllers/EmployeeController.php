<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /** Página principal */
    public function index()
    {
        $employees = Employee::with(['user', 'area', 'branch', 'state'])->get();

        return Inertia::render('settings/EmployeeIndex', [
            'employees' => $employees,
        ]);
    }

    /** Crear empleado */
    public function create()
    {
        return Inertia::render('settings/EmployeeForm', [
            // Usuarios disponibles para asignar
            'users' => User::whereDoesntHave('employee')
                ->get(['id', 'name']),

            'areas' => Area::all(['id_area', 'name_area']),
            'branches' => Branch::all(['id_branch', 'name_branch']),
            'states' => State::all(['id_state', 'name_state']),
            'departments' => Department::all(['id_department', 'name_department']),
            'municipalities' => Municipality::all([
                'id_municipality',
                'name_municipality',
                'id_department_municipality'
            ]),
        ]);
    }

    /** Guardar empleado */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cui_employee' => 'nullable|string|max:15|unique:employees,cui_employee',
            'firstname_employee' => 'required|string|max:30',
            'middlename_employee' => 'nullable|string|max:30',
            'thirdname_employee' => 'nullable|string|max:30',
            'lastname_employee' => 'required|string|max:30',
            'secondlastname_employee' => 'nullable|string|max:30',
            'thirdlastname_employee' => 'nullable|string|max:30',
            'profession_employee' => 'required|string|max:50',
            'phone_employee' => 'required|string|max:25',

            'id_user_employee' => 'required|integer|exists:users,id|unique:employees,id_user_employee',

            'birthdate_employee' => 'required|date',
            'idbirth_department_employee' => 'required|integer|exists:departments,id_department',
            'idbirth_municipality_employee' => 'required|integer|exists:municipalities,id_municipality',

            'age_employee' => 'required|integer|max:150',
            'id_department_employee' => 'required|integer|exists:departments,id_department',
            'id_municipality_employee' => 'required|integer|exists:municipalities,id_municipality',

            'address_employee' => 'required|string|max:255',
            'gender_employee' => 'required|string',
            'maritalstatus_employee' => 'required|string|max:20',

            'id_area_employee' => 'required|integer|exists:areas,id_area',
            'id_branch_employee' => 'required|integer|exists:branches,id_branch',
            'id_state_employee' => 'required|integer|exists:states,id_state',

            'idupdater_user_employee' => 'required|integer|exists:users,id',
        ]);

        $validatedData['idcreate_user_employee'] = $validatedData['idupdater_user_employee'];

        Employee::create($validatedData);

        return redirect()->route('employees.index')
            ->with('success', 'Empleado creado con éxito.');
    }

  public function edit(Employee $employee)
{
    $employee->load([
        'birthDepartment',
        'birthMunicipality',
        'locationDepartment',
        'locationMunicipality'
    ]);

    return Inertia::render('settings/EmployeeForm', [
        'employee' => $employee,

        'users' => User::select([
                'id as id_user',
                'name as user_name'
            ])->get(),

        'areas' => Area::all(['id_area', 'name_area']),
        'branches' => Branch::all(['id_branch', 'name_branch']),
        'states' => State::all(['id_state', 'name_state']),
        'departments' => Department::all(['id_department', 'name_department']),
        'municipalities' => Municipality::all([
            'id_municipality',
            'name_municipality',
            'id_department_municipality'
        ]),
    ]);
}


    /** Actualizar empleado */
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'cui_employee' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('employees', 'cui_employee')->ignore($employee->id_employee, 'id_employee')
            ],

            'firstname_employee' => 'sometimes|required|string|max:30',
            'middlename_employee' => 'nullable|string|max:30',
            'thirdname_employee' => 'nullable|string|max:30',

            'lastname_employee' => 'sometimes|required|string|max:30',
            'secondlastname_employee' => 'nullable|string|max:30',
            'thirdlastname_employee' => 'nullable|string|max:30',

            'profession_employee' => 'sometimes|required|string|max:50',
            'phone_employee' => 'sometimes|required|string|max:25',

            'id_user_employee' => [
                'sometimes',
                'required',
                'integer',
                Rule::unique('employees', 'id_user_employee')->ignore($employee->id_employee, 'id_employee')
            ],

            'birthdate_employee' => 'sometimes|required|date',
            'idbirth_department_employee' => 'sometimes|required|integer|exists:departments,id_department',
            'idbirth_municipality_employee' => 'sometimes|required|integer|exists:municipalities,id_municipality',

            'age_employee' => 'sometimes|required|integer|max:150',
            'id_department_employee' => 'sometimes|required|integer|exists:departments,id_department',
            'id_municipality_employee' => 'sometimes|required|integer|exists:municipalities,id_municipality',

            'address_employee' => 'sometimes|required|string|max:255',
            'gender_employee' => 'sometimes|required|string',
            'maritalstatus_employee' => 'sometimes|required|string|max:20',

            'id_area_employee' => 'sometimes|required|integer|exists:areas,id_area',
            'id_branch_employee' => 'sometimes|required|integer|exists:branches,id_branch',
            'id_state_employee' => 'sometimes|required|integer|exists:states,id_state',

            'idupdater_user_employee' => 'required|integer|exists:users,id',
        ]);

        $employee->update($validatedData);

        return redirect()->back()->with('success', 'Empleado actualizado con éxito.');
    }

    /** Eliminar empleado */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Empleado eliminado con éxito.');
    }
}
