<template>
    <nav class="bg-gray-800 p-4" :key="isAuthenticated">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <router-link to="/" class="text-white text-lg font-bold">ExpenseTracker</router-link>
                <div v-if="isAuthenticated" class="ml-6 space-x-4">
                    <router-link to="/goals" class="text-gray-300 hover:text-white">Goals</router-link>
<!--                    <router-link to="/balances" class="text-gray-300 hover:text-white">Balance</router-link>-->
                    <router-link to="/incomes" class="text-gray-300 hover:text-white">Incomes</router-link>
                    <router-link to="/expenses" class="text-gray-300 hover:text-white">Expenses</router-link>
                    <router-link to="/schedules" class="text-gray-300 hover:text-white">Schedules</router-link>
                </div>
            </div>
            <div v-if="isAuthenticated" class="flex items-center space-x-3">
                <img src="https://via.placeholder.com/30" alt="User Icon" class="rounded-full w-8 h-8">
                <span class="text-gray-300">{{user.username}}</span>
                <div>
                    <button class="bg-white rounded-md px-1" @click="logout">Log out</button>
                </div>
            </div>
            <div v-else>
                <router-link to="/login"><button class="bg-white rounded-md px-1">Log in</button></router-link>
            </div>
        </div>
    </nav>
</template>

<script setup>
import {useStore} from "vuex";
import {computed} from 'vue';

const store = useStore();
const isAuthenticated = computed(()=> store.getters.isAuthenticated);
const user = computed(() => {
    if(isAuthenticated.value){
        return store.getters.getUser;
    }
    return null;
})

async function logout(){
    try{
        const response = await axios.post('api/logout', user.value)
        console.log(response);
        await store.dispatch('logoutAction');
    }catch(error){
        console.error(error);
    }
}

</script>
