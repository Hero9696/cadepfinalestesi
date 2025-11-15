import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import 'bootstrap/dist/css/bootstrap.min.css';

// --- 1. MODIFICACIÓN: Importa 'Config' además de 'ZiggyVue' ---
import { ZiggyVue } from 'ziggy-js';
import type { Config } from 'ziggy-js'; // <-- AÑADE ESTO
import { Ziggy } from './ziggy';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            // --- 2. MODIFICACIÓN: Añade 'as Config' para la aserción de tipo ---
            .use(ZiggyVue, Ziggy as Config) // <-- MODIFICA ESTO
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();