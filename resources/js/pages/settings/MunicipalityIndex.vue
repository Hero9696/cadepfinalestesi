<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS DE PROPS DE INERTIA ---
interface Municipality {
    id_municipality: number;
    name_municipality: string;
    id_department_municipality: number;
    department: { name_department: string } | null;
}

// Interfaz corregida para PageProps
interface PageProps {
    municipalities: Municipality[];
    // Añade aquí otras props globales si faltan
    auth?: any;
    name?: string;
    quote?: { message: string; author: string };
    errors?: Record<string, string>;
    success?: string; // Para el mensaje flash de éxito
    error?: string; // Para el mensaje flash de error
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// 'municipalities' es reactivo a las props (se actualiza solo)
const municipalities = computed(() => page.props.municipalities);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    // Asumiendo que tienes 'departments.index'
    { title: 'Departamentos', href: route('departments.index') },
    { title: 'Municipios', href: route('municipalities.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deleteMunicipality = (id: number, name: string) => {
    // 🛑 Reemplaza window.confirm con un modal en el futuro
    const confirmed = window.confirm(`¿Estás seguro de eliminar el municipio "${name}"?`);

    if (confirmed) {
        // --- ¡LA CORRECCIÓN CLAVE ESTÁ AQUÍ! ---
        // 1. El nombre de la ruta es 'municipalities.destroy' (plural)
        // 2. El parámetro es 'municipality' (singular) para que coincida con el controlador
        router.delete(route('municipalities.destroy', { municipality: id }), {
            preserveScroll: true, // No ir al inicio de la página
            onSuccess: () => {
                // El controlador ya recarga la página con el mensaje flash
                console.log('Municipio eliminado (manejado por Inertia)');
            },
            onError: (errors) => {
                console.error('Error:', errors);
                // Si el controlador envía un 'error' flash, se mostrará.
                // Si no, mostramos una alerta genérica.
                window.alert(page.props.error || 'Hubo un error al eliminar el municipio.');
            }
        });
    }
};
</script>

<template>
    <Head title="Gestión de Municipios" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">

            <!-- Mensajes Flash de Éxito (del redirect) -->
            <div v-if="page.props.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ page.props.success }}</span>
            </div>
            <!-- Mensajes Flash de Error (del redirect) -->
            <div v-if="page.props.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ page.props.error }}</span>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Municipios</h3>

                <button
                    @click="router.visit(route('municipalities.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nuevo Municipio
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Usamos la prop computada 'municipalities' -->
                        <tr v-for="muni in municipalities" :key="muni.id_municipality">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ muni.id_municipality }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ muni.name_municipality }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ muni.department ? muni.department.name_department : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button


                                    @click="router.visit(route('municipalities.edit', { municipality: muni.id_municipality }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteMunicipality(muni.id_municipality, muni.name_municipality)"
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
