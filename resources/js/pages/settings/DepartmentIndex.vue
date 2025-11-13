<script setup lang="ts">
import { router, Head } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- INTERFACES DE DATOS ---
interface Department {
    id_department: number;
    name_department: string;
}

// --- DECLARACIÓN LOCAL DE ROUTE ---
const route = (window as any).route;

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Departamentos', href: route('departments.index') },
]);

// 1. Define la 'forma' de un Departamento (ajusta según tu modelo)
interface Department {
  id_department: number;
  name_department: string;
  // ...otros campos si los necesitas
}

// 2. Recibe los 'departments' como una prop directamente del controlador
const props = defineProps<{
  departments: Department[];
}>();
// --- CICLO DE VIDA ---


// --- LÓGICA DE DATOS ---


// --- LÓGICA DE ACCIONES ---
const deleteDepartment = async (id: number, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de eliminar el departamento "${name}"?`);

    if (confirmed) {
        try {
            // Usamos la ruta DELETE
            await axios.delete(route('departments.destroy', { id: id }));
            
            // Recargar la lista
            
            console.log(`Departamento eliminado: ${name}`);

        } catch (error) {
            console.error('Error deleting department:', error);
            window.alert('Hubo un error al eliminar el departamento. Podría tener municipios asociados.');
        }
    }
};
</script>

<template>
    <Head title="Gestión de Departamentos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Departamentos</h3>
                
                <button 
                    @click="router.visit(route('departments.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nuevo Departamento
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="dept in props.departments" :key="dept.id_department">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ dept.id_department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ dept.name_department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button 
                                    @click="router.visit(route('departments.edit', { id: dept.id_department }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button 
                                    @click="deleteDepartment(dept.id_department, dept.name_department)"
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