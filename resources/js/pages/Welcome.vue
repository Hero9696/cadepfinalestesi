<script setup lang="ts">
// Importaciones de rutas y componentes de la página de login
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { register } from '@/routes';

// Propiedades combinadas de ambas vistas
defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Gestión de Pacientes CADEP" />

    <!-- CONTENEDOR PRINCIPAL: Fondo con efecto de degradado sutil -->
    <div
        class="flex min-h-screen items-center justify-center bg-gray-50/50 p-4 transition-all duration-300 dark:bg-gray-900/50"
        style="
            background-image: radial-gradient(at 10% 10%, var(--tw-primary-500) 0%, transparent 50%),
                radial-gradient(at 90% 90%, var(--tw-primary-500) 0%, transparent 50%);
            background-size: cover;
            background-repeat: no-repeat;
            --tw-primary-500: #eef2ff; /* Color de fondo claro para el efecto */
        "
    >
        <div
            class="w-full max-w-sm rounded-xl bg-white/90 p-8 shadow-2xl backdrop-blur-sm dark:bg-gray-800/80 md:p-10"
        >
            <!-- TÍTULO Y BIENVENIDA -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-indigo-700 dark:text-indigo-400">
                    Gestión de Pacientes CADEP
                </h1>
                <p class="mt-2 text-md text-gray-600 dark:text-gray-400">
                    Bienvenido al sistema de administración
                </p>
            </div>

            <!-- MENSAJE DE ESTADO (si existe) -->
            <div
                v-if="status"
                class="mb-4 rounded-lg bg-green-50 p-3 text-center text-sm font-medium text-green-600 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ status }}
            </div>

            <!-- FORMULARIO DE LOGIN (usando la estructura que proporcionaste) -->
            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-5"
            >
                <div class="grid gap-5">
                    <!-- Campo de Email -->
                    <div class="grid gap-2">
                        <Label for="email">Correo Electronico</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@example.com"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- Campo de Contraseña -->
                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Contraseña</Label>
                            <!-- Enlace de 'Olvidé mi contraseña' -->
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="text-sm"
                                :tabindex="5"
                            >
                                ¿Olvidaste tu contraseña?
                            </TextLink>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <!-- Checkbox 'Remember me' -->
                    <div class="flex items-center">
                        <Label for="remember" class="flex items-center space-x-3">
                            <Checkbox id="remember" name="remember" :tabindex="3" />
                            <span> Recuerdame</span>
                        </Label>
                    </div>
                </div>

                <!-- Botón de Acceso -->
                <Button
                    type="submit"
                    class="w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    Inicias Sesión
                </Button>

                 <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                Don't have an account?
                <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
            </div>
            </Form>


        </div>
    </div>
</template>

<style scoped>
/* Estilo para asegurar que el fondo se muestre correctamente */
.min-h-screen {
    min-height: 100vh;
}
</style>
