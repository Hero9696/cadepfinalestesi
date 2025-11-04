<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, watch, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS ---
interface SelectOption { id: number; name: string; }
interface Department { id_department: number; name_department: string; }
interface Municipality { id_municipality: number; name_municipality: string; id_department_municipality: number; }
interface State { id_state: number; name_state: string; }
interface Branch { id_branch: number; name_branch: string; }
interface Area { id_area: number; name_area: string; }


interface PatientProp {
    id_patient: number; cui_patient: string; firstname_patient: string; middlename_patient: string;
    thirdname_patient: string;
    lastname_patient: string; secondlastname_patient: string; thirdlastname_patient: string;
    birthdate_patient: string; idbirth_department_patient: number; idbirth_municipality_patient: number;
    weight_patient: number; schooling_patient: string; phone_patient: string;
    id_department_patient: number; id_municipality_patient: number; address_patient: string;
    gender_patient: string; religion_patient: string; maritalstatus_patient: string;
    dependentfamily_patient: boolean; reasonforconsultation_patient: string;
    referreddoctor_patient: string;
    // AGREGADOS: Campos faltantes de la DB
    photo_url_patient?: string;
    status_patient?: boolean | number; // Asumimos que es un booleano o tinyint (0/1)
    reserved_until_patient?: string;
    idcreate_user_patient?: number; // Para edición si necesitas saber quién lo creó

    id_branch_patient: number; id_state_patient: number;
    age_patient: number;
    // Auditoría
    idupdater_user_patient: number;
}

interface PageProps extends InertiaBasePageProps {
    patient?: PatientProp;
    departments: Department[];
    municipalities: Municipality[];
    states: State[];
    branches: Branch[];
    areas: Area[];
    errors?: Record<string, string>;
    auth: { user: { id: number } };
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const patientProp = page.props.patient;
const departments = page.props.departments;
const allMunicipalities = page.props.municipalities;
const states = page.props.states;
const branches = page.props.branches;

const isEdit = computed(() => !!patientProp);

// Helper para formatear fecha (ej: '2025-01-01 00:00:00' a '2025-01-01')
const formatDateForInput = (dateString: string | undefined): string => {
    if (!dateString) return '';
    return dateString.substring(0, 10); // Solo YYYY-MM-DD
};

const genderOptions: SelectOption[] = [
    { id: 1, name: 'Masculino' },
    { id: 2, name: 'Femenino' },
    { id: 3, name: 'Otro' },
];

const maritalStatusOptions: SelectOption[] = [
    { id: 1, name: 'Soltero(a)' },
    { id: 2, name: 'Casado(a)' },
    { id: 3, name: 'Unido(a)' },
    { id: 4, name: 'Divorciado(a)' },
    { id: 5, name: 'Viudo(a)' },
];

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Pacientes', href: route('patients.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    // Sección 1: Identificación
    id_patient: patientProp?.id_patient || null,
    cui_patient: patientProp?.cui_patient || '',
    firstname_patient: patientProp?.firstname_patient || '',
    middlename_patient: patientProp?.middlename_patient || '',
    thirdname_patient: patientProp?.thirdname_patient || '',
    lastname_patient: patientProp?.lastname_patient || '',
    secondlastname_patient: patientProp?.secondlastname_patient || '',
    thirdlastname_patient: patientProp?.thirdlastname_patient || '',

    // Sección 2: Datos Vitales / Sociales
    birthdate_patient: formatDateForInput(patientProp?.birthdate_patient),
    weight_patient: patientProp?.weight_patient || '',
    schooling_patient: patientProp?.schooling_patient || '',
    gender_patient: patientProp?.gender_patient || "",
    religion_patient: patientProp?.religion_patient || '',
    maritalstatus_patient: patientProp?.maritalstatus_patient || "",
    dependentfamily_patient: patientProp?.dependentfamily_patient ?? false,

    // Sección 3: Contacto / Consulta
    phone_patient: patientProp?.phone_patient || '',
    address_patient: patientProp?.address_patient || '',
    reasonforconsultation_patient: patientProp?.reasonforconsultation_patient || '',
    referreddoctor_patient: patientProp?.referreddoctor_patient || '',

    // **AGREGADO:** URL de la foto
    photo_url_patient: patientProp?.photo_url_patient || '',

    // **AGREGADO:** Fecha de reserva
    reserved_until_patient: formatDateForInput(patientProp?.reserved_until_patient),

