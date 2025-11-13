<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3'; // <-- 1. Importar Head y router

// --- 1. DECLARACIÓN DE ROUTE ---
const route = (window as any).route;

// --- 2. CORRECCIÓN: Breadcrumbs para la página de Roles ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Roles', href: route('role.index') }, // Enlace a sí mismo
]);

// Interfaz para el Rol
interface Role {
    id_role: number;
    name_role: string;
    description_role: string;
    id_state_role: { name_state: number } | null; // Asumiendo que el JSON anida el estado
}

const roles = ref<Role[]>([]);

// --- 3. ELIMINADO: Ya no se necesita lógica de modales ---
// const selectedRole = ref<Role | null>(null);
// const showModal = ref(false);

onMounted(() => {
    fetchRoles();
});

const fetchRoles = async () => {
    try {
        // Usamos la ruta JSON que definiste en web.php
        const response = await axios.get(route('role.json')); 
        roles.value = response.data;
    } catch (error) {
        console.error('Error al obtener roles:', error);
    }
};

// --- 4. NUEVO: Funciones de navegación de Inertia ---
const goToCreate = () => {
    // Navega a la página del formulario de creación
    router.visit(route('role.create'));
};

const goToEdit = (id: number) => {
    // Navega a la página del formulario de edición
    router.visit(route('role.edit', { role: id }));
};

const deleteRole = (id: number) => {
    // 🛑 Reemplaza esto con un modal de confirmación
    if (confirm("¿Estás seguro de que quieres eliminar este rol?")) {
        // Usamos router.delete de Inertia
        router.delete(route('role.destroy', { role: id }), {
            // preserveScroll: true, // Evita que la página salte al inicio
            onSuccess: () => {
                // El controlador redirige, pero como esta página
                // carga sus datos con axios, debemos refrescarlos manualmente.
                fetchRoles(); 
                console.log('Rol eliminado con éxito.'); // Reemplazar con un Toast
            },
            onError: (errors) => {
                console.error('Error al eliminar el rol:', errors);
                console.log('Hubo un error al eliminar el rol.'); // Reemplazar con un Toast
            }
        });
    }
};

</script>

<template>
    <!-- 5. AÑADIDO: Head para el título de la página -->
    <Head title="Gestión de Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Lista de Roles de Usuario</h3>
                <button
                   
                    @click="goToCreate()"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Nuevo Rol
                </button>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="roles.length === 0">
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                No se encontraron roles.
                            </td>
                        </tr>
                        <tr v-for="role in roles" :key="role.id_role">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ role.id_role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ role.name_role }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ role.description_role }}</td>
                            <!-- Corregido para mostrar el nombre del estado si existe -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ role.id_state_role ? (role.id_state_role as any).name_state : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    
                                    @click="goToEdit(role.id_role)"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteRole(role.id_role)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 8. ELIMINADO: El modal de RoleForm ya no se usa aquí -->
            <!-- <RoleForm ... /> -->
        </div>
    </AppLayout>
</template>