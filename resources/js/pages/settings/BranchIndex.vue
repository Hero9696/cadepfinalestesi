<!-- resources/js/Pages/Settings/Branches/BranchIndex.vue -->

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
// --- 1. IMPORTAR 'computed' ---
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS DE PROPS DE INERTIA ---
interface Branch {
    id_branch: number;
    name_branch: string;
    phone_branch: string;
    id_municipality_branch: number;
    municipality: { name_municipality: string; department: { name_department: string } | null } | null;
    state: { name_state: string } | null;
}

interface PageProps {
    branches: Branch[];
    // Añadir props globales que faltaban para que 'usePage<PageProps>' sea válido
    auth: { user: any | null };
    errors?: Record<string, string>;
    name?: string;
    quote?: { message: string; author: string };
    sidebarOpen?: boolean;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// --- 2. USAR 'computed' PARA LA REACTIVIDAD ---
// Esto asegura que 'branches' se actualice CADA VEZ que 'page.props.branches' cambie.
const branches = computed(() => page.props.branches);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Sucursales', href: route('branches.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deleteBranch = async (id: number, name: string) => {
    if (window.confirm(`¿Estás seguro de eliminar la sucursal "${name}"?`)) {

        router.delete(route('branches.destroy', { branch: id }), {
            // --- 3. (MEJORA) Añadir preserveScroll ---
            preserveScroll: true,

            onSuccess: () => {
                // (Opcional) Mostrar una notificación de éxito aquí
                console.log(`Sucursal eliminada: ${name}`);
            },
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar la sucursal. Es posible que tenga registros asociados.');
            }
        });
    }
};
</script>

<template>
    <Head title="Gestión de Sucursales" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Sucursales</h3>

                <button
                    @click="router.visit(route('branches.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nueva Sucursal
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!--
                          Vue 'desenvuelve' automáticamente el .value de 'branches'
                          cuando se usa en un v-for, por lo que no necesitas 'branches.value'.
                        -->
                        <tr v-for="branch in branches" :key="branch.id_branch">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ branch.id_branch }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ branch.name_branch }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ branch.phone_branch }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span v-if="branch.municipality">
                                    {{ branch.municipality.name_municipality }}, {{ branch.municipality.department?.name_department }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ branch.state ? branch.state.name_state : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('branches.edit', { branch: branch.id_branch }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteBranch(branch.id_branch, branch.name_branch)"
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