    // Sección 4: Ubicación (Nacimiento)
    idbirth_department_patient: patientProp?.idbirth_department_patient || "",
    idbirth_municipality_patient: patientProp?.idbirth_municipality_patient || "",

    // Sección 5: Ubicación (Residencia)
    id_department_patient: patientProp?.id_department_patient || "",
    id_municipality_patient: patientProp?.id_municipality_patient || "",

    // Sección 6: Organizacional / Auditoría
    id_branch_patient: patientProp?.id_branch_patient || "",
    id_state_patient: patientProp?.id_state_patient || (states[0]?.id_state ?? ""),

    // **AGREGADO/MEJORADO:** Campo de estado y auditoría
    status_patient: patientProp?.status_patient ?? true, // Asume 'true' si es nuevo.
    idcreate_user_patient: page.props.auth.user?.id ?? 1, // Si no existe (creación), usa el ID del usuario actual.

    // Campo que el controlador requiere para validación
    age_patient: patientProp?.age_patient || 0,
    idupdater_user_patient: page.props.auth.user?.id ?? 1, // Siempre se envía el ID del usuario actual.
});

// --- LÓGICA DE EDAD y MUNICIPALIDADES (SE MANTIENE IGUAL) ---
const calculateAge = (dateString: string | undefined): number => {
    if (!dateString) return 0;
    const birthDate = new Date(dateString);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
};

const filteredBirthMunicipalities = computed(() => {
    const deptId = form.idbirth_department_patient ? Number(form.idbirth_department_patient) : null;
    if (!deptId) return [];
    return allMunicipalities.filter(m => m.id_department_municipality === deptId);
});
const filteredLocationMunicipalities = computed(() => {
    const deptId = form.id_department_patient ? Number(form.id_department_patient) : null;
    if (!deptId) return [];
    return allMunicipalities.filter(m => m.id_department_municipality === deptId);
});

watch(() => form.idbirth_department_patient, () => {
    form.idbirth_municipality_patient = '';
}, { deep: true });
watch(() => form.id_department_patient, () => {
    form.id_municipality_patient = '';
}, { deep: true });


// --- LÓGICA DE GUARDADO ---

