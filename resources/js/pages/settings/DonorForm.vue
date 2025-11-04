<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

// --- TIPOS ---
interface DonorProp {
    id_donor: string;
    email_donor: string;
    title_donor: string | null;
    firstname_donor: string;
    lastname_donor: string;
    country_donor: string;
    zipcode_donor: string;
    state_donor: string;
    address_donor: string;
    unit_donor: string | null;
    city_donor: string;
    phone_donor: string | null;
    mobile_donor: string | null;
}

interface PageProps {
    donor?: DonorProp;
    auth: { user: { id: number } };
    errors?: Record<string, string>;
}

// --- PROPS E INICIALIZACIÓN ---
const page = usePage<PageProps>();
const route = (window as any).route;

const donorProp = page.props.donor;
const isEdit = computed(() => !!donorProp);

// --- BREADCRUMBS ---
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Donantes', href: route('donors.index') },
    { title: isEdit.value ? 'Editar' : 'Crear', href: '#' },
]);

// --- FORMULARIO DE INERTIA ---
const form = useForm({
    // Sección Personal/Contacto
    // Nota: El campo id_donor es necesario solo en la creación si no es autoincremental,
    // pero Laravel lo maneja en el update mediante el Model Binding, por lo que lo omitimos aquí para simplificar.
    title_donor: donorProp?.title_donor || '',
    firstname_donor: donorProp?.firstname_donor || '',
    lastname_donor: donorProp?.lastname_donor || '',
    email_donor: donorProp?.email_donor || '',
    phone_donor: donorProp?.phone_donor || '',
    mobile_donor: donorProp?.mobile_donor || '',

    // Sección Dirección
    address_donor: donorProp?.address_donor || '',
    unit_donor: donorProp?.unit_donor || '',
    city_donor: donorProp?.city_donor || '',
    state_donor: donorProp?.state_donor || '',
    zipcode_donor: donorProp?.zipcode_donor || '',
    country_donor: donorProp?.country_donor || '',

    // Auditoría (Si se envía esto, Laravel lo recibirá, aunque es mejor que Laravel lo asigne)
    idupdater_user_donor: page.props.auth.user.id,
});

// --- LÓGICA DE GUARDADO ---
const saveDonor = () => {

    const url = isEdit.value
        ? route('donors.update', { donor: donorProp!.id_donor }) // CORRECCIÓN: Usar 'donor' como parámetro
        : route('donors.store');

    // AÑADIDO: BEFORE Hook para ver los datos del formulario
    console.log('--- Datos del Formulario (Before Submit) ---');
    console.log(`Modo: ${isEdit.value ? 'Actualización (PUT)' : 'Creación (POST)'}`);
    console.log(`Ruta: ${url}`);
    // Usamos form.data() para obtener una copia de los datos a enviar
    console.log('Datos a enviar:', form.data());
    console.log('-------------------------------------------');


    if (isEdit.value) {
        form.put(url, {
            onSuccess: () => router.visit(route('donors.index')),
        });
    } else {
        form.post(url, {
            onSuccess: () => router.visit(route('donors.index')),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Donante' : 'Registrar Donante'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 bg-white shadow-md rounded-xl max-w-4xl">

            <h2 class="text-2xl font-semibold mb-6">
                {{ isEdit ? 'Editar Donante (ID: ' + donorProp?.id_donor + ')' : 'Registrar Nuevo Donante' }}
            </h2>

            <form @submit.prevent="saveDonor">

                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Datos Personales y Contacto</h3>
                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="title_donor">Título (Sr/a)</label>
                        <input v-model="form.title_donor" type="text" id="title_donor"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="firstname_donor">Nombre(s) *</label>
                        <input v-model="form.firstname_donor" type="text" id="firstname_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.firstname_donor }" />
                        <p v-if="form.errors.firstname_donor" class="text-red-500 text-xs italic">{{ form.errors.firstname_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="lastname_donor">Apellido(s) *</label>
                        <input v-model="form.lastname_donor" type="text" id="lastname_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.lastname_donor }" />
                        <p v-if="form.errors.lastname_donor" class="text-red-500 text-xs italic">{{ form.errors.lastname_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="email_donor">Email *</label>
                        <input v-model="form.email_donor" type="email" id="email_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.email_donor }" />
                        <p v-if="form.errors.email_donor" class="text-red-500 text-xs italic">{{ form.errors.email_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="phone_donor">Teléfono Fijo</label>
                        <input v-model="form.phone_donor" type="text" id="phone_donor"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="mobile_donor">Teléfono Móvil</label>
                        <input v-model="form.mobile_donor" type="text" id="mobile_donor"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>
                </div>

                <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-6">Dirección de Envío / Facturación</h3>
                <div class="grid md:grid-cols-4 gap-4 mb-4">

                    <div class="grid gap-2 col-span-2">
                        <label class="block text-gray-700 text-sm font-bold" for="address_donor">Dirección (Calle y Número) *</label>
                        <input v-model="form.address_donor" type="text" id="address_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.address_donor }" />
                        <p v-if="form.errors.address_donor" class="text-red-500 text-xs italic">{{ form.errors.address_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="unit_donor">Apto/Unidad (Opcional)</label>
                        <input v-model="form.unit_donor" type="text" id="unit_donor"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="city_donor">Ciudad *</label>
                        <input v-model="form.city_donor" type="text" id="city_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.city_donor }" />
                        <p v-if="form.errors.city_donor" class="text-red-500 text-xs italic">{{ form.errors.city_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="state_donor">Estado/Región *</label>
                        <input v-model="form.state_donor" type="text" id="state_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.state_donor }" />
                        <p v-if="form.errors.state_donor" class="text-red-500 text-xs italic">{{ form.errors.state_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="zipcode_donor">Código Postal *</label>
                        <input v-model="form.zipcode_donor" type="text" id="zipcode_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.zipcode_donor }" />
                        <p v-if="form.errors.zipcode_donor" class="text-red-500 text-xs italic">{{ form.errors.zipcode_donor }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-gray-700 text-sm font-bold" for="country_donor">País *</label>
                        <input v-model="form.country_donor" type="text" id="country_donor" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700"
                            :class="{ 'border-red-500': form.errors.country_donor }" />
                        <p v-if="form.errors.country_donor" class="text-red-500 text-xs italic">{{ form.errors.country_donor }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ isEdit ? 'Actualizar Donante' : 'Guardar Donante' }}
                    </button>
                    <button
                        type="button"
                        @click="router.visit(route('donors.index'))"
                        class="font-bold text-sm text-gray-500 hover:text-gray-800"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
