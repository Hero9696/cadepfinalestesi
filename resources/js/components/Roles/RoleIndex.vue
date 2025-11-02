<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
// Si la carpeta de importación es diferente:
//import RoleForm from '@/components/Roles/RoleForm.vue';

// Interfaz para asegurar la tipificación de los datos del rol, basado en el uso de la plantilla
interface Role {
    id_role: number;
    name_role: string;
    description_role: string;
    id_state_role: { name_state: number } | null;
}

    const roles = ref<Role[]>([]);
    const selectedRole = ref<Role | null>(null);
const showModal = ref(false);

onMounted(() => {
    fetchRoles();
});

const fetchRoles = async () => {
    try {
        const response = await axios.get('/roles-json');
        roles.value = response.data;
        closeForm();
    } catch (error) {
        console.error('Error al obtener roles:', error);
    }
};

const openForm = (role: Role | null = null) => {
    selectedRole.value = role;
    showModal.value = true;
};

const closeForm = () => {
    selectedRole.value = null;
    showModal.value = false;
};

const deleteRole = async (id: number) => {
    // 🛑 ¡IMPORTANTE!: Se reemplaza la función nativa 'confirm()' por un aviso.
    // En producción, DEBES implementar un MODAL CUSTOM de confirmación aquí.
    if (confirm("Al hacer clic en 'Aceptar' se eliminará el rol. DEBE REEMPLAZAR ESTO POR UN MODAL CUSTOM.")) {
        try {
            await axios.delete(`/api/roles/${id}`);
            fetchRoles();
            // 💡 Reemplazar con una notificación Toast de éxito.
            console.log('Rol eliminado con éxito.');
        } catch (error) {
            console.error('Error al eliminar el rol:', error);
            // 💡 Reemplazar con una notificación Toast de error.
            console.log('Hubo un error al eliminar el rol. Podría tener usuarios asociados.');
        }
    }
};

</script>

<template>
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Lista de Roles de Usuario</h3>
            <button
                @click="openForm()"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
            >
                ➕ Nuevo Rol
            </button>
        </div>

        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
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
                    <tr v-for="role in roles" :key="role.id_role">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ role.id_role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ role.name_role }}</td>
                        <!-- El max-w-xs evita que la descripción rompa el diseño de la tabla -->
                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ role.description_role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ role.id_state_role ? role.id_state_role : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                @click="openForm(role)"
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

      <!-- <RoleForm :role-data="selectedRole" :show-modal="showModal" @close="closeForm" @success="fetchRoles" /> -->
    </div>
</template>