import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path:"/",
        component: () => import("./Pages/Dashboard.vue"),
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
    {
        path:"/incomes",
        component:() => import('./Pages/Income.vue')

    },
    {
        path:"/expenses",
        component:() => import('./Pages/Expense.vue')
    },
    {
        path:"/dashboard",
        component:() => import('./Pages/Dashboard.vue')
    },
    {
        path:"/schedules",
        component:() => import('./Pages/Schedules.vue')
    },
    {
        path:"/register",
        component:() => import('./Pages/Register.vue')
    },
    {
        path:"/login",
        component:() => import('./Pages/Login.vue')
    }


]

export default createRouter({
    history: createWebHistory(),
    routes,
});
