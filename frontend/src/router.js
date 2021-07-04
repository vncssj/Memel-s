import Vue from "vue";
import VueRouter from "vue-router";

import Form from "./views/Form.vue"
import Home from "./views/Home.vue"


Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        component: Home
    },
    {
        path: '/form',
        component: Form
    },
]

export default new VueRouter({
    mode: 'history',
    routes
});