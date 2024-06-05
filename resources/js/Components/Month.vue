
<template>

<div  class="max-w-lg bg-gray-800 mb-5 p-3 rounded-md text-white flex flex-col">
    <span class="font-bold">{{month}}</span>
    <span>Total Income:</span>
    <span>Total Expense:</span>
    <span>Total Goal Cost:</span>
    <span>Final Balance:</span>
</div>

</template>

<script setup>
import {ref, defineProps, onMounted} from 'vue';
import axios from "axios";

const props = defineProps({
    month: Object
});

const isLoaded = ref(null);
// const expenseData = {
//     totalIncome:
// }

function calculateIncome(array){

}

// async function getMonthData(){
//     const month = props.month < 10 ? '0' + props.month : props.month
//     try{
//         const response = await axios.get(`/api/balances/dashboard/stats/${month}`)
//         // console.log(response);
//         expenseData.value = response.data.data;
//         console.log(expenseData.value)
//
//     }catch(error){
//         console.error(error);
//     }
//     isLoaded.value = true;
// }



</script>


<style scoped>

</style>
