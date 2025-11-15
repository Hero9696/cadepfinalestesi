<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS DE PROPS DE INERTIA (Usando tus campos) ---
interface DonorItem {
    id_donor: string; // Es un string (ej: 'S1')
    email_donor: string;
    firstname_donor: string;
    lastname_donor: string;
    city_donor: string;
    country_donor: string;
    phone_donor: string | null;
}

// Interfaz para la estructura de paginación de Laravel/Inertia
interface Paginator<T> {
    data: T[]; // La clave donde se encuentra el array de donantes
    current_page: number;
    last_page: number;
    total: number;
    // ... otros campos del paginador
}

interface PageProps {
    donors: Paginator<DonorItem>; // Ahora 'donors' es el objeto paginador
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// 'donors' ahora es el objeto paginador completo
const donors = page.props.donors;

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Donantes No Registrados', href: route('donors.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deleteDonor = (id: string, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de ELIMINAR al donante "${name}"? Esta acción no se puede deshacer.`);

    if (confirmed) {
        router.delete(route('donors.destroy', { donor: id }), {
            onSuccess: () => {
                // Opción 2: Redirigir/visitar al index (menos eficiente si ya estás en el index, pero funciona).
                router.visit(route('donors.index'));

            },
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar al donante.');
            }
        });
    }
};
</script>

<template>
    <Head title="Registro de Donantes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Registro de Donantes </h3>

                <button
                    @click="router.visit(route('donors.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm shadow-md transition duration-150"
                >
                    ➕ Nuevo Donante
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto / Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- LA CORRECCIÓN CLAVE: Iterar sobre donors.data en lugar de donors -->
                        <tr v-for="donor in donors.data" :key="donor.id_donor">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ donor.id_donor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ donor.firstname_donor }} {{ donor.lastname_donor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ donor.phone_donor || donor.email_donor }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ donor.city_donor }}, {{ donor.country_donor }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('donors.edit', { donor: donor.id_donor }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3 transition duration-150"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteDonor(donor.id_donor, `${donor.firstname_donor} ${donor.lastname_donor}`)"
                                    class="text-red-600 hover:text-red-900 transition duration-150"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Aquí faltaría el componente de paginación si se quiere usar la paginación -->
            <!-- <Pagination :links="donors.links" /> -->
        </div>
    </AppLayout>
</template>
