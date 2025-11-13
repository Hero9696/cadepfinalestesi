<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface DepartmentProp {
    id_department: number;
    name_department: string;
}

// --- Tipo global correcto de la página ---
interface PageProps extends InertiaBasePageProps {
    department?: DepartmentProp;
    auth: { user: any | null };
    errors?: Record<string, string>;
}

const page = usePage<PageProps>();
const route = (window as any).route;

const departmentProp = page.props.department;
const isEdit = computed(() => !!departmentProp);

// Breadcrumbs
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Departamentos', href: route('departments.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// Formulario Inertia
const form = useForm({
    id_department: departmentProp?.id_department || undefined,
    name_department: departmentProp?.name_department || '',
});

// Guardar / Actualizar
const saveDepartment = () => {
    if (isEdit.value && departmentProp) {
        form.put(route('departments.update', {  id: departmentProp!.id_department}));
    } else {
        form.post(route('departments.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Departamento' : 'Crear Departamento'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-lg">
            
            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Departamento' : 'Crear Nuevo Departamento' }}
            </h2>

            <form @submit.prevent="saveDepartment">

                <!-- ID solo en creación -->
                <div v-if="!isEdit" class="mb-4">
                    <Label for="id_department">ID del Departamento (Código)</Label>
                    <Input
                        v-model.number="form.id_department"
                        type="number"
                        id="id_department"
                        required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.id_department }"
                    />
                    <InputError :message="form.errors.id_department" />
                </div>

                <div class="mb-6">
                    <Label for="name_department">Nombre del Departamento</Label>
                    <Input
                        v-model="form.name_department"
                        type="text"
                        id="name_department"
                        required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        :class="{ 'border-red-500': form.errors.name_department }"
                    />
                    <InputError :message="form.errors.name_department" />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <Button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ isEdit ? 'Actualizar' : 'Guardar' }}
                    </Button>

                    <Button
                        type="button"
                        variant="ghost"
                        @click="router.visit(route('departments.index'))"
                    >
                        Cancelar
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
