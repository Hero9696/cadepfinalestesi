<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS DE PROPS DE INERTIA ---
interface Area {
    id_area: number;
    name_area: string;
    description_area: string;
    duration_area: string; // TIME en DB, se recibe como string 'HH:MM:SS'
    state: { name_state: string } | null;
}

// --- CORRECCIÓN 1: Interfaz de PageProps completa ---
interface PageProps extends InertiaBasePageProps {
    areas: Area[];
    // Añadir props globales que faltaban
    auth: { user: any | null };
    errors?: Record<string, string>;
    name?: string;
    quote?: { message: string; author: string };
    sidebarOpen?: boolean;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// --- CORRECCIÓN 2: Usar 'computed' para la reactividad ---
// 'areas' se actualizará automáticamente cuando 'page.props.areas' cambie (ej. al borrar)
const areas = computed(() => page.props.areas);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Áreas de Terapia', href: route('areas.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deleteArea = (id: number, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de eliminar el área "${name}"?`);

    if (confirmed) {
        // --- CORRECCIÓN 3: El parámetro debe ser 'area' (singular) ---
        router.delete(route('areas.destroy', { area: id }), {
            preserveScroll: true, // Para que no salte al inicio de la página
            onSuccess: () => {
                console.log(`Área eliminada: ${name}`);
            },
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar el área. Es posible que tenga registros asociados.');
            }
        });
    }
};
</script>

<template>
    <Head title="Gestión de Áreas de Terapia" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Áreas de Terapia</h3>

                <button
                    @click="router.visit(route('areas.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nueva Área
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración (H:M:S)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- 'areas' ahora es un computed, pero v-for lo maneja automáticamente -->
                        <tr v-for="area in areas" :key="area.id_area">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ area.id_area }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ area.name_area }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ area.duration_area }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ area.state ? area.state.name_state : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button

                                    @click="router.visit(route('areas.edit', { area: area.id_area }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteArea(area.id_area, area.name_area)"
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
