<template>
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Lista de Usuarios</h3>
            <button 
                @click="router.visit(route('admin.users.create'))"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
            >
                ➕ Nuevo Usuario
            </button>
        </div>

        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users" :key="user.id_user">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.id_user }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ user.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.role ? user.role.name_role : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.state ? user.state.name_state : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button 
                                @click="router.visit(route('admin.users.edit', { user: user.id_user }))"
                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                            >
                                Editar
                            </button>
                            <button 
                                @click="confirmDeactivate(user.id_user, user.id_state_user)"
                                :class="{'text-red-600 hover:text-red-900': user.id_state_user != 2, 'text-green-600 hover:text-green-900': user.id_state_user == 2}"
                            >
                                {{ user.id_state_user != 2 ? 'Desactivar' : 'Activar' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// --- INTERFACES DE DATOS ---
interface User {
    id_user: number;
    name: string;
    id_state_user: number;
    role: { name_role: string } | null;
    state: { name_state: string } | null;
}

// --- DECLARACIÓN LOCAL DE ROUTE (DE ZIGGY) ---
const route = (window as any).route;


// --- ESTADO REACTIVO ---
const users = ref<User[]>([]);
const currentUserId = 1; 

// --- CICLO DE VIDA ---
onMounted(() => {
    fetchUsers();
});

// --- LÓGICA DE DATOS ---
const fetchUsers = async () => {
    try {
        // Asegúrate de que el backend ya no devuelve 404
        const response = await axios.get<User[]>('/api/users');
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

// --- LÓGICA DE ACCIONES ---

const confirmDeactivate = async (id: number, currentStateId: number) => {
    const action = currentStateId !== 2 ? 'desactivar' : 'activar';

    // Se mantiene window.confirm temporalmente (DEBE reemplazarse por un modal de Vue)
    const confirmed = window.confirm(`¿Estás seguro de que quieres ${action} este usuario?`);

    if (confirmed) {
        try {
            // Usamos el estado 2 como "Inactivo" y 1 como "Activo" para alternar.
            const newStateId = currentStateId !== 2 ? 2 : 1; 
            
            await axios.put(`/api/users/${id}/deactivate`, {
                idupdater_user_user: currentUserId,
                id_state_user: newStateId
            });
            
            // Refrescar la lista
            fetchUsers();
            console.log(`Usuario ${action} con éxito.`);

        } catch (error) {
            console.error('Error updating user state:', error);
            console.log(`Hubo un error al ${action} el usuario.`);
        }
    }
};
</script>