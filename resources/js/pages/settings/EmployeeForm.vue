<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS DE PROPS ---
interface Department { id_department: number; name_department: string; }
interface Municipality { id_municipality: number; name_municipality: string; id_department_municipality: number; }
interface State { id_state: number; name_state: string; }

interface EmployeeProp {
    id_employee: number;
    cui_employee: string;
    firstname_employee: string;
    lastname_employee: string;
    profession_employee: string;
    phone_employee: string;
    birthdate_employee: string; // YYYY-MM-DD
    gender_employee: string;
    maritalstatus_employee: string;
    address_employee: string;
    age_employee: number;
    // IDs
    id_user_employee: number;
    idbirth_department_employee: number;
    idbirth_municipality_employee: number;
    id_department_employee: number; // Residencia
    id_municipality_employee: number; // Residencia
    id_area_employee: number;
    id_branch_employee: number;
    id_state_employee: number;
}

interface UserDropdown {
    id: number;
    name: string;
}

interface PageProps extends InertiaBasePageProps {
    employee?: EmployeeProp;
    users: { id_user: number, user_name: string }[];
    departments: Department[];
    municipalities: Municipality[];
    areas: { id_area: number, name_area: string }[];
    branches: { id_branch: number, name_branch: string }[];
    states: State[];
    errors?: Record<string, string>;
    auth: { user: { id_user: number } }; // La clave que Inertia recibe
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const employeeProp = page.props.employee;
const users = page.props.users;
const departments = page.props.departments;
const allMunicipalities = page.props.municipalities;
const areas = page.props.areas;
const branches = page.props.branches;
const states = page.props.states;

const isEdit = computed(() => !!employeeProp);

// --- HELPER DE FECHA: Asegura el formato YYYY-MM-DD para input[type="date"] ---
const formatDateForInput = (dateString: string | undefined): string => {
    if (!dateString) return '';
    return dateString.substring(0, 10);
};


// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Empleados', href: route('employees.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    // Sección Personal
    cui_employee: employeeProp?.cui_employee || '',
    firstname_employee: employeeProp?.firstname_employee || '',
    lastname_employee: employeeProp?.lastname_employee || '',
    profession_employee: employeeProp?.profession_employee || '',
    phone_employee: employeeProp?.phone_employee || '',
    birthdate_employee: formatDateForInput(employeeProp?.birthdate_employee),
    gender_employee: employeeProp?.gender_employee || '',
    maritalstatus_employee: employeeProp?.maritalstatus_employee || '',
    address_employee: employeeProp?.address_employee || '',
    age_employee: employeeProp?.age_employee || 0,

    // Seccion Ubicacion (Nacimiento)
    idbirth_department_employee: employeeProp?.idbirth_department_employee || '',
    idbirth_municipality_employee: employeeProp?.idbirth_municipality_employee || '',

    // Seccion Ubicacion (Residencia)
    id_department_employee: employeeProp?.id_department_employee || '',
    id_municipality_employee: employeeProp?.id_municipality_employee || '',

    // Seccion Organizacional
    // CORRECCIÓN CLAVE: Pasamos el ID a STRING para que el v-model lo seleccione correctamente.
    id_user_employee: employeeProp?.id_user_employee?.toString() || '',
    id_area_employee: employeeProp?.id_area_employee || '',
    id_branch_employee: employeeProp?.id_branch_employee || '',
    id_state_employee: employeeProp?.id_state_employee || '',

    // Auditoría
    idupdater_user_employee: page.props.auth.user.id,
});

// Calculamos la edad o la pasamos por el backend si es necesario
const calculatedAge = computed(() => {
    if (form.birthdate_employee) {
        const birthDate = new Date(form.birthdate_employee);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }
    return 0;
});

// Watcher para sincronizar la edad calculada con el campo del formulario (necesario para la validación)
watch(calculatedAge, (newAge) => {
    form.age_employee = newAge;
}, { immediate: true });


// Filtros de Municipios
const filteredBirthMunicipalities = computed(() => {
    // Aseguramos que el valor no sea una cadena vacía ('') antes de comparar con el número.
    const deptId = form.idbirth_department_employee ? Number(form.idbirth_department_employee) : null;
    if (!deptId) return [];
    return allMunicipalities.filter(m => m.id_department_municipality === deptId);
});
const filteredLocationMunicipalities = computed(() => {
    const deptId = form.id_department_employee ? Number(form.id_department_employee) : null;
    if (!deptId) return [];
    return allMunicipalities.filter(m => m.id_department_municipality === deptId);
});

