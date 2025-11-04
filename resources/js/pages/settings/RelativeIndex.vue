<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS ---
interface PatientData {
    id_patient: number;
    firstname_patient: string;
    middlename_patient: string;
    thirdname_patient: string;
    lastname_patient: string;
    secondlastname_patient: string;
    thirdlastname_patient: string;
}

interface Relative {
    id_relative: number;
    relationship_relative: string;
    principalPatient: PatientData | null;
    secondaryPatient: PatientData | null;
}

interface PageProps {
    relatives?: Relative[]; 
    flash: { success?: string; error?: string; }
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// console.log('--- DATOS CRUDOS DE PROPS (relatives) ---', page.props.relatives);

const relatives = page.props.relatives || [];
const flash = page.props.flash || {}; 

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Relaciones Familiares', href: route('relatives.index') },
]);

// --- LÓGICA DE VISUALIZACIÓN ---

const getPatientName = (patient: PatientData | null): string => {
    if (!patient) return 'Paciente Desconocido (N/A)';
    
    const names = [
        patient.firstname_patient, 
        patient.middlename_patient, 
        patient.thirdname_patient
    ].filter(Boolean).join(' ');

    const lastnames = [
        patient.lastname_patient, 
        patient.secondlastname_patient, 
        patient.thirdlastname_patient
    ].filter(Boolean).join(' ');

    const fullName = `${names} ${lastnames}`.trim();

    return fullName || 'Paciente (Sin Nombre)';
};

// --- LÓGICA DE ACCIONES ---
const deleteRelative = (id: number, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de ELIMINAR la relación familiar ${name}?`);

    if (confirmed) {
        router.delete(route('relatives.destroy', { relative: id }), {
               onSuccess: () => {
               

                // Opción 2: Redirigir/visitar al index (menos eficiente si ya estás en el index, pero funciona).
                 router.visit(route('relatives.index'));

            },
            onError: (errors) => {
                console.error('Error al eliminar:', errors);
                window.alert('Hubo un error al eliminar la relación.');
            }
        });
    }
};
</script>

<template>

    <Head title="Relaciones Familiares" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">Relaciones Familiares Registradas</h3>

            <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ flash.error }}
            </div>

            <div class="flex justify-end items-center mb-4">
                <button @click="router.visit(route('relatives.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm shadow-md transition duration-150 w-full md:w-auto flex-shrink-0">
                    ➕ Registrar Nueva Relación
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paciente Principal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Relación
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paciente Secundario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="relative in relatives" :key="relative.id_relative" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ relative.id_relative }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ getPatientName(relative.principalPatient) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                {{ relative.relationship_relative }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ getPatientName(relative.secondaryPatient) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('relatives.edit', { relative: relative.id_relative }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3 transition duration-150"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteRelative(relative.id_relative, relative.relationship_relative)"
                                    class="text-red-600 hover:text-red-900 transition duration-1T50"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="relatives.length === 0" class="p-6 text-center text-gray-500">
                    No hay relaciones registradas todavía.
                </div>
            </div>
        </div>
    </AppLayout>
</template>