<template>

<form @submit.prevent="sendRegister" class="max-w-xs md:max-w-sm  mx-auto bg-stone-100 shadow-lg mt-10 p-5 rounded-md">
    <div class="text-center mt-3"><h1 class="font-oswald font-bold text-3xl uppercase text-slateGray mt-3 mb-6">Register</h1></div>
    <div class="relative flex flex-col gap-1">
<!--        <label class="text-white uppercase">Email</label>-->
        <div class="absolute left-2 top-1 "><v-icon
            class=""
            color="slateGray"
            dark
            size="small"
        >
            mdi-email
        </v-icon></div>
        <input placeholder="enter email..." type="email" v-model="email" name="email" class="bg-white text-small text-center p-1 mb-5 rounded-full pl-2 border border-slateGray">
    </div>
    <div class="relative flex flex-col gap-1">
<!--        <label class="text-white uppercase">Username</label>-->
        <div class="absolute left-2 top-1 "><v-icon
            class=""
            color="slateGray"
            dark
            size="small"
        >
            mdi-clipboard-account
        </v-icon></div>
        <input placeholder="enter username..." type="text" v-model="username" name="username" class="bg-white text-small text-center p-1 mb-5 rounded-full pl-2 border border-slateGray">
    </div>
    <div class="relative flex flex-col gap-1">
<!--        <label class="text-white uppercase">Password</label>-->
        <div class="absolute left-2 top-1 "><v-icon
            class=""
            color="slateGray"
            dark
            size="small"
        >
            mdi-lock
        </v-icon></div>
        <input placeholder="enter password..." type="password" v-model="password" name="password" class="bg-white text-small text-center p-1 mb-5 rounded-full pl-2 border border-slateGray">
    </div>
    <div v-if="errors" v-for="error in errors" class="mt-1 text-xs text-red-600 text-center font-bold">{{error}}</div>

    <div class="flex justify-between mt-6">
        <div class="text-slateGray font-oswald">Already an user? then <router-link class="font-bold" to="/login">Login</router-link></div>
        <button type="submit" class="bg-slateGray py-1 px-2 rounded-md hover:bg-black text-white font-oswald">Register</button>
    </div>
</form>

</template>


<script setup>
import {useRouter} from 'vue-router';
import {useStore} from 'vuex';
import {ref} from 'vue';
const email = ref("");
const password = ref(null);
const username = ref("");
const router = useRouter();
const store = useStore();
const errors = ref(null);

async function sendRegister(){
    try{
        const response = await axios.post('/api/register',{
            email:email.value,
            password:password.value,
            username:username.value
        });
        console.log(response);
        const {user,token} = response.data.data;
        store.dispatch("setCredentials",{
            token:token,
            user:user,
        })

        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        router.push('/');
    }catch(error){
        console.error(error);
        errors.value = Object.values(error.response.data.errors).flat();
    }
}

</script>

<style scoped>
.slide-enter-active, .slide-leave-active {
    transition: transform 0.5s ease;
}
.slide-enter, .slide-leave-to /* .slide-leave-active in <2.1.8 */ {
    transform: translateX(100%);
}

</style>
