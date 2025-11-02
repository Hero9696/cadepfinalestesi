<script setup lang="ts">
import { ref, onMounted } from 'vue'; // Nuevo: para lógica asíncrona
import axios, { type AxiosError } from 'axios'; // Nuevo: para peticiones API
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

// --- INTERFACES DE DATOS ---
interface SelectOption {
    id_role?: number;
    id_state?: number;
    name_role?: string;
    name_state?: string;
}

// --- ESTADO LOCAL ---
const roles = ref<Partial<SelectOption>[]>([]);
const states = ref<Partial<SelectOption>[]>([]);

// Obtiene el formulario de Fortify/Inertia.
// CORRECCIÓN: Se usa un casting (as Record<string, any>) para permitir
// las propiedades personalizadas (id_role_user, id_state_user) que faltan en la definición de tipo de Fortify.
const form = store.form() as Record<string, any>; 

// --- LÓGICA DE CARGA DE DEPENDENCIAS ---
onMounted(() => {
    fetchDependencies();
});

const fetchDependencies = async () => {
    try {
        const [rolesResponse, statesResponse] = await Promise.all([
            axios.get<Partial<SelectOption>[]>('/api/roles'),
            axios.get<Partial<SelectOption>[]>('/api/states')
        ]);

        roles.value = rolesResponse.data;
        states.value = statesResponse.data;

        // Inicializar el formulario con el primer valor si existen datos
        if (roles.value.length > 0 && !form.id_role_user) {
            form.id_role_user = roles.value[0].id_role;
        }
        if (states.value.length > 0 && !form.id_state_user) {
            form.id_state_user = states.value[0].id_state;
        }

    } catch (error) {
        const axiosError = error as AxiosError;
        console.error('Error al cargar Roles/Estados (posible 401 o 404):', axiosError.response?.statusText || axiosError.message);
        // Si el error es 401 o 404, la aplicación no debe usarse hasta que las rutas API estén disponibles
    }
};

</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-bind="form"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- 1. NAME (USUARIO) -->
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <!-- 2. EMAIL -->
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <!-- 3. ROL (NUEVO CAMPO REQUERIDO) -->
                <div class="grid gap-2">
                    <Label for="id_role_user">Rol del Usuario</Label>
                    <!-- Usamos v-model y name para Inertia -->
                    <select
                        id="id_role_user"
                        v-model="form.id_role_user"
                        name="id_role_user"
                        required
                        :tabindex="3"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="" disabled>Seleccione un Rol</option>
                        <option v-for="role in roles" :key="role.id_role" :value="role.id_role">
                            {{ role.name_role }}
                        </option>
                    </select>
                    <InputError :message="errors.id_role_user" />
                </div>

                <!-- 4. ESTADO (NUEVO CAMPO REQUERIDO) -->
                <div class="grid gap-2">
                    <Label for="id_state_user">Estado Inicial</Label>
                    <select
                        id="id_state_user"
                        v-model="form.id_state_user"
                        name="id_state_user"
                        required
                        :tabindex="4"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="" disabled>Seleccione un Estado</option>
                        <option v-for="state in states" :key="state.id_state" :value="state.id_state">
                            {{ state.name_state }}
                        </option>
                    </select>
                    <InputError :message="errors.id_state_user" />
                </div>


                <!-- 5. PASSWORD -->
                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <!-- 6. CONFIRM PASSWORD -->
                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="6"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="7"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="8"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
