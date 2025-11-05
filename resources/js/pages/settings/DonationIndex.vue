<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS DE PROPS DE INERTIA ---

// Interfaz para el formato de datos simplificado que envía el controlador
interface DonationItem {
    id: number; // CLAVE: El controlador envía 'id'
    type: 'S' | 'R';
    donor_name: string;
    amount: number;
    currency: string;
    patient_id: number | null;
    patient_name: string; // CLAVE: El controlador envía 'patient_name'
    created_at: string; // CLAVE: El controlador envía 'created_at'
}

// Interfaz para la estructura de paginación de Laravel/Inertia
interface Paginator<T> {
    data: T[]; // La clave donde se encuentra el array de donaciones
    current_page: number;
    last_page: number;
    total: number;
    links: any[]; // Para el componente de paginación
}

interface PageProps {
    donations: Paginator<DonationItem>; // Ahora 'donations' es el objeto paginador
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// 'donations' ahora es el objeto paginador completo
const donations = page.props.donations;

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Donaciones', href: route('donations.index') },
]);

// --- HELPERS ---
const getDonorTypeLabel = (type: string) => {
    return type === 'S' ? 'Simple (S)' : 'Registrado (R)';
};

// Formatear la moneda
const formatCurrency = (amount: number, currency: string) => {
    try {
        const formatter = new Intl.NumberFormat('es-GT', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2,
        });
        return formatter.format(amount);
    } catch (e) {
        return `${amount} ${currency}`; // Fallback
    }
}

// --- LÓGICA DE ACCIONES ---
const deleteDonation = (id: number) => {
    // Reemplazado window.confirm por un mensaje más amigable
    if (!window.confirm(`¿Estás seguro de ELIMINAR el registro de esta donación (ID: ${id})? Esta acción no se puede deshacer.`)) {
        return;
    }

    // CORRECCIÓN: El parámetro de ruta debe ser 'donation' para el Model Binding
    router.delete(route('donations.destroy', { donation: id }), {
        // Al omitir onSuccess, Inertia gestionará la recarga al recibir la redirección del controlador.
        onError: (errors) => {
            console.error('Error:', errors);
            window.alert('Hubo un error al eliminar la donación. Asegúrate de que no existan dependencias.');
        }
    });
};
</script>

<template>
    <Head title="Registro de Donaciones" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Historial de Donaciones</h3>
            </div>

            <div class="shadow overflow-x-auto border-b border-gray-200 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo Donante</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Donante</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paciente Ligado</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <!-- CLAVE: Iterar sobre donations.data -->
                        <tr v-for="donation in donations.data" :key="donation.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-700">
                                {{ formatCurrency(donation.amount, donation.currency) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ getDonorTypeLabel(donation.type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ donation.donor_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(donation.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ donation.patient_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('donations.edit', { donation: donation.id }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteDonation(donation.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="donations.data.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                No hay registros de donaciones para mostrar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <!-- Componente de Paginación (Asumo que tienes uno) -->
            <!-- <div v-if="donations.last_page > 1" class="mt-4">
                <Pagination :links="donations.links" />
            </div> -->
        </div>
    </AppLayout>
</template>
