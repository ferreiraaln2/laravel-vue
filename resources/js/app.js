import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import App from './components/App.vue';

// Import components
import Dashboard from './components/Dashboard.vue';
import PetsList from './components/pets/PetsList.vue';
import PetForm from './components/pets/PetForm.vue';
import ServicesList from './components/services/ServicesList.vue';
import ServiceForm from './components/services/ServiceForm.vue';
import AppointmentsList from './components/appointments/AppointmentsList.vue';
import AppointmentForm from './components/appointments/AppointmentForm.vue';

// Set up axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Set up routes
const routes = [
    { 
        path: '/', 
        component: Dashboard,
        name: 'dashboard'
    },
    { 
        path: '/pets', 
        component: PetsList,
        name: 'pets.index'
    },
    { 
        path: '/pets/create', 
        component: PetForm,
        name: 'pets.create'
    },
    { 
        path: '/pets/:id/edit', 
        component: PetForm,
        name: 'pets.edit',
        props: true
    },
    { 
        path: '/services', 
        component: ServicesList,
        name: 'services.index'
    },
    { 
        path: '/services/create', 
        component: ServiceForm,
        name: 'services.create'
    },
    { 
        path: '/services/:id/edit', 
        component: ServiceForm,
        name: 'services.edit',
        props: true
    },
    { 
        path: '/appointments', 
        component: AppointmentsList,
        name: 'appointments.index'
    },
    { 
        path: '/appointments/create', 
        component: AppointmentForm,
        name: 'appointments.create'
    },
    { 
        path: '/appointments/:id/edit', 
        component: AppointmentForm,
        name: 'appointments.edit',
        props: true
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Create Vue app
const app = createApp(App);
app.use(router);
app.mount('#app'); 