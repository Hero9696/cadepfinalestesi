<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS ---
interface WeekDayProp {
    id_week: number;
    name_week: string;
}

interface PageProps {
    weekDay?: WeekDayProp;
    errors?: Record<string, string>;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const weekDayProp = page.props.weekDay;

const isEdit = computed(() => !!weekDayProp);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Días de la Semana', href: route('week.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    id_week: weekDayProp?.id_week || null,
    name_week: weekDayProp?.name_week || '',
});


// --- LÓGICA DE GUARDADO ---
const saveWeekDay = () => {

    const url = isEdit.value
        ? route('week.update', { week: weekDayProp!.id_week })
        : route('week.store');

    if (isEdit.value) {
        // PUT para edición
        form.put(url, {
            onSuccess: () => router.visit(route('week.index')),
        });
    } else {
        // POST para creación
        form.post(url, {
            onSuccess: () => router.visit(route('week.index')),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Día' : 'Crear Día'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-lg">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Día de la Semana' : 'Crear Nuevo Día de la Semana' }}
            </h2>

            <form @submit.prevent="saveWeekDay">

                <div v-if="!isEdit" class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="id_week">
                        ID del Día (1-7)
                    </label>
                    <input
                        v-model="form.id_week"
                        type="number"
                        id="id_week"
                        required
                        min="1"
                        max="7"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.id_week }"
                    />
                    <p v-if="form.errors.id_week" class="text-red-500 text-xs italic">
                        {{ form.errors.id_week }}
                    </p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name_week">
                        Nombre del Día
                    </label>
                    <input
                        v-model="form.name_week"
                        type="text"
                        id="name_week"
                        required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.name_week }"
                    />
                    <p v-if="form.errors.name_week" class="text-red-500 text-xs italic">
                        {{ form.errors.name_week }}
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
                        @click="router.visit(route('week.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
