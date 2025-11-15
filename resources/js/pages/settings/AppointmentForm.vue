<script setup lang="ts">
import { computed } from 'vue';
import { usePage, useForm, Head, router } from '@inertiajs/vue3';
import type { PageProps as InertiaBasePageProps } from '@inertiajs/core';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';

// -----------------------------------------------------
// TIPOS E INTERFACES
// -----------------------------------------------------

// Interfaces para las props que el controlador nos pasa
interface Employee {
  id_employee: number;
  user: { // Asumimos que la relación 'user' existe para el nombre
    name: string;
  };
  // puedes añadir más campos si los necesitas, ej: profession_employee
}

interface State {
  id_state: number;
  name_state: string;
}

interface AppointmentProp {
  id_appointment: number;
  id_employee_appointment: number;
  date_appointment: string;
  id_state_appointment: number;
  // ...otros campos de la cita
}

// Extender PageProps para incluir las props de esta página
interface PageProps extends InertiaBasePageProps {
  appointment?: AppointmentProp; // La cita (opcional, solo en edición)
  employees: Employee[]; // Lista de empleados
  states: State[]; // Lista de estados
  auth: {
    user: {
      id: number;
    } | null;
  };
  // ...otras props globales
}

// -----------------------------------------------------
// SETUP INICIAL (Props y Formulario)
// -----------------------------------------------------

const page = usePage<PageProps>();
const route = (window as any).route;

// Obtenemos las props pasadas por AppointmentController
const appointmentProp = page.props.appointment;
const employees = page.props.employees;
const states = page.props.states;

const isEdit = computed(() => !!appointmentProp);

// Usamos el hook useForm de Inertia
const form = useForm({
  id_employee_appointment: appointmentProp?.id_employee_appointment || '',
  date_appointment: appointmentProp?.date_appointment || '',
  id_state_appointment: appointmentProp?.id_state_appointment || '',
  // NOTA: 'idupdater_user_appointment' se maneja en el backend con Auth::id()
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Agenda', href: route('appointments.index') },
  { title: isEdit.value ? 'Editar Cita' : 'Nueva Cita', href: '#' },
]);

// -----------------------------------------------------
// ENVÍO DE FORMULARIO
// -----------------------------------------------------

const saveAppointment = () => {
  if (isEdit.value && appointmentProp) {
    // Modo Edición: usa form.put()
    form.put(route('appointments.update', { appointment: appointmentProp.id_appointment }), {
      // onSuccess: () => ... (puedes añadir notificaciones aquí)
    });
  } else {
    // Modo Creación: usa form.post()
    form.post(route('appointments.store'), {
      // onSuccess: () => ...
    });
  }
};
</script>

<template>
  <Head :title="isEdit ? 'Editar Cita' : 'Nueva Cita'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- 
      El formulario ya está dentro del AppLayout (con sidebar).
      No es necesario (y es incorrecto) anidar <AuthBase> aquí.
    -->
    <div class="p-4 max-w-2xl mx-auto bg-white rounded-xl shadow-md">
      
      <h2 class="text-2xl font-semibold mb-6">
        {{ isEdit ? 'Editar Cita Agendada' : 'Agendar Nueva Cita' }}
      </h2>
      
      <form @submit.prevent="saveAppointment" class="flex flex-col gap-6">
        
        <!-- Campo Terapeuta -->
        <div class="grid gap-2">
          <Label for="id_employee">Terapeuta / Empleado</Label>
          <select
            id="id_employee"
            v-model="form.id_employee_appointment"
            required
            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
          >
            <option value="" disabled>Seleccione un Terapeuta</option>
            <option v-for="emp in employees" :key="emp.id_employee" :value="emp.id_employee">
              {{ emp.user.name }}
            </option>
          </select>
          <!-- 
            InputError ahora funciona automáticamente 
            porque 'form.errors' es un objeto simple (string), no un array.
          -->
          <InputError :message="form.errors.id_employee_appointment" />
        </div>

        <!-- Campo Fecha y Hora -->
        <div class="grid gap-2">
          <Label for="date_appointment">Fecha y Hora de Inicio</Label>
          <Input
            id="date_appointment"
            type="datetime-local"
            v-model="form.date_appointment"
            required
            name="date_appointment"
          />
          <InputError :message="form.errors.date_appointment" />
        </div>

        <!-- Campo Estado -->
        <div class="grid gap-2">
          <Label for="id_state">Estado de la Cita</Label>
          <select
            id="id_state"
            v-model="form.id_state_appointment"
            required
            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
          >
            <option value="" disabled>Seleccione un Estado</option>
            <option v-for="state in states" :key="state.id_state" :value="state.id_state">
              {{ state.name_state }}
            </option>
          </select>
          <InputError :message="form.errors.id_state_appointment" />
        </div>
        
        <!-- Botón de Envío -->
        <Button
          type="submit"
          class="mt-6 w-full"
          :disabled="form.processing"
        >
          <LoaderCircle
            v-if="form.processing"
            class="h-4 w-4 animate-spin"
          />
          {{ isEdit ? 'Actualizar Cita' : 'Agendar Cita' }}
        </Button>
        
        <!-- Botón de Cancelar -->
        <Button
          type="button"
          variant="ghost"
          @click="router.visit(route('appointments.index'))"
          :disabled="form.processing"
        >
          Cancelar
        </Button>

      </form>
    </div>
  </AppLayout>
</template>
