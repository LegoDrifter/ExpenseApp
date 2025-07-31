<template>

    <div v-if="isLoading === 'loading'" class="flex justify-center my-40" >
        <v-progress-circular indeterminate :size="120" color="blue"></v-progress-circular>
    </div>

    <div v-else class="max-w-2xl md:max-w-6xl lg:max-w-[2000px] mx-auto mt-6">
        <div class="flex flex-col md:flex-row  max-w-xs  md:max-w-lg mx-auto">
            <v-select
                label="Select a year"
                :items="['2024','2023']"
                class="mt-3"
                v-model="inputYear"
            ></v-select>
            <v-btn :disabled="!inputYear" variant="tonal" class="md:mt-5 md:ml-2 mb-3" @click="sendYear">
                Submit
            </v-btn>
        </div>
        <div v-if="dataIsSent" class="flex justify-center mb-4">
            <v-btn color="#3B5555" @click="toggleModal"><span class="font-oswald">Show Advanced Stats</span></v-btn>
        </div>
        <TheModal :show="modalStats" @close="toggleModal" class="z-20">

            <div class="max-h-[700px] md:max-h-[800px] overflow-y-auto">
                <h1  class="text-center font-bold mt-6">Percentage Categories Spending Projection and Real Categories Spending {{inputYear}}</h1>

                <div class="flex flex-col md:flex-row justify-center">

                    <div  class="mb-5 ">
                        <Pie :data="dataPie1" :options="options" />
                    </div>
                    <div  class="mb-5">
                        <Pie :data="dataPie2" :options="options" />
                    </div>
                </div>
                <div class="max-w-[300px] md:max-w-5xl lg:max-w-[1900px] overflow-x-auto mx-auto mt-4 ">
                    <div  class="mb-5">
                        <Line  :data="dataLine" :options="options" />
                    </div>
                    <h1  class="text-center font-bold">Generated income per month {{inputYear}}</h1>

                    <div  class="mb-5">
                        <Bar :data="dataBar1" :options="options" />
                    </div>
                    <h1  class="text-center font-bold">Generated expense per month {{inputYear}}</h1>

                    <div class="mb-5">
                        <Bar :data="dataBar2" :options="options" />
                    </div>
                </div>
            </div>

        </TheModal>
        <h1 v-if="dataIsSent" class="text-center mb-3 font-bold font-italic">Balances at the end of each month:</h1>
        <div v-if="dataIsSent" class="flex justify-center gap-1 mb-6 text-white font-oswald font-bold">
<!--            <button :class="{'bg-blue-500 px-2 py-1 text-white':activeBalances==='scheduled','bg-gray-500 px-2 py-1 text-white':activeBalances==='realized'}" @click="toggleBalances('scheduled')">Scheduled</button>-->
<!--            <button :class="{'bg-blue-500 px-2 py-1 text-white':activeBalances==='realized','bg-gray-500 px-2 py-1 text-white':activeBalances==='scheduled'}" @click="toggleBalances('realized')">Realized</button>-->
            <p class="bg-slateGray px-2 py-1 rounded-md">S:Scheduled</p>
            <p class="bg-slateGray px-2 py-1 rounded-md">R:Realized</p>
        </div>
        <div class="mx-10 grid  grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4" v-if="dataIsSent">
<!--            <Month v-for="(month, index) in 12" :key="index" :month="index + 1"></Month>-->
            <Month v-for="(month, index) in dataArray" :key="index" :numberMonth="index" :month="month" :year="inputYear" :type="activeBalances"></Month>
        </div>
    </div>

</template>


<script setup>
import {useStore} from "vuex";
import {ref, defineComponent, onMounted, computed} from "vue";
import Month from "../Components/Month.vue"
const inputYear = ref('');
const dataIsSent = ref(false);
const dataArray = ref(null);
const store = useStore();
const modalStats = ref(false);
const labels = ref(null);
const balanceValues = ref(null);
const incomeValues = ref(null);
const expenseValues = ref(null);
const isLoading = ref(null);
const activeBalances = ref('scheduled');
const settingsObject = ref({
    wants:null,
    needs:null,
    savingsInvest:null,
});
const wnsObject = ref({
    totalWantsYear:"",
    totalNeedsYear:"",
    totalSavingsYear:"",
})
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    BarElement,

} from 'chart.js'
import { Line } from 'vue-chartjs'
import { Pie } from 'vue-chartjs'
import { Bar } from 'vue-chartjs'
import TheModal from "@/Components/TheModal.vue";
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, ArcElement, Legend,BarElement);


