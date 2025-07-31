<template>
    <div v-if="isLoading === 'loading'" class="flex justify-center my-40" >
        <v-progress-circular indeterminate :size="120" color="slateGray"></v-progress-circular>
    </div>

    <div v-if="isLoading === 'loaded'" class="max-w-3xl mx-auto md:mt-10 bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-slateGray h-32 w-full"></div>
        <div class="relative -mt-16">
            <img src="https://letstryai.com/wp-content/uploads/2023/11/stable-diffusion-avatar-prompt-example-2.jpg" alt="Profile Picture" class="w-32 h-32 rounded-full mx-auto border-4 border-white">
        </div>
        <div class="text-center mt-2">
            <h2 class="text-2xl font-semibold text-gray-800">{{ user.username }}</h2>
        </div>
        <div class="px-6 py-4">
            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 lg:w-1/3 px-4 py-2">
                    <p class="text-gray-700">Email:</p>
                    <p class="font-semibold text-gray-900">{{ user.email }}</p>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/3 px-4 py-2">
                    <p class="text-gray-700">Total Income:</p>
                    <p class="font-semibold text-gray-900">{{ userDB.totalIncome }}</p>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/3 px-4 py-2">
                    <p class="text-gray-700">Total Expense:</p>
                    <p class="font-semibold text-gray-900">{{ acumNeeds + acumSavings + acumWants}}</p>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/3 px-4 py-2">
                    <p class="text-gray-700">Budget:</p>
                    <p class="font-semibold text-gray-900">{{ userBudget }}</p>
                    <v-dialog max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn
                                v-bind="activatorProps"
                                color="surface-variant"
                                text="update"
                                variant="flat"
                                size="small"
                            ></v-btn>
                        </template>

                        <template v-slot:default="{ isActive }">
                            <v-card>
                                <v-text-field
                                    v-model="budget"
                                    label="New Budget"
                                    required
                                ></v-text-field>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn color="slateGray"
                                    @click="updateBudget">
                                       UPDATE
                                    </v-btn>
                                    <v-btn
                                        color="dark"
                                        text="Close Dialog"
                                        @click="isActive.value = false"
                                    ></v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/3 px-4 py-2">
                    <p class="text-gray-700">Balance:</p>
                    <p class="font-semibold text-gray-900">{{ userDB.balance }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gray-100 px-6 py-4">
            <h3 class="text-gray-800 text-lg font-semibold">Expense Categories</h3>
            <ul class="mt-2">
                <li class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Needs</span>
                    <span class="font-semibold text-gray-900">{{ acumNeeds }}</span>
                </li>
                <li class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Wants</span>
                    <span class="font-semibold text-gray-900">{{ acumWants }}</span>
                </li>
                <li class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Savings & Investments</span>
                    <span class="font-semibold text-gray-900">{{ acumSavings }}</span>
                </li>
            </ul>
        </div>
    </div>


</template>

<script setup>
import {defineComponent, computed, onMounted, ref} from 'vue';
import { useRoute } from 'vue-router';
import {useStore} from "vuex"
import TheModal from "@/Components/TheModal.vue";
const route = useRoute();
const today = new Date();
const derivedBudget = ref(null);
const budget = ref(null);
const store = useStore();
const userBudget = ref(null);
const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, '0');
const day = String(today.getDate()).padStart(2, '0');
const formattedDate = `${year}-${month}-${day}`;
const toggleModal = ref(false);
const acumWants = ref(null);
const acumNeeds = ref(null);
const acumSavings = ref(null);
const isLoading = ref(null);
const userDB = ref(null);
const isAuthenticated = computed(()=> store.getters.isAuthenticated);
const user = computed(() => {
    if(isAuthenticated.value){
        return store.getters.getUser;
    }
    return null;
})



function accumulateFunction(array){
    return array.reduce((accumulator,item) => {
        return accumulator + item.wage;
    },0)
}




async function getCategoryData(){
    isLoading.value = 'loading';
     await getUser();
    try{
        const response = await axios.get(`/api/balances/profile/${user.value.id}`);
        console.log(response);
        const wantsArray = Object.values(response.data.wants);
        const needsArray = Object.values(response.data.needs);
        const savingsArray = Object.values(response.data.savings);

        // Calculate accumulations
        acumWants.value = accumulateFunction(wantsArray);
        acumNeeds.value = accumulateFunction(needsArray);
        acumSavings.value = accumulateFunction(savingsArray);
        isLoading.value = 'loaded';

    }catch(error){
        console.error(error);
    }
}

async function updateBudget(){
    try{
        const response = await axios.post('/api/balances/profile/budget',{
            amount:budget.value
        })
        userBudget.value = budget.value;
    }catch(error){
        console.error(error);
    }
}
async function getUser(){
    try{
        const response = await axios.get(`api/users/${user.value.id}`);
        console.log(response.data);
        userBudget.value = response.data.budget;
        userDB.value = response.data;

    }catch(error){
        console.error(error);
    }
}

onMounted(getCategoryData)
</script>


<style scoped>

</style>
