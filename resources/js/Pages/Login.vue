<template>
    <div class="text-center mt-12"><h1 class="text-3xl uppercase">Login</h1></div>

    <form method="POST" @submit.prevent="sendLogin" class="max-w-xl  mx-auto bg-gray-800 mt-10 p-5 rounded-md">
        <div class="flex flex-col gap-1">
            <label class="text-white uppercase">Email</label>
            <input placeholder="Enter email..." v-model="email" type="email" name="email" class="bg-white p-1 mb-2 rounded-sm pl-2">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-white uppercase">Password</label>
            <input placeholder="Enter password..." v-model="password" type="password" name="password" class="bg-white p-1 mb-2 rounded-sm pl-2">
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-white py-1 px-2 rounded-md  text-gray-800">Login</button>
        </div>
    </form>
</template>

<script setup>
import {ref} from 'vue';
const email = ref("");
const password = ref(null);
// function test(){
//     console.log(email.value);
//     console.log(password.value);
// }
async function sendLogin(){
    try{
        const response = await axios.post('/api/login',{
            email:email.value,
            password:password.value
        });
        const {user,token} = response.data.data;
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('token',token);

        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }catch(error){
        console.error(error);
    }
}


</script>

<style scoped>

</style>