const savePatient = () => {
    // --- PASO CLAVE: CALCULAR LA EDAD JUSTO ANTES DE ENVIAR ---
    form.age_patient = calculateAge(form.birthdate_patient);
    const url = isEdit.value
        ? route('patients.update', { patient: patientProp!.id_patient }) // Parámetro 'patient'
        : route('patients.store');

    if (isEdit.value) {
        // PUT para edición
        form.put(url, {
            onBefore: (visit) => {
                console.log('--- ENVIANDO PUT (EDITAR) ---');
                console.log('PAYLOAD (Form Data):', form.data());
            },

            onSuccess: () => router.visit(route('patients.index')),

        });

    } else {

        // POST para creación

        form.post(url, {

            onBefore: (visit) => {

                console.log('--- ENVIANDO POST (CREAR) ---');

                console.log('PAYLOAD (Form Data):', form.data());

            },

            onSuccess: () => router.visit(route('patients.index')),

        });

    }

};
</script>
<template>
    <Head :title="isEdit ? 'Editar Paciente' : 'Crear Paciente'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-5xl">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Expediente de Paciente' : 'Registro de Nuevo Paciente' }}
            </h2>

            <form @submit.prevent="savePatient">

                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Identificación y Nombres</h3>
                <div class="grid md:grid-cols-4 gap-4 mb-4">

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_patient">ID Paciente</label>
                        <input v-model.number="form.id_patient" type="number" id="id_patient" :required="!isEdit" :disabled="isEdit"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 disabled:bg-gray-100" />
                        <p v-if="form.errors.id_patient" class="text-red-500 text-xs italic">{{ form.errors.id_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="cui_patient">CUI/ID</label>
                        <input v-model="form.cui_patient" type="text" id="cui_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.cui_patient }" />
                        <p v-if="form.errors.cui_patient" class="text-red-500 text-xs italic">{{ form.errors.cui_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="firstname_patient">Primer Nombre</label>
                        <input v-model="form.firstname_patient" type="text" id="firstname_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.firstname_patient }" />
                        <p v-if="form.errors.firstname_patient" class="text-red-500 text-xs italic">{{ form.errors.firstname_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="lastname_patient">Primer Apellido</label>
                        <input v-model="form.lastname_patient" type="text" id="lastname_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.lastname_patient }" />
                        <p v-if="form.errors.lastname_patient" class="text-red-500 text-xs italic">{{ form.errors.lastname_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="middlename_patient">Segundo Nombre</label>
                        <input v-model="form.middlename_patient" type="text" id="middlename_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.middlename_patient }" />
                        <p v-if="form.errors.middlename_patient" class="text-red-500 text-xs italic">{{ form.errors.middlename_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="secondlastname_patient">Segundo Apellido</label>
                        <input v-model="form.secondlastname_patient" type="text" id="secondlastname_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.secondlastname_patient }" />
                        <p v-if="form.errors.secondlastname_patient" class="text-red-500 text-xs italic">{{ form.errors.secondlastname_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="thirdname_patient">Tercer Nombre</label>
                        <input v-model="form.thirdname_patient" type="text" id="thirdname_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.thirdname_patient }" />
                        <p v-if="form.errors.thirdname_patient" class="text-red-500 text-xs italic">{{ form.errors.thirdname_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="thirdlastname_patient">Tercer Apellido</label>
                        <input v-model="form.thirdlastname_patient" type="text" id="thirdlastname_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.thirdlastname_patient }" />
                        <p v-if="form.errors.thirdlastname_patient" class="text-red-500 text-xs italic">{{ form.errors.thirdlastname_patient }}</p>
                    </div>

                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Datos Vitales y Sociales</h3>
                <div class="grid md:grid-cols-4 gap-4 mb-4">

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="birthdate_patient">Fecha de Nacimiento</label>
                        <input v-model="form.birthdate_patient" type="date" id="birthdate_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.birthdate_patient }" />
                        <p v-if="form.errors.birthdate_patient" class="text-red-500 text-xs italic">{{ form.errors.birthdate_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="gender_patient">Género</label>
                        <select v-model="form.gender_patient" id="gender_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.gender_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="option in genderOptions" :key="option.id" :value="option.name">
                                {{ option.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.gender_patient" class="text-red-500 text-xs italic">{{ form.errors.gender_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="maritalstatus_patient">Estado Civil</label>
                        <select v-model="form.maritalstatus_patient" id="maritalstatus_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.maritalstatus_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="option in maritalStatusOptions" :key="option.id" :value="option.name">
                                {{ option.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.maritalstatus_patient" class="text-red-500 text-xs italic">{{ form.errors.maritalstatus_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="weight_patient">Peso (Kg)</label>
                        <input v-model.number="form.weight_patient" type="number" step="0.01" id="weight_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.weight_patient }" />
                        <p v-if="form.errors.weight_patient" class="text-red-500 text-xs italic">{{ form.errors.weight_patient }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="schooling_patient">Escolaridad</label>
                        <input v-model="form.schooling_patient" type="text" id="schooling_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.schooling_patient }" />
                        <p v-if="form.errors.schooling_patient" class="text-red-500 text-xs italic">{{ form.errors.schooling_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="religion_patient">Religión</label>
                        <input v-model="form.religion_patient" type="text" id="religion_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.religion_patient }" />
                        <p v-if="form.errors.religion_patient" class="text-red-500 text-xs italic">{{ form.errors.religion_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="dependentfamily_patient">Familiares Dependientes</label>
                        <select v-model="form.dependentfamily_patient" id="dependentfamily_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.dependentfamily_patient }">
                            <option :value="true">Sí</option>
                            <option :value="false">No</option>
                        </select>
                        <p v-if="form.errors.dependentfamily_patient" class="text-red-500 text-xs italic">{{ form.errors.dependentfamily_patient }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Ubicación de Nacimiento</h3>
                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="idbirth_department_patient">Departamento Nacimiento</label>
                        <select v-model="form.idbirth_department_patient" id="idbirth_department_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.idbirth_department_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                                {{ dept.name_department }}
                            </option>
                        </select>
                        <p v-if="form.errors.idbirth_department_patient" class="text-red-500 text-xs italic">{{ form.errors.idbirth_department_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="idbirth_municipality_patient">Municipio Nacimiento</label>
                        <select v-model="form.idbirth_municipality_patient" id="idbirth_municipality_patient" required
                            :disabled="!form.idbirth_department_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 disabled:bg-gray-100" :class="{ 'border-red-500': form.errors.idbirth_municipality_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="muni in filteredBirthMunicipalities" :key="muni.id_municipality" :value="muni.id_municipality">
                                {{ muni.name_municipality }}
                            </option>
                        </select>
                        <p v-if="form.errors.idbirth_municipality_patient" class="text-red-500 text-xs italic">{{ form.errors.idbirth_municipality_patient }}</p>
                    </div>

                    <div class="hidden md:block"></div>
                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Contacto y Residencia</h3>
                <div class="grid md:grid-cols-4 gap-4 mb-4">

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_department_patient">Departamento Residencia</label>
                        <select v-model="form.id_department_patient" id="id_department_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.id_department_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                                {{ dept.name_department }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_department_patient" class="text-red-500 text-xs italic">{{ form.errors.id_department_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_municipality_patient">Municipio Residencia</label>
                        <select v-model="form.id_municipality_patient" id="id_municipality_patient" required
                            :disabled="!form.id_department_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 disabled:bg-gray-100" :class="{ 'border-red-500': form.errors.id_municipality_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="muni in filteredLocationMunicipalities" :key="muni.id_municipality" :value="muni.id_municipality">
                                {{ muni.name_municipality }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_municipality_patient" class="text-red-500 text-xs italic">{{ form.errors.id_municipality_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="phone_patient">Teléfono</label>
                        <input v-model="form.phone_patient" type="text" id="phone_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.phone_patient }" />
                        <p v-if="form.errors.phone_patient" class="text-red-500 text-xs italic">{{ form.errors.phone_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="referreddoctor_patient">Doctor Referido</label>
                        <input v-model="form.referreddoctor_patient" type="text" id="referreddoctor_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.referreddoctor_patient }" />
                        <p v-if="form.errors.referreddoctor_patient" class="text-red-500 text-xs italic">{{ form.errors.referreddoctor_patient }}</p>
                    </div>

                    <div class="grid gap-2 col-span-2">
                        <label class="block text-gray-700 text-sm font-bold" for="address_patient">Dirección Completa</label>
                        <textarea v-model="form.address_patient" id="address_patient" rows="2" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.address_patient }" />
                        <p v-if="form.errors.address_patient" class="text-red-500 text-xs italic">{{ form.errors.address_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="photo_url_patient">URL de Foto (opcional)</label>
                        <input v-model="form.photo_url_patient" type="text" id="photo_url_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.photo_url_patient }" />
                        <p v-if="form.errors.photo_url_patient" class="text-red-500 text-xs italic">{{ form.errors.photo_url_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="reserved_until_patient">Reservado Hasta (opcional)</label>
                        <input v-model="form.reserved_until_patient" type="date" id="reserved_until_patient"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.reserved_until_patient }" />
                        <p v-if="form.errors.reserved_until_patient" class="text-red-500 text-xs italic">{{ form.errors.reserved_until_patient }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Datos Organizacionales y Consulta</h3>
                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_branch_patient">Sucursal Asignada</label>
                        <select v-model.number="form.id_branch_patient" id="id_branch_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.id_branch_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="branch in branches" :key="branch.id_branch" :value="branch.id_branch">
                                {{ branch.name_branch }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_branch_patient" class="text-red-500 text-xs italic">{{ form.errors.id_branch_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_state_patient">Estado del Paciente</label>
                        <select v-model.number="form.id_state_patient" id="id_state_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.id_state_patient }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="state in states" :key="state.id_state" :value="state.id_state">
                                {{ state.name_state }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_state_patient" class="text-red-500 text-xs italic">{{ form.errors.id_state_patient }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="status_patient">¿Activo?</label>
                        <select v-model="form.status_patient" id="status_patient" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.status_patient }">
                            <option :value="true">Sí</option>
                            <option :value="false">No</option>
                        </select>
                        <p v-if="form.errors.status_patient" class="text-red-500 text-xs italic">{{ form.errors.status_patient }}</p>
                    </div>

                    <div class="grid gap-2 col-span-3">
                        <label class="block text-gray-700 text-sm font-bold" for="reasonforconsultation_patient">Motivo de Consulta</label>
                        <textarea v-model="form.reasonforconsultation_patient" id="reasonforconsultation_patient" rows="3" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" :class="{ 'border-red-500': form.errors.reasonforconsultation_patient }" />
                        <p v-if="form.errors.reasonforconsultation_patient" class="text-red-500 text-xs italic">{{ form.errors.reasonforconsultation_patient }}</p>
                    </div>

                </div>

                <div class="flex items-center justify-between mt-6">
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ isEdit ? 'Actualizar Expediente' : 'Registrar Paciente' }}
                    </button>
                    <button
                        type="button"
                        @click="router.visit(route('patients.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
