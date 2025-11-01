<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import axios, { type AxiosError } from 'axios';
import { usePage } from '@inertiajs/vue3';

// -----------------------------------------------------
// 1. TYPING DE INTERFACES (CORREGIDO)
// -----------------------------------------------------

// Interfaz para la data que viene del componente padre (RoleIndex.vue)
interface RolePropData {
    id_role?: number;
    name_role: string;
    description_role: string;
    // La data de la tabla trae el objeto 'state'
    state: { name_state: string; id_state?: number } | null;
    
    // Estos campos son cruciales para el formulario, pero pueden faltar al inicio:
    id_state_role?: number | string;
    idupdater_user_role?: number; 
    idcreator_user_role?: number;
}

// Interfaz para el estado de Vue (form.value), que necesita los IDs para la API
interface RoleFormState extends RolePropData {
    id_state_role: number | string;
    idupdater_user_role: number;
}

interface State {
    id_state: number;
    name_state: string;
}

interface FormErrors {
    name_role?: string;
    description_role?: string;
    id_state_role?: string;
}

// -----------------------------------------------------
// 2. PROPS, EMITS, Y SETUP INICIAL
// -----------------------------------------------------

const props = defineProps<{
    // Usamos la nueva interfaz RolePropData para la entrada
    roleData: RolePropData | null;
    showModal: boolean;
}>();

const emit = defineEmits(['close', 'success']);
const page = usePage();

const isEdit = computed(() => props.roleData !== null && props.roleData.id_role !== undefined);
// El formulario interno usa la interfaz RoleFormState, que asegura los IDs
const form = ref<Partial<RoleFormState>>({});
const errors = ref<FormErrors>({});
const states = ref<State[]>([]);
const processing = ref(false); 

const currentUserId = computed<number>(() => page.props.auth?.user?.id || 1); 

// -----------------------------------------------------
// 3. LÓGICA ASÍNCRONA
// -----------------------------------------------------

const fetchStates = async () => {
    try {
        const response = await axios.get<State[]>('/api/states');
        states.value = response.data;
        
        if (!isEdit.value && states.value.length > 0) {
             // Asignar el primer estado por defecto para formularios nuevos
             if (!form.value.id_state_role) {
                form.value.id_state_role = states.value[0].id_state;
            }
        }
    } catch (error) {
        console.error('Error fetching states for role form:', error);
    }
};

onMounted(() => {
    fetchStates();
});

// -----------------------------------------------------
// 4. WATCHERS Y REINICIO DEL FORMULARIO
// -----------------------------------------------------

watch(() => props.roleData, (newVal) => {
    if (newVal) {
        // Modo Edición: Mapeamos los datos del prop al formulario
        form.value = { 
            name_role: newVal.name_role, 
            description_role: newVal.description_role, 
            // ASUMIMOS que el ID del estado está anidado o lo obtenemos directamente del prop.
            // Si el prop NO trae id_state_role, debes calcularlo aquí
            id_state_role: (newVal.id_state_role || newVal.state?.id_state) as number,
            idupdater_user_role: currentUserId.value,
            idcreator_user_role: newVal.idcreator_user_role // Si existe en el prop
        } as RoleFormState; // Forzamos la tipificación final
    } else {
        // Modo Creación
        form.value = {
            name_role: '', 
            description_role: '', 
            id_state_role: states.value.length > 0 ? states.value[0].id_state : '', 
            idcreator_user_role: currentUserId.value, 
            idupdater_user_role: currentUserId.value
        } as RoleFormState;
    }
    errors.value = {};
}, { immediate: true });


// -----------------------------------------------------
// 5. FUNCIÓN DE ENVÍO
// -----------------------------------------------------

const saveRole = async () => {
    errors.value = {};
    processing.value = true;
    const method = isEdit.value ? 'put' : 'post';
    const url = isEdit.value ? `/api/roles/${props.roleData!.id_role}` : '/api/roles';

    const payload = {
        ...form.value,
        id_state_role: form.value.id_state_role ? Number(form.value.id_state_role) : null,
    };
    
    try {
        await axios({ method, url, data: payload });
        
        console.log('Rol guardado con éxito.');
        emit('success');
        emit('close');
        
    } catch (error) {
        const axiosError = error as AxiosError;

        if (axiosError.response && axiosError.response.status === 422) {
            errors.value = (axiosError.response.data as { errors: FormErrors }).errors;
        } else {
            console.error('Error saving role:', error);
            console.log('Hubo un error al guardar el rol.');
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <!-- Modal Backdrop -->
    <div 
        v-if="props.showModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300"
        @click.self="emit('close')"
    >
        <!-- Modal Content -->
        <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all duration-300 scale-100 opacity-100">
            
            <!-- Título y Botón de Cerrar -->
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h4 class="text-lg font-bold text-gray-800">
                    {{ isEdit ? 'Editar Rol' : 'Crear Nuevo Rol' }}
                </h4>
                <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl font-semibold leading-none">
                    &times;
                </button>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="saveRole" class="space-y-4">
                
                <!-- Nombre del Rol -->
                <div>
                    <label for="name_role" class="block text-sm font-medium text-gray-700">Nombre del Rol:</label>
                    <input 
                        type="text" 
                        id="name_role" 
                        v-model="form.name_role" 
                        :class="[
                            'mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500',
                            errors.name_role ? 'border-red-500' : ''
                        ]"
                        required
                    />
                    <p v-if="errors.name_role" class="mt-1 text-sm text-red-600">{{ errors.name_role }}</p>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description_role" class="block text-sm font-medium text-gray-700">Descripción:</label>
                    <textarea 
                        id="description_role" 
                        v-model="form.description_role" 
                        rows="3"
                         :class="[
                            'mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500',
                            errors.description_role ? 'border-red-500' : ''
                        ]"
                        required
                    ></textarea>
                    <p v-if="errors.description_role" class="mt-1 text-sm text-red-600">{{ errors.description_role }}</p>
                </div>
                
                <!-- Estado del Rol -->
                <div>
                    <label for="id_state_role" class="block text-sm font-medium text-gray-700">Estado:</label>
                    <select 
                        id="id_state_role" 
                        v-model="form.id_state_role" 
                        :class="[
                            'mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500',
                            errors.id_state_role ? 'border-red-500' : ''
                        ]"
                        required
                    >
                        <option 
                            v-for="state in states" 
                            :key="state.id_state" 
                            :value="state.id_state"
                        >
                            {{ state.name_state }}
                        </option>
                    </select>
                    <p v-if="errors.id_state_role" class="mt-1 text-sm text-red-600">{{ errors.id_state_role }}</p>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 pt-2">
                    <button 
                        type="button" 
                        @click="emit('close')" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                        :disabled="processing"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="processing"
                    >
                        <span v-if="processing">Guardando...</span>
                        <span v-else>{{ isEdit ? 'Guardar Cambios' : 'Crear Rol' }}</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>