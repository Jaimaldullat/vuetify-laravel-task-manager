/*****************************
 *          IMPORT
 ****************************/

// NPM
import Vue from 'vue';
import VueRouter from "vue-router";
import Vuetify from "vuetify";
import VuetifyConfirm from 'vuetify-confirm'
import axios from 'axios';
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css'

// PROJECT
import App from "./App.vue";
import Dashboard from './components/Dashboard.vue';

const vuetifyInstance = new Vuetify();
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Vue.prototype.$axios = axios;

Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(VuetifyConfirm, {vuetify: vuetifyInstance});

// ROUTES
const routes = [
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard
    },
];

// CREATE VueRouter INSTANCE
const router = new VueRouter({
    mode: 'history',
    routes
});

/*****************************
 *          VUE INSTANCE
 ****************************/
const app = new Vue({
    el: "#app",
    vuetify: vuetifyInstance,
    router,
    render: h => h(App),
});

export default app;
