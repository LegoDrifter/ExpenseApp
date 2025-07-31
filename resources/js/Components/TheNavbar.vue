<template>
    <nav class="bg-stone-100 p-4 shadow-lg mb-1" :key="isAuthenticated">
        <div class="container mx-auto ">
            <div :class="{'flex gap-4 justify-space-between':isAuthenticated,'flex gap-4 justify-center':!isAuthenticated }">
                <div v-if="!isAuthenticated" class="flex center">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div>
                            <img class="h-16 w-16" :src="imageSrc" alt="None"/>
                        </div>
                        <div class="">
                            <h1 class="font-oswald font-bold  text-2xl text-slateGray hover:text-slateLight">ExpenseTracker</h1>
                        </div>

                    </div>
                </div>
                <div v-if="isAuthenticated">
<!--                    <router-link to="/" class="text-slateGray text-lg font-bold">ExpenseTracker</router-link>-->
                    <router-link to="/" >
                        <div class="flex gap-2  ">
                            <div>
                                <img class="h-16 w-16" :src="imageSrc" alt="None"/>
                            </div>
                            <div class="text-2xl mt-3 md:hidden">
                                <h1 class="font-oswald font-bold md:mt-4 md:text-2xl text-slateGray hover:text-slateLight">ExpenseTracker</h1>
                            </div>
                            <div class="text-2xl  hidden md:block">
                                <h1 class="font-oswald font-bold md:mt-4 md:text-2xl text-slateGray hover:text-slateLight">ExpenseTracker</h1>
                            </div>
<!--                        -->
                        </div>
                    </router-link>

                </div>
                <div v-if="isAuthenticated" class="flex items-center md:hidden">
                    <v-app-bar-nav-icon size="large" color="slateGray" @click="toggleDrawer"></v-app-bar-nav-icon>
                    <v-layout>
                        <v-navigation-drawer v-model="drawer" app temporary>
                            <v-list>
                                <v-list-item v-for="item in menuItems" :key="item.title" :to="item.to" link>
                                    <div v-if="item.type === 'link'" class="flex gap-2">
                                        <v-icon color="slateGray">{{ item.icon }}</v-icon>
                                        <v-list-item-title><span class="text-slateGray font-bold">{{ item.title }}</span></v-list-item-title>
                                    </div>
                                    <div @click="logout" v-else-if="item.type === 'button'" class="flex gap-1 cursor-pointer">
                                        <v-icon color="slateGray">{{ item.icon }}</v-icon>
                                        <v-list-item-title><span class="text-slateGray font-bold">{{ item.title }}</span></v-list-item-title>
                                    </div>
                                </v-list-item>
                            </v-list>
                        </v-navigation-drawer>
                    </v-layout>

                </div>
<!--                <div v-else>-->
<!--                    <span class="text-white text-lg font-bold">ExpenseTracker</span>-->
<!--                </div>-->

                <div v-if="isAuthenticated" class="hidden md:flex items-center space-x-4 ">
                    <router-link to="/goals" class="text-slateGray font-oswald text-xl hover:text-slateLight font-bold">Goals</router-link>
<!--                    <router-link to="/balances" class="text-gray-300 hover:text-white">Balance</router-link>-->
                    <router-link to="/incomes" class="text-slateGray font-oswald text-xl  font-bold hover:text-slateLight">Incomes</router-link>
                    <router-link to="/expenses" class="text-slateGray font-oswald text-xl  font-bold hover:text-slateLight">Expenses</router-link>
                    <router-link to="/schedules" class="text-slateGray font-oswald text-xl  font-bold hover:text-slateLight">Schedules</router-link>
                    <router-link to="/details" class="text-slateGray font-oswald text-xl  font-bold hover:text-slateLight">Details</router-link>

                </div>
                <div v-if="isAuthenticated" class="hidden md:flex items-center">
                    <v-menu
                        open-on-hover
                    >
                        <template v-slot:activator="{ props }">
                            <v-btn
                                color="#3B5555"
                                class="font-oswald"
                                v-bind="props"
                            >

                                {{user.username}}
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item
                                v-for="(item, index) in dropItems"
                                :key="index"
                            >
                                <router-link v-if="item.type ==='link'" :to="item.to" class="flex gap-1">
                                    <v-icon color="slateGray">{{ item.icon }}</v-icon>
                                    <v-list-item-title><span class="font-bold">{{ item.title }}</span></v-list-item-title>
                                </router-link>
                                <div v-else-if="item.type ==='button'">
