<template>
    <button @click="toggleMonth" class="max-w-lg mb-5 rounded-md overflow-hidden hover:opacity-80">
        <!-- Background image div -->
        <div
            class="h-56 relative"
            :style="{
        backgroundImage: `url(/ArtBreeder/${month.monthName}.jpeg)`,
        backgroundSize: '100% 100%', /* Adjusts background size to cover the entire container */
        backgroundPosition: 'center' /* Centers the background image */
      }"
        >
            <span class="absolute top-4 left-4 font-bold text-lg text-white">{{ month.monthName }}</span>
            <div class="flex gap-2 absolute bottom-4 left-4">
                <span class="bg-gray-800 bg-opacity-75 px-2 py-1 rounded text-white">S:{{month.totalBalance}}</span>
                <span class="bg-gray-800 bg-opacity-75 px-2 py-1 rounded text-white">R:{{month.realBalance}}</span>
            </div>
        </div>
        <!-- Additional content if needed -->
    </button>
    <TheModal :show="clickedMonth" @close="toggleMonth" class="z-20 shadow-xl ">
        <div class="p-4">
            <p class="text-lg font-semibold mb-4">Incomes this month:</p>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Date</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Category</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Amount</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="income in incomes" :key="income.id" class="bg-white border-b border-gray-200">
                        <td class="py-2 px-4 text-sm text-gray-600">{{ income.date }}</td>
                        <td class="py-2 px-4 text-sm text-gray-600">{{ income.category.title }}</td>
                        <td class="py-2 px-4 text-sm text-gray-600">{{ income.wage }}</td>
                        <td class="py-2 px-4 text-sm text-gray-600">{{ income.description }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
            <div class="p-4">
                <p class="text-lg font-semibold mb-4">Expenses this month:</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Date</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Category</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Amount</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm text-gray-600">Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="expense in expenses" :key="expense.id" class="bg-white border-b border-gray-200">
                            <td class="py-2 px-4 text-sm text-gray-600">{{ expense.date }}</td>
                            <td class="py-2 px-4 text-sm text-gray-600">{{ expense.category.title }}</td>
                            <td class="py-2 px-4 text-sm text-gray-600">{{ expense.wage }}</td>
                            <td class="py-2 px-4 text-sm text-gray-600">{{ expense.description }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </TheModal>
</template>

<script setup>
import {ref, defineProps, onMounted,computed} from 'vue';
import axios from "axios";
import TheModal from "@/Components/TheModal.vue";

const props = defineProps({
    month: Object,
    year:String,
    type:String,
    numberMonth:Number,
    key:Number,
});
const clickedMonth = ref(false);
const incomes = ref(null);
const expenses = ref(null);
const isLoading = ref(null);
const computedValue = computed(() => {
    return props.type === 'scheduled' ? props.month.totalBalance : props.month.realBalance;
});
function toggleMonth(){
    clickedMonth.value = !clickedMonth.value;
    // console.log(props.numberMonth);
    // console.log(props.year);
    getMonthlyData();
}

async function getMonthlyData(){
    try{
        const response = await axios.post('/api/balances/monthlyBalances',{
                year:props.year,
                month:props.numberMonth,
            })
        incomes.value = response.data.data.incomes;
        expenses.value = response.data.data.expenses;

    }catch(error){
        console.error(error);
    }
}


</script>


<style scoped>

</style>
