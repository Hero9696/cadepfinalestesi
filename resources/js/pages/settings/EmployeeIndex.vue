<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS DE PROPS DE INERTIA ---
interface Employee {
    id_employee: number;
    cui_employee: string;
    firstname_employee: string;
    lastname_employee: string;
    profession_employee: string;
    area: { name_area: string } | null;
    branch: { name_branch: string } | null;
    state: { name_state: string } | null;
    user: { name: string } | null;
}

interface PageProps extends InertiaBasePageProps {
    employees: Employee[];
    // Añadir props globales para evitar errores de tipo
    auth: { user: { id: number } | null };
    errors?: Record<string, string>;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

// --- CORRECCIÓN CLAVE: Usar computed para la reactividad ---
const employees = computed(() => page.props.employees);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Empleados', href: route('employees.index') },
]);

// --- LÓGICA DE ACCIONES ---
const deleteEmployee = (id: number, name: string) => {
    const confirmed = window.confirm(`¿Estás seguro de eliminar al empleado "${name}"?`);

    if (confirmed) {
        // Usamos Inertia.delete con la ruta resource 'employees.destroy'
        router.delete(route('employees.destroy', { employee: id }), {
            // preserveScroll: true, si quieres mantener la posición de la tabla

            // Eliminamos onSuccess ya que el servidor se encarga de la redirección
            // y la recarga, y la lista se refrescará con el 'computed'.
            onError: (errors) => {
                console.error('Error:', errors);
                window.alert('Hubo un error al eliminar al empleado.');
            }
        });
    }
};
</script>

<template>
    <Head title="Gestión de Empleados" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Empleados y Terapeutas</h3>

                <button
                    @click="router.visit(route('employees.create'))"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nuevo Empleado
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CUI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profesión / Área</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario (Login)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- La variable 'employees' ahora es reactiva -->
                        <tr v-for="emp in employees" :key="emp.id_employee">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ emp.cui_employee || 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ emp.firstname_employee }} {{ emp.lastname_employee }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ emp.profession_employee }} ({{ emp.area?.name_area || 'N/A' }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ emp.branch?.name_branch || 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ emp.user?.name || 'SIN CUENTA' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="router.visit(route('employees.edit', { employee: emp.id_employee }))"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteEmployee(emp.id_employee, `${emp.firstname_employee} ${emp.lastname_employee}`)"
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