<!--                                    <button class="bg-slateGray text-white rounded-md px-2 py-1 text-xs font-bold" @click="logout">{{ item.title }}</button>-->
                                    <div @click="logout" class="flex gap-1 cursor-pointer">
                                        <v-icon color="slateGray">{{item.icon}}</v-icon>
                                        <v-list-item-title><span class="font-bold">{{item.title}}</span></v-list-item-title>
                                    </div>
                                </div>


                            </v-list-item>
                        </v-list>
                    </v-menu>
<!--                    <router-link :to="{path:'/profile', query:{userId:user.id, username:user.username}}" v-if="isAuthenticated" class="flex items-center space-x-1">-->
<!--                        <v-icon-->
<!--                            class=""-->
<!--                            color="black"-->
<!--                            dark-->
<!--                            size="large"-->
<!--                        >-->
<!--                            mdi-account-->
<!--                        </v-icon>-->
<!--                        <span class="text-gray-600 font-bold">{{user.username}}</span>-->
<!--                        <div>-->
<!--                            <button class="bg-slateGray text-white rounded-md px-2 py-1 text-xs font-bold" @click="logout">Sign out</button>-->
<!--                        </div>-->
<!--                    </router-link>-->
<!--                    <div v-else>-->
<!--                        <router-link to="/login"><button class="bg-white rounded-md px-1">Log in</button></router-link>-->
<!--                    </div>-->
                </div>

            </div>

        </div>
        <!-- Navigation Drawer -->
<!--        <v-navigation-drawer v-model="drawer" app temporary>-->
<!--            <v-list>-->
<!--                <v-list-item v-for="item in menuItems" :key="item.title" :to="item.to" link>-->
<!--                        <v-icon>{{ item.icon }}</v-icon>-->

<!--                        <v-list-item-title>{{ item.title }}</v-list-item-title>-->

<!--                </v-list-item>-->
<!--            </v-list>-->
<!--        </v-navigation-drawer>-->
    </nav>
</template>

<script setup>
import {useStore} from "vuex"
import {ref} from 'vue';
import {computed} from 'vue';
import {useRouter} from 'vue-router';
const imageSrc = '/logo.png';
const store = useStore();
const router = useRouter();
const drawer = ref(false); // Vue 3 composition API syntax
const isAuthenticated = computed(()=> store.getters.isAuthenticated);
const user = computed(() => {
    if(isAuthenticated.value){
        return store.getters.getUser;
    }
    return null;
})

const menuItems = [
    { title: 'Goals', icon: 'mdi-target', to: '/goals',type:'link', },
    { title: 'Incomes', icon: 'mdi-cash-multiple', to: '/incomes', type:'link', },
    { title: 'Expenses', icon: 'mdi-currency-usd', to: '/expenses',type:'link', },
    { title: 'Schedules', icon: 'mdi-calendar-clock', to: '/schedules',type:'link', },
    { title: 'Settings', icon: 'mdi-cog', to: '/settings',type:'link', },
    {title:'Profile', icon:'mdi-account', to:'/profile',type:'link',},
    { title: 'Sign out', icon: 'mdi-logout', type: 'button' }
];

const dropItems = [
    { title: 'Settings', icon: 'mdi-cog', to: '/settings', type: 'link' },
    { title: 'Profile', icon: 'mdi-account', to: '/profile', type: 'link' },
    { title: 'Sign out', icon: 'mdi-logout', type: 'button' }
];
function toggleDrawer() {
    drawer.value = !drawer.value;
}

async function logout(){
    try{
        const response = await axios.post('api/logout', user.value)
        console.log(response);
        await store.dispatch('logoutAction');
    }catch(error){
        console.error(error);
    }
    router.push('/login');
}

</script>
