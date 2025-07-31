import { createRouter, createWebHistory } from "vue-router";
import store from "./Store/index.js"

const routes = [
    {
      path:"/",
      component:()=> import("./Pages/Dashboard.vue")
    },
    {
      path:"/test",
      component:() => import("./Pages/TestRoute.vue")
    },
    {
        path:"/details",
        component: () => import("./Pages/Details.vue"),
    },
    {
        path:"/test",
        component: () => import("./Pages/TestRoute.vue")
    },
    {
        path:"/goals",
        component: () => import('./Pages/GoalsCard.vue')
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
        component:() => import('./Pages/Details.vue')
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
    },
    {
        path:"/settings",
        component:() => import('./Pages/Settings.vue')
    },
    {
        path:"/demo",
        component:() =>import('./Pages/Demo.vue')
    },
    {
        path:"/profile",
        component:()=>import('./Pages/ProfilePage.vue')
    }


]

const router =  createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) =>{
    if (!store.getters.isAuthenticated && to.path !== '/register'  && to.path !== '/login' && to.path !== '/demo'){
        console.log("You are not authenticated.");
        next('/register');
    } else {
        next();
    }
})

export default router;
