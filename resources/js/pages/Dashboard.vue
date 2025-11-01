<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, computed, markRaw } from 'vue'; // <--- markRaw AGREGADO

// --- Importación de Módulos Administrativos ---
import RoleIndex from '@/components/Roles/RoleIndex.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// --- Lógica de Pestañas (CORREGIDO) ---
const tabs = ref([
    { id: 'dashboard', title: 'Resumen', component: markRaw(PlaceholderPattern) }, // Usar markRaw
    { id: 'roles', title: 'Roles', component: markRaw(RoleIndex) },              // USAR markRaw AQUÍ
]);

const activeTab = ref('dashboard');

const CurrentComponent = computed(() => {
    const tab = tabs.value.find(t => t.id === activeTab.value);

    // Si la pestaña es 'dashboard', usa la lógica de renderizado original
    if (tab && tab.id === 'dashboard') {
        return PlaceholderPattern;
    }
    // No es necesario usar markRaw o shallowRef aquí, ya que el componente ya fue marcado en `tabs`.
    return tab ? tab.component : null;
});

const changeTab = (tabId: string) => {
    activeTab.value = tabId;
};
</script>


<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        v-for="tab in tabs" :key="tab.id"
                        @click="changeTab(tab.id)"
                        :class="[
                            tab.id === activeTab
                                ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-bold'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-150'
                        ]"
                    >
                        {{ tab.title }}
                    </button>
                </nav>
            </div>

            <template v-if="activeTab === 'dashboard'">
                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <PlaceholderPattern />
                    </div>
                    <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <PlaceholderPattern />
                    </div>
                    <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <PlaceholderPattern />
                    </div>
                </div>
                <div
                    class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </template>

            <template v-else>
                <div
                    class="relative flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border bg-white dark:bg-gray-800"
                >
                    <component :is="CurrentComponent" :key="activeTab" v-if="CurrentComponent" />
                </div>
            </template>
        </div>
    </AppLayout>
</template>
