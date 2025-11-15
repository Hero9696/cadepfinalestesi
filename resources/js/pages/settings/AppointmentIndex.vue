<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import FullCalendar, { DateSelectArg } from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router } from '@inertiajs/vue3'; // Importar router

// Eliminamos la importación de AppointmentForm ya que se cargará como página Inertia separada.

// Declaración segura de route (asumiendo que Ziggy está globalmente disponible)
const route = (window as any).route;

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Agenda', href: route('appointments.index') }, // <-- AQUÍ FALLA
]);

// ---------------------------------------------------------------------
// 1. ELIMINACIÓN DE ESTADO MODAL
// ---------------------------------------------------------------------

// const showModal = ref(false); // ELIMINADO
// const selectedEventData = ref(null); // ELIMINADO

// ---------------------------------------------------------------------
// 2. HANDLERS DE ACCIONES DE FULLCALENDAR (REDIRECCIÓN)
// ---------------------------------------------------------------------

/**
 * Redirige al formulario de creación/edición de citas.
 * @param start Fecha de inicio preseleccionada.
 * @param end Fecha de fin preseleccionada.
 * @param eventId ID del evento para edición.
 */
const navigateToAppointmentForm = (data: { start?: string, end?: string, eventId?: number }) => {
    
    // Si se pasa un eventId (Editar Cita)
    if (data.eventId) {
        // Asumimos que tienes una ruta Inertia llamada 'appointments.edit'
        router.visit(route('appointments.edit', { appointment: data.eventId }));
    } 
    // Si se pasa un rango de tiempo (Nueva Cita)
    else if (data.start) {
        // Asumimos que tienes una ruta Inertia llamada 'appointments.create'
        // Pasamos las fechas como parámetros de query para precargar el formulario
        router.visit(route('appointments.create', { start: data.start, end: data.end }));
    } else {
        // Agendar Manual
        router.visit(route('appointments.create'));
    }
};

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    locale: 'es', 
    allDaySlot: false,
    slotMinTime: '08:00:00',
    slotMaxTime: '18:00:00',
    height: 'auto',
    editable: true,
    selectable: true,
    eventStartEditable: true,
    eventDurationEditable: true,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek,timeGridDay,dayGridMonth'
    },
    events: '/appointments/events', 

    // Handler al seleccionar un rango de tiempo (NUEVA CITA)
    select: (selectInfo: DateSelectArg) => {
        // Redirigir y pasar las fechas
        navigateToAppointmentForm({ start: selectInfo.startStr, end: selectInfo.endStr });
    },
    
    // Handler al hacer clic en un evento (EDITAR CITA)
    eventClick: (clickInfo: any) => {
        // Redirigir y pasar el ID del evento (que debe ser el id_appointment)
        navigateToAppointmentForm({ eventId: clickInfo.event.id });
    },

    // Handler al arrastrar o redimensionar un evento (ACTUALIZAR FECHA/HORA)
    eventChange: async (changeInfo: any) => {
        // Lógica de actualización AJAX (Mantenida)
        const event = changeInfo.event;
        const confirmed = window.confirm(`¿Estás seguro de cambiar la cita de ${event.title} a ${event.startStr}?`);

        if (confirmed) {
            try {
                await axios.put(`/appointments/${event.id}`, {
                    start: event.startStr,
                    end: event.endStr,
                });
                console.log('Cita actualizada en la base de datos.');
            } catch (error) {
                console.error('Error updating appointment via eventChange:', error);
                changeInfo.revert();
                alert('No se pudo actualizar la cita. Verifique la disponibilidad.');
            }
        } else {
            changeInfo.revert();
        }
    },
});

onMounted(() => {
    // Si esta vista se usa como pestaña, no necesita cargar nada al inicio, FullCalendar lo hace.
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Agenda de Citas (FullCalendar)</h3>
                <button 
                    @click="navigateToAppointmentForm({})"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                >
                    ➕ Agendar Cita Manual
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <FullCalendar 
                    :options="calendarOptions"
                    class="appointment-calendar"
                />
            </div>
            
            </div>
    </AppLayout>
</template>

<style scoped>
/* Estilos para que el calendario se vea bien */
.appointment-calendar {
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
}
</style>