<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS ---
interface Department {
    id_department: number;
    name_department: string;
}

interface MunicipalityProp {
    id_municipality: number;
    name_municipality: string;
    id_department_municipality: number;
}

interface PageProps {
    municipality?: MunicipalityProp;
    departments: Department[];
    errors?: Record<string, string>; // Errors from Inertia
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const municipalityProp = page.props.municipality;
const departments = page.props.departments;

const isEdit = computed(() => !!municipalityProp);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Municipios', href: route('municipalities.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    id_municipality: municipalityProp?.id_municipality || null,
    name_municipality: municipalityProp?.name_municipality || '',
    id_department_municipality: municipalityProp?.id_department_municipality || '',
});


// --- LÓGICA DE GUARDADO ---
// const saveMunicipality = () => {

//     const url = isEdit.value
//         ? route('municipalities.update', { id: municipalityProp!.id_municipality })
//         : route('municipalities.store');

//     // Usamos Inertia's PUT/POST con redirección automática
//     if (isEdit.value) {
//         // En edición, usamos PUT
//         form.put(url, {
//             onSuccess: () => form.reset(),
//         });
//     } else {
//         // En creación, usamos POST
//         form.post(url, {
//             onSuccess: () => form.reset(),
//         });
//     }
// };
    // --- LÓGICA DE GUARDADO ---
const saveMunicipality = () => {
    if (isEdit.value && municipalityProp) {
        form.put(route('municipalities.update', { id: municipalityProp!.id_municipality }));
    } else {
        form.post(route('municipalities.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Municipio' : 'Crear Municipio'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-lg">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Municipio' : 'Crear Nuevo Municipio' }}
            </h2>

            <form @submit.prevent="saveMunicipality">

                <div v-if="!isEdit" class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="id_municipality">
                        ID del Municipio (Código)
                    </label>
                    <input
                        v-model="form.id_municipality"
                        type="number"
                        id="id_municipality"
                        required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.id_municipality }"
                    />
                    <p v-if="form.errors.id_municipality" class="text-red-500 text-xs italic">
                        {{ form.errors.id_municipality }}
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name_municipality">
                        Nombre del Municipio
                    </label>
                    <input
                        v-model="form.name_municipality"
                        type="text"
                        id="name_municipality"
                        required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.name_municipality }"
                    />
                    <p v-if="form.errors.name_municipality" class="text-red-500 text-xs italic">
                        {{ form.errors.name_municipality }}
                    </p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="id_department_municipality">
                        Departamento
                    </label>
                    <select
                        v-model="form.id_department_municipality"
                        id="id_department_municipality"
                        required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.id_department_municipality }"
                    >
                        <option value="" disabled>Seleccione un Departamento</option>
                        <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                            {{ dept.name_department }}
                        </option>
                    </select>
                    <p v-if="form.errors.id_department_municipality" class="text-red-500 text-xs italic">
                        {{ form.errors.id_department_municipality }}
                    </p>
                </div>

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
                        @click="router.visit(route('municipalities.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
