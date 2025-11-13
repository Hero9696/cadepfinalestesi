<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS ---
interface State { id_state: number; name_state: string; }

interface AreaProp {
    id_area: number;
    name_area: string;
    description_area: string;
    duration_area: string;
    id_state_area: number;
}

// --- CORRECCIÓN DE TIPOS: Interfaz de PageProps completa ---
interface PageProps extends InertiaBasePageProps {
    area?: AreaProp;
    states: State[];
    // Añadir props globales que faltaban
    auth: { user: { id_user: number } | null }; // Hacer 'user' opcional
    errors?: Record<string, string>;
    name?: string;
    quote?: { message: string; author: string };
    sidebarOpen?: boolean;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const areaProp = page.props.area;
const states = page.props.states;

const isEdit = computed(() => !!areaProp);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Áreas de Terapia', href: route('areas.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    name_area: areaProp?.name_area || '',
    description_area: areaProp?.description_area || '',
    duration_area: areaProp?.duration_area || '00:30:00', // Valor por defecto
    id_state_area: areaProp?.id_state_area || (states.length > 0 ? states[0].id_state : ''), // Estado por defecto
    // 'idupdater_user_area' se maneja en el backend (controlador)
});


// --- LÓGICA DE GUARDADO (CORREGIDA) ---
const saveArea = () => {

    // Ya no es necesario enviar 'idupdater_user_area', el controlador lo hace.
    // Ya no es necesario 'const url', se llama a route() directamente.

    if (isEdit.value && areaProp) {
        // --- ¡CORRECCIÓN AQUÍ! ---
        // El parámetro debe llamarse 'area' (singular), no 'admin_area'.
        form.put(route('areas.update', { area: areaProp.id_area }), {
            onSuccess: () => router.visit(route('areas.index')),
        });
    } else {
        // POST para creación
        form.post(route('areas.store'), {
            onSuccess: () => router.visit(route('areas.index')),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Área' : 'Crear Área'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-2xl">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Área de Terapia' : 'Crear Nueva Área de Terapia' }}
            </h2>

            <form @submit.prevent="saveArea">

                <div class="grid gap-4 mb-4">
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="name_area">Nombre</label>
                        <input v-model="form.name_area" type="text" id="name_area" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.name_area }" />
                        <p v-if="form.errors.name_area" class="text-red-500 text-xs italic">{{ form.errors.name_area }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="description_area">Descripción</label>
                        <textarea v-model="form.description_area" id="description_area" rows="3" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.description_area }" />
                        <p v-if="form.errors.description_area" class="text-red-500 text-xs italic">{{ form.errors.description_area }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="duration_area">Duración (HH:MM:SS)</label>
                        <input v-model="form.duration_area" type="time" step="1" id="duration_area" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.duration_area }" />
                        <p v-if="form.errors.duration_area" class="text-red-500 text-xs italic">{{ form.errors.duration_area }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="id_state_area">Estado</label>
                        <select v-model="form.id_state_area" id="id_state_area" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.id_state_area }">
                            <option value="" disabled>Seleccionar</option>
                            <option v-for="state in states" :key="state.id_state" :value="state.id_state">
                                {{ state.name_state }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_state_area" class="text-red-500 text-xs italic">{{ form.errors.id_state_area }}</p>
                    </div>
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
                        @click="router.visit(route('areas.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
