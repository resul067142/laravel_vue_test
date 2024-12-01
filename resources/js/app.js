import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

import App from './layouts/App.vue';
import Vue3TouchEvents from 'vue3-touch-events';

// Vue I18n'i import edin
import { createI18n } from 'vue-i18n';
import messages from './i18n/messages.js';

// main.js veya app.js
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8000'; // Laravel projenizin URL'si


import Home from './pages/Home.vue';
import ProductIndex from './pages/sorular/Index.vue';
import ProductCreate from './pages/sorular/Create.vue';
import ProductEdit from './pages/sorular/Edit.vue';
import Admin from './pages/admin/Index.vue';
import profil from "./pages/profil/index.vue";
import sinavolustur from "./pages/sinavolustur/index.vue";
import kategori from "./pages/kategori/index.vue";
import rozetler from "./pages/rozetler/index.vue";
import istatistik from "./pages/istatistik/index.vue";
import scores from "./pages/scores/index.vue";
import ProfilDüzenle from './pages/profil/profildüzenle.vue';
import kayitol from "./pages/profil/kayitol.vue";
import giris from "./pages/profil/giris.vue";
import video from "./pages/egitim/video.vue";
import belge from "./pages/egitim/belge.vue";
import harita from "./pages/harita/index.vue";
import antireman from "./pages/egitim/antreman.vue";
import dosya from "./pages/egitim/dosya.vue";
import puanlama from "./pages/puanlama/puanlama.vue";
import soytakip from "./pages/egitim/soytakip.vue";
import sesmetin from "./pages/egitim/sesmetin.vue";
import aile from "./pages/egitim/ailemm.vue";
import kisitespit from "./pages/egitim/kisitespit.vue";

const routes = [
    { path: '/', component: Home },
    { path: '/admin', component: Admin },
    { path: '/sorular', component: ProductIndex },
    { path: '/sorular/ekle', component: ProductCreate },
    { path: '/sorular/:id', component: ProductEdit },
    { path: '/categories', component: kategori },
    { path: '/exam-create', component: sinavolustur },
    { path: '/badges', component: rozetler },
    { path: '/statistics', component: istatistik },
    { path: '/scores', component: scores },
    { path: '/profile', component: profil },
    { path: '/kayitol', component: kayitol },
    { path: '/giris', component: giris },
    { path: '/video', component: video },
    { path: '/belge', component: belge },
    { path: '/harita', component: harita },
    { path: '/antreman', component: antireman },
    { path: '/dosya', component: dosya },
    { path: '/puanlama', component: puanlama },
    { path: '/soytakip', component: soytakip },
    { path: '/profile/edit', component: ProfilDüzenle },
    { path: '/sesmetin', component: sesmetin },
    { path: '/aile', component: aile },
    { path: '/kisitespit', component: kisitespit }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// i18n nesnesini oluşturun
const i18n = createI18n({
    locale: 'tr', // Varsayılan dil
    fallbackLocale: 'en', // Yedek dil
    messages, // Dil mesajları
});

const app = createApp(App);

app.use(router);
app.use(i18n); // i18n'i uygulamaya dahil edin
app.use(Vue3TouchEvents);  // vue3-touch-events kütüphanesini kullanıma açın
app.mount('#app');
