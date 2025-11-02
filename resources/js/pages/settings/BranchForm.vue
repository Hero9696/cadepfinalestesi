<!-- resources/js/Pages/Settings/Branches/BranchForm.vue -->

<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS DE PROPS ---
interface Department { id_department: number; name_department: string; }
interface Municipality { id_municipality: number; name_municipality: string; id_department_municipality: number; }
interface State { id_state: number; name_state: string; }

interface BranchProp {
    id_branch: number;
    name_branch: string;
    phone_branch: string;
    id_municipality_branch: number;
    id_department_branch: number;
    id_state_branch: number;
}

interface PageProps {
    branch?: BranchProp;
    departments: Department[];
    municipalities: Municipality[];
    states: State[];
    errors?: Record<string, string>;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const branchProp = page.props.branch;
const departments = page.props.departments;
const allMunicipalities = page.props.municipalities;
const states = page.props.states;

const isEdit = computed(() => !!branchProp);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Sucursales', href: route('branches.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    name_branch: branchProp?.name_branch || '',
    phone_branch: branchProp?.phone_branch || '',

    // IDs de ubicación
    selected_department_id: branchProp?.id_department_branch || '', // Usamos un campo temporal
    id_municipality_branch: branchProp?.id_municipality_branch || '',
    id_department_branch: branchProp?.id_department_branch || '', // FK para el controlador
    id_state_branch: branchProp?.id_state_branch || '',

    // Auditoría
    idupdater_user_branch: page.props.auth?.user?.id || 1,
});

// Municipios filtrados por el departamento seleccionado
const filteredMunicipalities = computed(() => {
    return allMunicipalities.filter(m => m.id_department_municipality === form.selected_department_id);
});

// Si el departamento cambia, resetea el municipio
watch(() => form.selected_department_id, () => {
    form.id_municipality_branch = '';
    form.id_department_branch = form.selected_department_id; // Sincroniza la FK
});

// --- LÓGICA DE GUARDADO ---
const saveBranch = () => {

    // Esta variable 'url' ya no es necesaria si usas form.put/post
    // const url = isEdit.value
    //     ? route('branches.update', { branch: branchProp!.id_branch }) // <-- CORREGIDO
    //     : route('branches.store');

    if (isEdit.value && branchProp) { // <-- Asegúrate de chequear branchProp
        // En edición, usamos PUT
        // --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
        // El parámetro debe llamarse 'branch', no 'admin_branch'.
        form.put(route('branches.update', { branch: branchProp.id_branch }), {
            onSuccess: () => router.visit(route('branches.index')),
        });
    } else {
        // En creación, usamos POST
        // Es más limpio llamar a route() aquí también
        form.post(route('branches.store'), {
            onSuccess: () => router.visit(route('branches.index')),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Sucursal' : 'Crear Sucursal'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-2xl">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Sucursal' : 'Crear Nueva Sucursal' }}
            </h2>

            <form @submit.prevent="saveBranch">

                <!-- Nombre y Teléfono -->
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="name_branch">Nombre</label>
                        <input v-model="form.name_branch" type="text" id="name_branch" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.name_branch }" />
                        <p v-if="form.errors.name_branch" class="text-red-500 text-xs italic">{{ form.errors.name_branch }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="phone_branch">Teléfono</label>
                        <input v-model="form.phone_branch" type="text" id="phone_branch" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.phone_branch }" />
                        <p v-if="form.errors.phone_branch" class="text-red-500 text-xs italic">{{ form.errors.phone_branch }}</p>
                    </div>
                </div>

                <!-- Ubicación Geográfica -->
                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Ubicación</h3>

                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <!-- Departamento -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="selected_department_id">Departamento</label>
                        <select v-model="form.selected_department_id" id="selected_department_id" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_department_branch }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                                {{ dept.name_department }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_department_branch" class="text-red-500 text-xs italic">{{ form.errors.id_department_branch }}</p>
                    </div>

                    <!-- Municipio -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_municipality_branch">Municipio</label>
                        <select v-model="form.id_municipality_branch" id="id_municipality_branch" required
                            :disabled="!form.selected_department_id"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 disabled:bg-gray-100"
                            :class="{ 'border-red-500': form.errors.id_municipality_branch }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="muni in filteredMunicipalities" :key="muni.id_municipality" :value="muni.id_municipality">
                                {{ muni.name_municipality }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_municipality_branch" class="text-red-500 text-xs italic">{{ form.errors.id_municipality_branch }}</p>
                    </div>

                    <!-- Estado -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_state_branch">Estado</label>
                        <select v-model="form.id_state_branch" id="id_state_branch" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_state_branch }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="state in states" :key="state.id_state" :value="state.id_state">
                                {{ state.name_state }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_state_branch" class="text-red-500 text-xs italic">{{ form.errors.id_state_branch }}</p>
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="flex items-center justify-between mt-6">
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ isEdit ? 'Actualizar' : 'Guardar' }}
                    </button>
                    <button
                        type="button"
                        @click="router.visit(route('branches.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
