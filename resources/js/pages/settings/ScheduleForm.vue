<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';

// --- TIPOS ---
// 1. Tipo de la Prop Schedule (ajustado a la estructura de la tabla)
interface ScheduleProp {
    id_schedule: number;
    hour_schedule: string; // HH:MM:SS de la DB
    date_schedule: string; // YYYY-MM-DD de la DB
    // Las columnas de auditoría ya están en PageProps
}

// 2. Tipos de Props de Inertia (limpiadas de states innecesarios)
interface PageProps extends InertiaBasePageProps {
    schedule?: ScheduleProp;
    // Eliminamos 'states' ya que el controlador ya no lo pasa
    auth: { user: { id_user: number } | null };
    errors?: Record<string, string>;
    name?: string;
    quote?: { message: string; author: string };
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const scheduleProp = page.props.schedule;

const isEdit = computed(() => !!scheduleProp);

// Helper para formatear tiempo (ej: '08:00:00' a '08:00') para input[type="time"]
const formatTimeForInput = (time: string | undefined): string => {
    return time ? time.substring(0, 5) : ''; // Solo HH:MM
};
// Helper para formatear fecha (ej: '2025-01-01 00:00:00' a '2025-01-01')
const formatDateForInput = (date: string | undefined): string => {
    return date ? date.substring(0, 10) : ''; // Solo YYYY-MM-DD
};


// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Horarios', href: route('schedules.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    // --- CAMPOS CORREGIDOS A LA ESTRUCTURA DE TU TABLA ---
    hour_schedule: formatTimeForInput(scheduleProp?.hour_schedule) || '08:00', // Time Input (HH:MM)
    date_schedule: formatDateForInput(scheduleProp?.date_schedule) || '',       // Date Input (YYYY-MM-DD)

    // Auditoría
    idupdater_user_schedule: page.props.auth.user?.id_user || 1,
});


// --- LÓGICA DE GUARDADO ---
const saveSchedule = () => {

    if (isEdit.value && scheduleProp) {
        // PUT para edición
        // Usamos la ruta 'schedules.update' y el parámetro 'schedule' (singular)
        form.put(route('schedules.update', { schedule: scheduleProp.id_schedule }), {
            onSuccess: () => router.visit(route('schedules.index')),
        });
    } else {
        // POST para creación
        form.post(route('schedules.store'), {
            onSuccess: () => router.visit(route('schedules.index')),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Horario' : 'Crear Horario'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-lg">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Horario' : 'Crear Nuevo Horario' }}
            </h2>

            <form @submit.prevent="saveSchedule">

                <div class="grid md:grid-cols-2 gap-4 mb-6">

                    <!-- HORA DEL HORARIO (TIME) -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="hour_schedule">Hora</label>
                        <input
                            v-model="form.hour_schedule"
                            type="time"
                            id="hour_schedule"
                            required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.hour_schedule }"
                            step="60"
                        />
                        <p v-if="form.errors.hour_schedule" class="text-red-500 text-xs italic">{{ form.errors.hour_schedule }}</p>
                    </div>

                    <!-- FECHA DEL HORARIO (DATE) -->
                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="date_schedule">Fecha</label>
                        <input
                            v-model="form.date_schedule"
                            type="date"
                            id="date_schedule"
                            required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.date_schedule }"
                        />
                        <p v-if="form.errors.date_schedule" class="text-red-500 text-xs italic">{{ form.errors.date_schedule }}</p>
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
                        @click="router.visit(route('schedules.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
