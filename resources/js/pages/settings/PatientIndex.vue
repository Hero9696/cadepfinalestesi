<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS DE PROPS DE INERTIA ---
interface Patient {
    id_patient: number;
    cui_patient: string;
    firstname_patient: string;
    lastname_patient: string;
    age_patient: number;
    phone_patient: string;
    branch: { name_branch: string } | null;
    state: { name_state: string } | null;
}

interface PageProps {
    patients: Patient[];
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const patients = page.props.patients;

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Pacientes', href: route('patients.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deletePatient = (id: number, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de ELIMINAR el expediente del paciente "${name}"? Se recomienda DESACTIVAR.`);

    if (confirmed) {
        router.delete(route('patients.destroy', { patient: id }), {
            onSuccess: () => {
                console.log(`Paciente eliminado: ${name}`);

                // Opción 2: Redirigir/visitar al index (menos eficiente si ya estás en el index, pero funciona).
                 router.visit(route('patients.index'));

            },
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar al paciente.');
            }
        });
    }
};
</script>

<template>

    <Head title="Registro de Pacientes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Registro de Pacientes</h3>

                <button @click="router.visit(route('patients.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    ➕ Nuevo Paciente
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CUI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre Completo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Edad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sucursal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="patient in patients" :key="patient.id_patient">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ patient.cui_patient ||
                                'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ patient.firstname_patient }}
                                {{ patient.lastname_patient }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ patient.age_patient }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ patient.phone_patient }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ patient.branch?.name_branch
                                || 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ patient.state?.name_state
                                || 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('patients.edit', { patient: patient.id_patient }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Editar
                                </button>
                                <button
                                    @click="deletePatient(patient.id_patient, `${patient.firstname_patient} ${patient.lastname_patient}`)"
                                    class="text-red-600 hover:text-red-900">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
