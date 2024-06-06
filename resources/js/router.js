import { createRouter, createWebHistory } from "vue-router";
import store from "./Store/index.js"

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

const router =  createRouter({
    history: createWebHistory(),
    routes,
});

// router.beforeEach((to, from, next) =>{
//     // if (!store.getters.isAuthenticated && to.name !== 'Login'){
//     //     next({name:'Login', query:{redirect:to.fullPath}})
//     // }else{
//     //     next();
//     // }
// })

export default router;
