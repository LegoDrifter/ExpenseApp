<template>
    <div>
        <h1>HOME</h1>
        <router-link to="/test"> Take me to Test page </router-link>
        <button @click.prevent="getValue" class="bg-indigo-500">Trigger Endpoint</button>
        <p v-if="responseData">{{ responseData}}</p>
        <p v-if="errors">{{ errors }}</p>

    </div>
</template>

<script>

import axios from "axios";
import { ref } from "vue";
export default{

    setup(){

        const userID = 3;
        const response = ref();
        const errors = ref();
        const responseData = ref();

        const getValue = async () => {
            try {
                response.value = await axios.get("api/users/" + userID);
                responseData.value = response.value.data;
                console.log(responseData.value)
            } catch (error) {
                // Do something with the error
                errors.value = error.response.data.errors;
                console.log(error);
            }
        };

        return{
            getValue,
            response,
            errors,
            responseData
        }
    }
}
</script>
