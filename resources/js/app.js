import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { createVuetify } from 'vuetify';
import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css' // Import MDI icons CSS
import { aliases, mdi } from 'vuetify/iconsets/mdi';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import Toast from 'vue3-toastify';
import 'vue3-toastify/dist/index.css'; // Đảm bảo đường dẫn CSS chính xác
import { CkeditorPlugin } from '@ckeditor/ckeditor5-vue';







const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(vuetify) // Sử dụng Vuetify trong ứng dụng
            .use(Toast) // Thêm cấu hình cho Toast
            .use( CkeditorPlugin )
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
