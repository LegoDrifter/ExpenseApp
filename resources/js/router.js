import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path:"/",
        component: () => import("./Pages/HomeRoute.vue"),
    },
    {
        path:"/test",
        component: () => import("./Pages/TestRoute.vue")
    },
    {
        path:"/goals",
        component: () => import('./Pages/Goals.vue')
    },
    {
        path:"/balances",
        component: () => import('./Pages/Balance.vue')
    },

]

export default createRouter({
    history: createWebHistory(),
    routes,
});
