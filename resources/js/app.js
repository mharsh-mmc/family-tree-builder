import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// Import VueFlow components
import { VueFlow, Background, MiniMap, Controls, NodeResizer } from '@vueflow/core';
import '@vueflow/core/dist/style.css';
import '@vueflow/core/dist/theme-default.css';

// Import Pinia for state management
import { createPinia } from 'pinia';

// Import custom components
import FamilyTreeNode from './Components/FamilyTree/NodeComponent.vue';
import FamilyTreeEdge from './Components/FamilyTree/EdgeComponent.vue';
import ProfileModal from './Components/FamilyTree/ProfileModal.vue';
import NodeForm from './Components/FamilyTree/NodeForm.vue';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Use Inertia plugin
        app.use(plugin);
        
        // Use Ziggy for route generation
        app.use(ZiggyVue);
        
        // Use Pinia for state management
        app.use(createPinia());
        
        // Register global VueFlow components
        app.component('VueFlow', VueFlow);
        app.component('Background', Background);
        app.component('MiniMap', MiniMap);
        app.component('Controls', Controls);
        app.component('NodeResizer', NodeResizer);
        
        // Register custom family tree components
        app.component('FamilyTreeNode', FamilyTreeNode);
        app.component('FamilyTreeEdge', FamilyTreeEdge);
        app.component('ProfileModal', ProfileModal);
        app.component('NodeForm', NodeForm);
        
        // Global error handler
        app.config.errorHandler = (err, vm, info) => {
            console.error('Vue Error:', err, info);
            
            // Send error to Laravel log if in production
            if (import.meta.env.PROD) {
                // You can implement error reporting here
                console.error('Error details:', { error: err, component: vm, info });
            }
        };
        
        // Global properties
        app.config.globalProperties.$formatDate = (date) => {
            if (!date) return '';
            return new Date(date).toLocaleDateString();
        };
        
        app.config.globalProperties.$formatAge = (birthDate, deathDate = null) => {
            if (!birthDate) return '';
            
            const birth = new Date(birthDate);
            const end = deathDate ? new Date(deathDate) : new Date();
            const age = end.getFullYear() - birth.getFullYear();
            const monthDiff = end.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && end.getDate() < birth.getDate())) {
                return age - 1;
            }
            
            return age;
        };
        
        app.config.globalProperties.$getRelationColor = (relation) => {
            const colors = {
                'parent': 'bg-blue-100 text-blue-800',
                'child': 'bg-green-100 text-green-800',
                'spouse': 'bg-pink-100 text-pink-800',
                'sibling': 'bg-purple-100 text-purple-800',
                'grandparent': 'bg-indigo-100 text-indigo-800',
                'grandchild': 'bg-teal-100 text-teal-800',
                'aunt': 'bg-orange-100 text-orange-800',
                'uncle': 'bg-yellow-100 text-yellow-800',
                'cousin': 'bg-red-100 text-red-800',
                'niece': 'bg-emerald-100 text-emerald-800',
                'nephew': 'bg-cyan-100 text-cyan-800',
                'in-law': 'bg-gray-100 text-gray-800',
                'step': 'bg-slate-100 text-slate-800',
                'adopted': 'bg-amber-100 text-amber-800',
                'foster': 'bg-lime-100 text-lime-800'
            };
            
            return colors[relation.toLowerCase()] || 'bg-gray-100 text-gray-800';
        };
        
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});