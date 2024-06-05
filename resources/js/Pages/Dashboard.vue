<template>

    <div class="max-w-4xl mx-auto mt-6">
        <div class="flex gap-2 max-w-lg mx-auto">
            <v-select
                label="Select a year"
                :items="['2024']"
                class="mt-3"
                v-model="inputYear"
            ></v-select>
            <v-btn variant="tonal" class="mt-5" @click="sendYear">
                Submit
            </v-btn>
        </div>
        <div class="mx-10 grid grid-cols-3 gap-4" v-if="dataIsSent">
<!--            <Month v-for="(month, index) in 12" :key="index" :month="index + 1"></Month>-->
            <Month v-for="(month, index) in dataArray" :key="index" :month="month"></Month>
        </div>
    </div>

</template>


<script setup>
import {useStore} from "vuex";
import {ref, defineComponent, onMounted} from "vue";
import Month from "../Components/Month.vue"
const inputYear = ref('');
const dataIsSent = ref(false);
const dataArray = ref(null);
const store = useStore();

function getTestValue(){
    store.dispatch('printAction')
}

onMounted(getTestValue);
async function sendYear(){
    console.log(inputYear.value);

    try{
        const response = await axios.post(`/api/balances/dashboard/${inputYear.value}`);
        console.log(response);
        dataIsSent.value = true;
        dataArray.value = response.data.data;
        console.log("Data array is", typeof dataArray.value);
    }catch(error){
        console.error(error);
    }

}


</script>


<style scoped>

</style>
