import './bootstrap';

import {createApp} from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

import App from './layouts/App.vue'
import Home from './pages/Home.vue'
import ProductIndex from './pages/sorular/Index.vue'
import ProductCreate from './pages/sorular/Create.vue'
import ProductEdit from './pages/sorular/Edit.vue'
import Admin from './pages/admin/Index.vue'
import profil from "./pages/profil/index.vue";
import sinavolustur from "./pages/sinavolustur/index.vue";
import kategori from "./pages/kategori/index.vue";
import rozetler from "./pages/rozetler/index.vue";
import istatistik from "./pages/istatistik/index.vue";
import scores from "./pages/scores/index.vue";

const routes = [
    {path: '/', component: Home},
    {path: '/admin', component: Admin},
    {path: '/sorular', component: ProductIndex},
    {path: '/sorular/ekle', component: ProductCreate},
    {path: '/sorular/:id', component: ProductEdit},

    {path: '/categories', component: kategori},
    {path: '/exam-create', component: sinavolustur},
    {path: '/categories', component: kategori},
    {path: '/badges', component: rozetler},
    {path: '/statistics', component: istatistik},

    {path: '/scores', component: scores},

    {path: '/profile', component: profil},
];


const router = createRouter({
    history: createWebHistory(),
    routes
})

createApp(App).use(router).mount('#app')
