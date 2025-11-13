<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup, // Importamos para agrupar elementos
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Settings,
    Users,
    //Tag,
    Flag,
    //Home,
    Map,
    //Calendar,
  //  Archive,
  CalendarCheck,
    Briefcase,
    Globe, // Para Departamentos/Municipios
    Building, // Para Sucursales
    Star, // Para Roles
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
const route = (window as any).route;
// --- ELEMENTOS DE NAVEGACIÓN PRINCIPAL ---

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        // Aseguramos que el Dashboard se cargue primero sin pestañas específicas
        href: dashboard(),
        icon: LayoutGrid,
    },
    // Nota: Aquí se pueden añadir Pacientes, Empleados, etc., como módulos principales.
];

// --- ELEMENTOS DE CONFIGURACIÓN Y ADMINISTRACIÓN ---

const adminNavItems: NavItem[] = [
    {
        title: 'Usuarios',
        // --- ¡CAMBIO PRINCIPAL AQUÍ! ---
        // Apunta a la nueva ruta 'users.index' que creamos en web.php
        href: route('users.index'),
        icon: Users,
    },
    {
        title: 'Roles',
        href: route('role.index'),
        icon: Star,
    },
     {
        title: 'Citas',
        href: route('appointments.index'),
        icon: CalendarCheck,
    },
    {
        title: 'Estados',
        href: dashboard(),
        icon: Flag,
    },
    {
        title: 'Departamentos',
        href: route('departments.index'),
        icon: Globe,
    },
    {
        title: 'Municipios',
        href: route('municipalities.index'),
        icon: Map,
    },
    {
        title: 'Sucursales',
        href: route('branches.index'),
        icon: Building,
    },
    {
        title: 'Áreas Terapia',
        href: route('areas.index'),
        icon: Briefcase,
    },
    {
        title: 'Horarios',
        href: route('schedules.index'),
        icon: CalendarCheck,
    },
    {
        title: 'Días de la Semana',
        href: route('week.index'),
        icon: CalendarCheck,
    },
    {
        title: 'Empleados',
        href: route('employees.index'),
        icon: Briefcase,
    },
    {
        title: 'Pacientes',
        href: route('patients.index'),
        icon: Users,
    },
<<<<<<< HEAD
    {
        title: 'Parentescos',
        href: route('relatives.index'),
        icon: Users,
    },
    {
        title: 'Donantes',
        href: route('donors.index'),
        icon: Star,
    },
    {
        title: 'Donaciones',
        href: route('donations.index'),
        icon: Star,
    },

=======
>>>>>>> parent of 6025cb8 (relative ready)
    // Añadir rutas de Donadores, Empleados y Citas si son módulos principales
];

// --- ELEMENTOS DEL PIE DE PÁGINA (LIMPIOS) ---
const footerNavItems: NavItem[] = [
    // El pie de página ha sido limpiado según tu solicitud
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <SidebarGroup>
                <template #header>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="sm" class="font-semibold text-gray-500 hover:text-gray-700">
                            <Settings class="h-4 w-4" />
                            <span>Configuración</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>

                <SidebarMenu>
                    <SidebarMenuItem v-for="item in adminNavItems" :key="item.title">
                        <SidebarMenuButton size="sm" as-child>
                            <Link :href="item.href">
                                <component :is="item.icon" class="h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
