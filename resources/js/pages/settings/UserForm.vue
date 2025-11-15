<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';
import { ref, computed } from 'vue';

// ==========================
// TIPOS
// ==========================

interface Role {
  value: number;
  label: string;
}

interface State {
  value: number;
  label: string;
}

interface UserProp {
  id: number;
  name: string;
  email: string;
  id_role_user: number;
  id_state_user: number;
}

interface PageProps extends InertiaBasePageProps {
  user?: UserProp;
  roles: Role[];
  states: State[];
  auth: {
    user: {
      id: number;
    } | null;
  };
  errors?: Record<string, string[]>;
}

// ==========================
// PROPS DE INERTIA
// ==========================

const page = usePage<PageProps>();

const userProp = page.props.user;
const roles = page.props.roles;
const states = page.props.states;

const isEdit = computed(() => !!userProp);
const route = (window as any).route;

// ==========================
// BREADCRUMBS
// ==========================

const breadcrumbs = computed(() => [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Usuarios', href: route('users.index') },
  { title: isEdit.value ? 'Editar Usuario' : 'Crear Usuario', href: '#' },
]);

// ==========================
// FORMULARIO
// ==========================

const form = ref({
  name: userProp?.name || '',
  email: userProp?.email || '',
  password: '',
  id_role_user: userProp?.id_role_user || '',
  id_state_user: userProp?.id_state_user || '',
  idupdater_user_user: page.props.auth?.user?.id ?? 1,
});

const errors = ref<Record<string, string[]>>({});

// ==========================
// GUARDAR
// ==========================

const saveUser = async () => {
  errors.value = {};

  type FormPayload = Omit<typeof form.value, 'password'> & {
    password?: string;
  };

  const payload: FormPayload = { ...form.value };

  // Si está editando y no escribió contraseña → no enviarla
  if (isEdit.value && !payload.password) {
    delete payload.password;
  }

  try {
    if (isEdit.value && userProp) {
      // ✅ CORRECTO → el parámetro se llama "user"
      router.put(
        route('users.update', { user: userProp.id }),
        payload,
        { onError: (errs: any) => (errors.value = errs) }
      );
    } else {
      router.post(
        route('users.store'),
        payload,
        { onError: (errs: any) => (errors.value = errs) }
      );
    }
  } catch (error) {
    console.error('Error saving user:', error);
  }
};
</script>


<template>
  <Head :title="isEdit ? 'Editar Usuario' : 'Crear Usuario'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto p-4 bg-white shadow-md rounded-xl">

      <h2 class="text-2xl font-semibold mb-6">
        {{ isEdit ? 'Editar Usuario' : 'Crear Usuario' }}
      </h2>

      <form @submit.prevent="saveUser">

        <!-- NAME -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
            Nombre de Usuario (Login)
          </label>

          <input
            v-model="form.name"
            type="text"
            id="name"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': errors.name }"
          />

          <p v-if="errors.name" class="text-red-500 text-xs italic">
            {{ errors.name[0] }}
          </p>
        </div>


        <!-- EMAIL -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
            Correo Electrónico
          </label>

          <input
            v-model="form.email"
            type="email"
            id="email"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': errors.email }"
          />

          <p v-if="errors.email" class="text-red-500 text-xs italic">
            {{ errors.email[0] }}
          </p>
        </div>


        <!-- PASSWORD -->
        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
            Contraseña {{ isEdit ? '(Dejar vacío para no cambiar)' : '*' }}
          </label>

          <input
            v-model="form.password"
            type="password"
            id="password"
            :required="!isEdit"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': errors.password }"
          />

          <p v-if="errors.password" class="text-red-500 text-xs italic">
            {{ errors.password[0] }}
          </p>
        </div>


        <!-- ROLE -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
            Rol
          </label>

          <select
            v-model="form.id_role_user"
            id="role"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': errors.id_role_user }"
          >
            <option value="" disabled>Seleccione un rol</option>

            <option
              v-for="role in roles"
              :key="role.value"
              :value="role.value"
            >
              {{ role.label }}
            </option>
          </select>

          <p v-if="errors.id_role_user" class="text-red-500 text-xs italic">
            {{ errors.id_role_user[0] }}
          </p>
        </div>


        <!-- STATE -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="state">
            Estado
          </label>

          <select
            v-model="form.id_state_user"
            id="state"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': errors.id_state_user }"
          >
            <option value="" disabled>Seleccione un estado</option>

            <option
              v-for="state in states"
              :key="state.value"
              :value="state.value"
            >
              {{ state.label }}
            </option>
          </select>

          <p v-if="errors.id_state_user" class="text-red-500 text-xs italic">
            {{ errors.id_state_user[0] }}
          </p>
        </div>


        <!-- BUTTONS -->
        <div class="flex items-center justify-between mt-6">
          <button
            type="submit"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ isEdit ? 'Actualizar' : 'Guardar' }}
          </button>

          <button
            type="button"
            @click="router.visit(route('users.index'))"
            class="font-bold text-sm text-gray-500 hover:text-gray-800"
          >
            Cancelar
          </button>
        </div>

      </form>
    </div>
  </AppLayout>
</template>