function getTestValue(){
    store.dispatch('printAction')
}

onMounted(getTestValue);

async function getCategoriesData(){
    try{
        const response = await axios.get('api/categories')
        console.log(response);
        settingsObject.value = {
            wants:response.data[0].percentage,
            needs:response.data[1].percentage,
            savingsInvest:response.data[2].percentage,
        }
    }catch(error){
        console.error(error);
    }
    console.log(settingsObject.value);
}

async function pieData(){
    try{
       const response =  await axios.post(`/api/balances/dashboard/pie/${inputYear.value}`);
        wnsObject.value.totalWantsYear = response.data.totalWants;
        wnsObject.value.totalNeedsYear = response.data.totalNeeds;
        wnsObject.value.totalSavingsYear = response.data.totalSavings;
        console.log(wnsObject.value);
    }catch(error){
        console.error(error);
    }
}

async function sendYear(){
    isLoading.value = 'loading';

    console.log(inputYear.value);
    getCategoriesData();

    try{
        const response = await axios.post(`/api/balances/dashboard/${inputYear.value}`);
        console.log(response);
        dataIsSent.value = true;
        isLoading.value = 'loaded';
        dataArray.value = response.data.data;
        setStats();
    }catch(error){
        console.error(error);
    }

}

function toggleModal(){
    modalStats.value = !modalStats.value;
}

function toggleBalances(arg){
    activeBalances.value = arg;
}


function setStats(){
    pieData();
    console.log(dataArray.value);
    const dataArr = Object.values(dataArray.value);
    console.log(dataArr)
     labels.value = dataArr.map(item => item.monthName);
    balanceValues.value = dataArr.map(item => item.totalBalance);
    incomeValues.value = dataArr.map(item => item.totalIncome);
    expenseValues.value = dataArr.map(item =>item.totalExpense);
    // wnsObject.value.totalWantsYear = dataArr.reduce((accumulator,currentValue) => {
    //     return accumulator + currentValue.totalWants;
    // },0 )
    // wnsObject.value.totalNeedsYear = dataArr.reduce((accumulator, currentValue) =>{
    //     return accumulator + currentValue.totalNeeds;
    // },0)
    // wnsObject.value.totalSavingsYear = dataArr.reduce((accumulator, currentValue) =>{
    //     return accumulator + currentValue.totalSavings;
    // },0)


}

const dataLine = computed(() => {
    return {
        labels:labels.value,
        datasets:
    [
        {
            label: "Monthly Balance",
            backgroundColor: "#2d3748",
            data: balanceValues.value,
        }
    ]
};
})

const dataPie1 = computed(() => {
    return {
        labels:['Wants','Needs','Investment and Savings'],
        datasets:[
            {
                label:'Projected Categories',
                backgroundColor: ['#41B883', '#E46651', '#00D8FF'],
                data: [
                    settingsObject.value.wants,
                    settingsObject.value.needs,
                    settingsObject.value.savingsInvest,
                ]
            }
        ]
    }
})

const dataPie2 = computed(() => {
    return {
        labels:['Wants','Needs','Investment and Savings'],
        datasets:[
            {
                label:'Projected Categories',
                backgroundColor: ['#41B883', '#E46651', '#00D8FF'],
                data: [
                   wnsObject.value.totalWantsYear,
                   wnsObject.value.totalNeedsYear,
                    wnsObject.value.totalSavingsYear,
                ]
            }
        ]
    }
})

const dataBar1 = computed(() => {
    return {
        labels:['Income'],
        datasets:
            [
                {
                    label: "Monthly Income",
                    backgroundColor: "#43B287",
                    data: incomeValues.value,
                }
            ]
    }
})

const dataBar2 = computed(() => {
    return {
        labels:labels.value,
        datasets:
            [
                {
                    label: "Monthly Expense",
                    backgroundColor: "#f87979",
                    data: expenseValues.value,
                }
            ]
    }
})

const options = {
    responsive:true,
    maintainAspectRatio:false
}


</script>


<style scoped>

</style>
