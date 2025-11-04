<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS ---
interface PatientOption { id: number; name: string; }

interface RelativeProp {
    id_relative: number;
    idprincipal_patient_relative: number;
    idsecondary_patient_relative: number;
    relationship_relative: string;
}

interface PageProps {
    relative?: RelativeProp;
    patients: PatientOption[];
    auth: { user: { id_user: number } };
    errors?: Record<string, string>;
}

// --- PROPS INERCIA ---
const page = usePage<PageProps>();
// Aseguramos que route exista
const route = (window as any).route;

const relativeProp = page.props.relative;
const patients = page.props.patients;

const isEdit = computed(() => !!relativeProp);

// --- TIPOS DE RELACIÓN ---
const relationshipTypes = [
    'Padre', 'Madre', 'Hijo/a', 'Cónyuge', 'Hermano/a',
    'Tío/a', 'Abuelo/a', 'Tutor Legal', 'Otro Familiar'
];

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Relaciones Familiares', href: route('relatives.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- COMPUTED PARA EL TÍTULO ---
const pageTitle = computed(() =>
    isEdit.value ? 'Editar Relación' : 'Crear Relación Familiar'
);

// --- FORMULARIO CON TIPADO MEJORADO ---
interface RelativeForm {
    id_relative: number | null;
    // CORRECCIÓN DE TIPADO: Debe permitir string ('') para la inicialización y number para el valor seleccionado.
    idprincipal_patient_relative: number | string;
    idsecondary_patient_relative: number | string;
    relationship_relative: string;
    idupdater_user_relative: number;
}

const form = useForm<RelativeForm>({
    id_relative: relativeProp?.id_relative || null,
    // Inicialización simple: Si existe el prop, úsalo. Si no, usa string vacío.
    idprincipal_patient_relative: relativeProp?.idprincipal_patient_relative || '',
    idsecondary_patient_relative: relativeProp?.idsecondary_patient_relative || '',
    relationship_relative: relativeProp?.relationship_relative || '',
    idupdater_user_relative: page.props.auth.user.id,
});

// --- VALIDACIÓN ---
const isSamePatientError = computed(() => {
    // Es crucial que los valores se comparen como números o strings consistentes.
    // Usaremos la coerción de tipos aquí ya que el v-model.number lo maneja en la plantilla.
    const principalId = form.idprincipal_patient_relative;
    const secondaryId = form.idsecondary_patient_relative;

    return (
        principalId &&
        secondaryId &&
        // Usamos Number() para la comparación lógica si los IDs son numéricos.
        Number(principalId) === Number(secondaryId)
    );
});

// --- GUARDAR ---
const saveRelative = () => {
    if (isSamePatientError.value) {
        window.alert('¡Error! Un paciente no puede ser su propio pariente.');
        return;
    }

    const url = isEdit.value
        ? route('relatives.update', { relative: relativeProp!.id_relative })
        : route('relatives.store');

    if (isEdit.value) {
        form.put(url, {
            onSuccess: () => router.visit(route('relatives.index')),
            onError: (errors) => console.error('Error PUT:', errors)
        });
    } else {
        form.post(url, {
            onSuccess: () => router.visit(route('relatives.index')),
            onError: (errors) => console.error('Error POST:', errors)
        });
    }
};
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-2xl">
            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Relación' : 'Registrar Nueva Relación Familiar' }}
            </h2>

            <form @submit.prevent="saveRelative">
                <p class="mb-4 text-sm text-gray-500">
                    Establezca la relación: <strong>Paciente Principal</strong> es el <strong>[Tipo de Relación]</strong> de <strong>Paciente Secundario</strong>.
                </p>

                <div class="grid gap-2 mb-4">
                    <label class="block text-gray-700 text-sm font-bold" for="idprincipal_patient_relative">Paciente Principal</label>
                    <select
                        v-model.number="form.idprincipal_patient_relative" id="idprincipal_patient_relative"
                        required
                        class="shadow border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.idprincipal_patient_relative }"
                    >
                        <option value="" disabled>Seleccionar Paciente Principal</option>
                        <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                            {{ patient.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.idprincipal_patient_relative" class="text-red-500 text-xs italic">
                        {{ form.errors.idprincipal_patient_relative }}
                    </p>
                </div>

                <div class="grid gap-2 mb-4">
                    <label class="block text-gray-700 text-sm font-bold" for="relationship_relative">
                        Tipo de Relación
                    </label>
                    <select
                        v-model="form.relationship_relative"
                        id="relationship_relative"
                        required
                        class="shadow border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.relationship_relative }"
                    >
                        <option value="" disabled>Seleccionar Tipo</option>
                        <option v-for="type in relationshipTypes" :key="type" :value="type">
                            {{ type }}
                        </option>
                    </select>
                    <p v-if="form.errors.relationship_relative" class="text-red-500 text-xs italic">
                        {{ form.errors.relationship_relative }}
                    </p>
                </div>

                <div class="grid gap-2 mb-4">
                    <label class="block text-gray-700 text-sm font-bold" for="idsecondary_patient_relative">Paciente Secundario</label>
                    <select
                        v-model.number="form.idsecondary_patient_relative" id="idsecondary_patient_relative"
                        required
                        class="shadow border rounded w-full py-2 px-3 text-gray-700"
                        :class="{
                            'border-red-500': form.errors.idsecondary_patient_relative || isSamePatientError
                        }"
                    >
                        <option value="" disabled>Seleccionar Paciente Secundario</option>
                        <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                            {{ patient.name }}
                        </option>
                    </select>

                    <p v-if="form.errors.idsecondary_patient_relative" class="text-red-500 text-xs italic">
                        {{ form.errors.idsecondary_patient_relative }}
                    </p>
                    <p v-if="isSamePatientError" class="text-red-500 text-xs italic font-bold">
                        ¡Error! Los pacientes Principal y Secundario no pueden ser el mismo.
                    </p>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        :disabled="form.processing || !!isSamePatientError"
                    >
                        {{ isEdit ? 'Actualizar Relación' : 'Registrar Relación' }}
                    </button>

                    <button
                        type="button"
                        @click="router.visit(route('relatives.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
