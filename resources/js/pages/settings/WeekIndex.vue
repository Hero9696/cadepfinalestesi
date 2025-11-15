<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS DE PROPS DE INERTIA ---
interface WeekDay {
    id_week: number;
    name_week: string;
}

interface PageProps extends InertiaBasePageProps {
    weeks: WeekDay[]; // <-- Propiedad corregida a 'weeks'
    auth: { user: { id: number } | null };
    errors?: Record<string, string>;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// Hacemos reactiva la lista para que se refresque al eliminar
const weeks = computed(() => page.props.weeks); // <-- ¡Corrección clave!

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Días de la Semana', href: route('week.index') }, // <-- Corregido a plural 'weeks'
]);

// --- LÓGICA DE ACCIONES ---
const deleteWeekDay = (id: number, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de eliminar el día "${name}" (ID: ${id})? Esta acción puede afectar la programación de horarios.`);

    if (confirmed) {
        // Usamos Inertia.delete con el parámetro correcto 'week' (singular)
        router.delete(route('week.destroy', { week: id }), {
            preserveScroll: true,
            onSuccess: () => {
                console.log(`Día eliminado: ${name}`);
            },
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar el día.');
            }
        });
    }
};
</script>

<template>
    <Head title="Gestión de Días de la Semana" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Catálogo de Días de la Semana</h3>

                <button
                    @click="router.visit(route('week.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nuevo Día
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Día</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- ¡Usa 'weeks' (plural) que es la prop reactiva! -->
                        <tr v-for="day in weeks" :key="day.id_week">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ day.id_week }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ day.name_week }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('week.edit', { week: day.id_week }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteWeekDay(day.id_week, day.name_week)"
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
