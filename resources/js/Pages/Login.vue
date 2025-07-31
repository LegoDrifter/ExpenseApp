<template>
<!--    <div class="text-center mt-12"><h1 class="text-3xl uppercase">Login</h1></div>-->

    <form method="POST" @submit.prevent="sendLogin" class="max-w-xs md:max-w-sm h-[280px]  mx-auto bg-stone-100 shadow-lg mt-10 p-5 rounded-md">
        <div class="text-center"><h1 class="font-oswald font-bold text-3xl uppercase text-slateGray mt-3 mb-6">Login</h1></div>
        <div class="relative flex flex-col gap-1 mb-3">
<!--            <label class="text-slateGray font-oswald uppercase">Email</label>-->
            <div class="absolute left-2 top-1 "><v-icon
                class=""
                color="slateGray"
                dark
                size="small"
            >
                mdi-account
            </v-icon></div>
            <input placeholder="enter email..." v-model="email" type="email" name="email" class="bg-white text-small text-center p-1 mb-2 rounded-full pl-2 border border-slateGray">
        </div>
        <div class="relative flex flex-col gap-1">
<!--            <label class="text-slateGray uppercase font-oswald">Password</label>-->
            <div class="absolute left-2 top-1 "><v-icon
                class=""
                color="slateGray"
                dark
                size="small"
            >
                mdi-lock
            </v-icon></div>
            <input placeholder="enter password..." v-model="password" type="password" name="password" class="bg-white text-center p-1 mb-2 rounded-full pl-2 border border-slateGray">
        </div>
        <div v-if="errors" class="mt-1 text-xs text-red-600 text-center font-bold">{{errors}}</div>
        <div class="flex justify-between mt-5">
            <div class="text-slateGray font-oswald">Not an user? then <router-link class="font-bold" to="/register">Register</router-link></div>
            <button type="submit" class="bg-slateGray py-1 px-2 rounded-md hover:bg-slateLight text-white font-oswald">Login</button>
        </div>

    </form>
</template>

<script setup>
import {useRouter} from 'vue-router';
import {useStore} from "vuex";
import {ref} from 'vue';
const email = ref("");
const password = ref(null);
const router = useRouter();
const store = useStore();
const errors = ref(null);

async function sendLogin(){
    try{
        const response = await axios.post('/api/login',{
            email:email.value,
            password:password.value
        });
        const {user,token} = response.data.data;
        store.dispatch("setCredentials",{
            token:token,
            user:user,
        })

        // axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        router.push('/');
    }catch(error){
        console.error(error);
        errors.value = error.response.data.errors;
    }
}


</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}

</style>