// Reseteo de municipios al cambiar departamento
watch(() => form.idbirth_department_employee, () => {
    form.idbirth_municipality_employee = '';
});
watch(() => form.id_department_employee, () => {
    form.id_municipality_employee = '';
});


// --- LÓGICA DE GUARDADO ---
const saveEmployee = () => {

    const url = isEdit.value
        ? route('employees.update', { employee: employeeProp!.id_employee }) // <-- CORREGIDO: usar 'employee'
        : route('employees.store');

    // Asignamos la edad calculada directamente antes del envío.
    form.age_employee = calculatedAge.value;

    // CORRECCIÓN FINAL: Eliminamos la conversión manual, ya que el valor en form.id_user_employee es una cadena
    // que el v-model está usando. Laravel es capaz de convertir la cadena a int.
    // Solo necesitamos asegurarnos de que la URL sea correcta.

    if (isEdit.value) {
        // PUT para edición
        form.put(url, {
            onBefore: (visit) => {
                console.log('--- ENVIANDO PUT (EDITAR) ---');
                console.log('URL:', visit.url);
                console.log('MÉTODO:', visit.method);
                console.log('PAYLOAD (Form Data):', form.data());
            },
            onSuccess: () => router.visit(route('employees.index')),
        });
    } else {
        // POST para creación
        form.post(url, {
            onBefore: (visit) => {
                console.log('--- ENVIANDO POST (CREAR) ---');
                console.log('URL:', visit.url);
                console.log('MÉTODO:', visit.method);
                console.log('PAYLOAD (Form Data):', form.data());
            },
            onSuccess: () => router.visit(route('employees.index')),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Empleado' : 'Crear Empleado'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-4xl">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Empleado' : 'Registrar Nuevo Empleado' }}
            </h2>

            <form @submit.prevent="saveEmployee">

                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Datos Personales</h3>
                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <!-- CUI/ID -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="cui_employee">CUI/ID</label>
                        <input v-model="form.cui_employee" type="text" id="cui_employee"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.cui_employee }" />
                        <p v-if="form.errors.cui_employee" class="text-red-500 text-xs italic">{{ form.errors.cui_employee }}</p>
                    </div>

                    <!-- Nombres -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="firstname_employee">Nombres</label>
                        <input v-model="form.firstname_employee" type="text" id="firstname_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.firstname_employee }" />
                        <p v-if="form.errors.firstname_employee" class="text-red-500 text-xs italic">{{ form.errors.firstname_employee }}</p>
                    </div>

                    <!-- Apellidos -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="lastname_employee">Apellidos</label>
                        <input v-model="form.lastname_employee" type="text" id="lastname_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.lastname_employee }" />
                        <p v-if="form.errors.lastname_employee" class="text-red-500 text-xs italic">{{ form.errors.lastname_employee }}</p>
                    </div>

                    <!-- F. Nacimiento -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="birthdate_employee">F. Nacimiento</label>
                        <input v-model="form.birthdate_employee" type="date" id="birthdate_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.birthdate_employee }" />
                        <p v-if="form.errors.birthdate_employee" class="text-red-500 text-xs italic">{{ form.errors.birthdate_employee }}</p>
                    </div>

                    <!-- Género -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="gender_employee">Género</label>
                        <select v-model="form.gender_employee" id="gender_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="" disabled>Seleccionar</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <p v-if="form.errors.gender_employee" class="text-red-500 text-xs italic">{{ form.errors.gender_employee }}</p>
                    </div>

                    <!-- Estado Civil -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="maritalstatus_employee">Estado Civil</label>
                        <select v-model="form.maritalstatus_employee" id="maritalstatus_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="" disabled>Seleccionar</option>
                            <option value="Soltero">Soltero</option>
                            <option value="Casado">Casado</option>
                            <option value="Divorciado">Divorciado</option>
                            <option value="Viudo">Viudo</option>
                        </select>
                        <p v-if="form.errors.maritalstatus_employee" class="text-red-500 text-xs italic">{{ form.errors.maritalstatus_employee }}</p>
                    </div>

                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Lugar de Nacimiento</h3>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <!-- Departamento de Nacimiento -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="idbirth_department_employee">Departamento de Nacimiento</label>
                        <select v-model="form.idbirth_department_employee" id="idbirth_department_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                                {{ dept.name_department }}
                            </option>
                        </select>
                        <p v-if="form.errors.idbirth_department_employee" class="text-red-500 text-xs italic">{{ form.errors.idbirth_department_employee }}</p>
                    </div>

                    <!-- Municipio de Nacimiento -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="idbirth_municipality_employee">Municipio de Nacimiento</label>
                        <select v-model="form.idbirth_municipality_employee" id="idbirth_municipality_employee" required
                            :disabled="!form.idbirth_department_employee"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 disabled:bg-gray-100">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="muni in filteredBirthMunicipalities" :key="muni.id_municipality" :value="muni.id_municipality">
                                {{ muni.name_municipality }}
                            </option>
                        </select>
                        <p v-if="form.errors.idbirth_municipality_employee" class="text-red-500 text-xs italic">{{ form.errors.idbirth_municipality_employee }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Residencia y Contacto</h3>
                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <!-- Departamento Residencia -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_department_employee">Departamento Residencia</label>
                        <select v-model="form.id_department_employee" id="id_department_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                                {{ dept.name_department }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_department_employee" class="text-red-500 text-xs italic">{{ form.errors.id_department_employee }}</p>
                    </div>

                    <!-- Municipio Residencia -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_municipality_employee">Municipio Residencia</label>
                        <select v-model="form.id_municipality_employee" id="id_municipality_employee" required
                            :disabled="!form.id_department_employee"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 disabled:bg-gray-100">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="muni in filteredLocationMunicipalities" :key="muni.id_municipality" :value="muni.id_municipality">
                                {{ muni.name_municipality }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_municipality_employee" class="text-red-500 text-xs italic">{{ form.errors.id_municipality_employee }}</p>
                    </div>

                    <!-- Teléfono -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="phone_employee">Teléfono</label>
                        <input v-model="form.phone_employee" type="text" id="phone_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.phone_employee }" />
                        <p v-if="form.errors.phone_employee" class="text-red-500 text-xs italic">{{ form.errors.phone_employee }}</p>
                    </div>

                    <!-- Dirección Completa -->
                    <div class="grid gap-2 col-span-3">
                        <label class="block text-gray-700 text-sm font-bold" for="address_employee">Dirección Completa</label>
                        <textarea v-model="form.address_employee" id="address_employee" rows="2" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.address_employee }" />
                        <p v-if="form.errors.address_employee" class="text-red-500 text-xs italic">{{ form.errors.address_employee }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Datos de Trabajo</h3>
                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <!-- Cuenta de Usuario -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_user_employee">Cuenta de Usuario</label>
                        <select v-model="form.id_user_employee" id="id_user_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_user_employee }">
                            <option value="" disabled>Seleccionar Cuenta</option>
                            <!-- La lista de usuarios disponibles se llama 'users' -->
                            <option v-for="user in users" :key="user.id_user" :value="user.id_user">
                                {{ user.user_name }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_user_employee" class="text-red-500 text-xs italic">{{ form.errors.id_user_employee }}</p>
                    </div>

                    <!-- Profesión/Título -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="profession_employee">Profesión/Título</label>
                        <input v-model="form.profession_employee" type="text" id="profession_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.profession_employee }" />
                        <p v-if="form.errors.profession_employee" class="text-red-500 text-xs italic">{{ form.errors.profession_employee }}</p>
                    </div>

                    <!-- Área de Trabajo -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_area_employee">Área de Trabajo</label>
                        <select v-model="form.id_area_employee" id="id_area_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_area_employee }">
                            <option value="" disabled>Seleccionar Área</option>
                            <option v-for="area in areas" :key="area.id_area" :value="area.id_area">
                                {{ area.name_area }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_area_employee" class="text-red-500 text-xs italic">{{ form.errors.id_area_employee }}</p>
                    </div>

                    <!-- Sucursal -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_branch_employee">Sucursal</label>
                        <select v-model="form.id_branch_employee" id="id_branch_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_branch_employee }">
                            <option value="" disabled>Seleccionar Sucursal</option>
                            <option v-for="branch in branches" :key="branch.id_branch" :value="branch.id_branch">
                                {{ branch.name_branch }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_branch_employee" class="text-red-500 text-xs italic">{{ form.errors.id_branch_employee }}</p>
                    </div>

                    <!-- Estado Laboral -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_state_employee">Estado Laboral</label>
                        <select v-model="form.id_state_employee" id="id_state_employee" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_state_employee }">
                            <option value="" disabled>Seleccionar Estado</option>
                            <option v-for="state in states" :key="state.id_state" :value="state.id_state">
                                {{ state.name_state }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_state_employee" class="text-red-500 text-xs italic">{{ form.errors.id_state_employee }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ isEdit ? 'Actualizar' : 'Guardar' }}
                    </button>
                    <button
                        type="button"
                        @click="router.visit(route('employees.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
