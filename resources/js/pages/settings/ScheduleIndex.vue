<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';


// --- TIPOS DE PROPS DE INERTIA (Ajustados a la estructura de la tabla) ---
interface Schedule {
    id_schedule: number;
    hour_schedule: string; // HH:MM:SS
    date_schedule: string; // YYYY-MM-DD
    // El controlador ya no pasa 'name_schedule' ni 'state'
}

// Interfaz que incluye las props de la página y las globales necesarias
interface PageProps extends InertiaBasePageProps {
    schedules: Schedule[];
    auth: { user: { id: number } | null };
    // Añade otras props globales aquí (errors, name, quote...)
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// Hacemos reactiva la lista para que se refresque al eliminar
const schedules = computed(() => page.props.schedules);

// Helper para mostrar solo HH:MM
const formatTimeDisplay = (time: string | undefined): string => {
    return time ? time.substring(0, 5) : 'N/A';
};

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Horarios', href: route('schedules.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deleteSchedule = (id: number, hour: string) => { // Usamos 'hour' en lugar de 'name' para la alerta
    const confirmed = window.confirm(`¿Estás seguro de eliminar el horario ${hour}?`);

    if (confirmed) {
        // --- CORRECCIÓN DE RUTA ---
        // El parámetro debe ser 'schedule' (singular) para Route Model Binding
        router.delete(route('schedules.destroy', { schedule: id }), {
            preserveScroll: true,
            onSuccess: () => {
                console.log(`Horario eliminado: ${hour}`);
                // La lista se refrescará automáticamente gracias al computed
            },
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar el horario.');
            }
        });
    }
};
</script>

<template>
    <Head title="Gestión de Horarios" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Horarios Agendados</h3>

                <button
                    @click="router.visit(route('schedules.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nuevo Horario
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="schedule in schedules" :key="schedule.id_schedule">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ schedule.id_schedule }}</td>

                            <!-- Muestra solo la hora con el helper seguro -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{ formatTimeDisplay(schedule.hour_schedule) }}
                            </td>

                            <!-- Muestra la fecha (asumiendo que viene en un formato legible) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ schedule.date_schedule }}
                            </td>

                            <!-- Columna de Acciones -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('schedules.edit', { schedule: schedule.id_schedule }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteSchedule(schedule.id_schedule, formatTimeDisplay(schedule.hour_schedule))"
                                    class="text-red-600 hover:text-red-900"
                                >
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
