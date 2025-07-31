<template>
<!--    <h1 v-if="isLoading === 'loaded'" class="text-center mt-3 text-2xl font-bold ">[Goals]</h1>-->

    <div v-if="isLoading === 'loading'" class="flex justify-center my-40" >
        <v-progress-circular indeterminate :size="120" color="slateGray"></v-progress-circular>
    </div>
    <div v-else-if="isLoading === 'loaded'">
        <v-toolbar flat>
            <v-toolbar-title>
                <v-toolbar-title><span class="font-oswald text-slateGray font-bold text-2xl">Goals</span></v-toolbar-title>

            </v-toolbar-title>
            <button @click="toggleModal('create')" class="bg-white px-1 py-1 rounded-md font-bold mr-5">+ Add </button>


        </v-toolbar>
    </div>
        <div v-if="isLoading === 'loaded'" class="my-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <GoalCard v-for="goal in combinedGoalList" :key="key" :goal="goal" class="mb-5" @getID="getID" :dashboard="false"></GoalCard>
        </div>
<!--    <div v-if="isLoading === 'loaded'" class="flex justify-center">-->
<!--        <button @click="toggleModal('create')" class="bg-blue-600 px-2 py-2 rounded-md mb-5 text-white">Create</button>-->
<!--    </div>-->
    <TheModal :show="modal === 'create'" @close="toggleModal('close')" class="max-w-sm mx-auto">
        <form @submit.prevent="sendData('CREATE')" class="max-w-xs md:max-w-sm mx-auto bg-stone-100 shadow-lg mt-0 p-5 rounded-md">
            <div class="text-center mt-3">
                <h1 class="font-oswald font-bold text-3xl uppercase text-slateGray mt-3 mb-6">Add New Goal</h1>
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Title" type="text" v-model="goalData.title" name="title" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Description" type="text" v-model="goalData.description" name="description" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input type="date" v-model="goalData.start_date" name="start_date" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Price" type="number" v-model="goalData.price" name="price" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Initial Budget" type="number" v-model="goalData.initial_budget" name="initial_budget" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <select name="status" v-model="goalData.status" class="block w-full px-4 py-2 rounded-full border border-gray-300 bg-white text-gray-800 shadow-sm focus:outline-none focus:border-blue-400">
                    <option value="1">Planned</option>
                    <option value="3">In Progress</option>
                </select>
            </div>
            <button type="submit" class="bg-slateGray py-1 px-2 rounded-md hover:bg-black text-white font-oswald">Create</button>
        </form>
    </TheModal>

    <!-- Edit Modal -->
    <TheModal :show="modal === 'edit'" @close="toggleModal('close')" class="max-w-sm mx-auto">
        <form @submit.prevent="sendData('UPDATE')" class="max-w-xs md:max-w-sm mx-auto bg-stone-100 shadow-lg mt-10 p-5 rounded-md">
            <div class="text-center mt-3">
                <h1 class="font-oswald font-bold text-3xl uppercase text-slateGray mt-3 mb-6">Edit Goal</h1>
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Title" type="text" v-model="fetchedGoal.title" name="title" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Description" type="text" v-model="fetchedGoal.description" name="description" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input type="date" v-model="fetchedGoal.start_date" name="start_date" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Price" type="number" v-model="fetchedGoal.price" name="price" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <input placeholder="Initial Budget" type="number" v-model="fetchedGoal.initial_budget" name="initial_budget" class="bg-white text-small text-center p-1 rounded-full pl-2 border border-slateGray">
            </div>
            <div class="relative flex flex-col gap-1 mb-5">
                <select @change="updateFetchedGoalStatus" name="status" v-model="fetchedGoal.status" class="block w-full px-4 py-2 rounded-full border border-gray-300 bg-white text-gray-800 shadow-sm focus:outline-none focus:border-blue-400">
                    <option value="1">Planned</option>
                    <option value="2">Completed</option>
                    <option value="3">In Progress</option>
                </select>
            </div>
            <button type="submit" class="bg-slateGray py-1 px-2 rounded-md hover:bg-black text-white font-oswald">Edit</button>
        </form>
    </TheModal>
</template>


<script setup>
import TheModal from "@/Components/TheModal.vue";
import {ref, onBeforeMount} from 'vue';
import GoalCard from "@/Components/GoalCard.vue";
import Month from "@/Components/Month.vue";
const goalsList = ref();
const combinedGoalList = ref();
const isLoading = ref('loading');
const modal = ref('close');
const itemID = ref(0);
const initialKey = ref("initial");
const fetchedGoal = ref();
const key = ref(null);


const goalData = ref({
    title:'',
    description:'',
    start_date:'',
    end_date:'',
    price:null,
    initial_budget:null,
    status:null,
});


async function getData(){
    isLoading.value = 'loading';
    console.log(isLoading.value);
    try{
        const response = await axios.get('api/goals');
        // console.log(response);
        console.log("Goals fetched.", response.data);
        goalsList.value = response.data.data;
        combinedGoalList.value = goalsList.value;
        isLoading.value = 'loaded';
        console.log(isLoading.value);

    }catch(error){
        error.value = error;
        console.error("Error fetching goals", error)
        isLoading.value = 'error';
    }
}
const updateFetchedGoalStatus = (event) => {
    fetchedGoal.value.status = parseInt(event.target.value, 10);
}

const toggleModal = (arg) =>{
    modal.value = arg;
    console.log(arg);
}

const getID = (arg, action) =>{
    console.log(arg);
    console.log(action);
    itemID.value = arg;
    if(action === 'EDIT'){
        getItem(arg);
        toggleModal('edit');
    }else if(action === 'DELETE'){
        const newArray = goalsList.value.filter(goal => goal.id !== arg);
        axios.delete(`api/goals/${arg}/delete`).then(response =>{
            console.log('Data deleted successfully',response);
            goalsList.value = newArray;
            initialKey.value = Math.random();
        }).catch(error =>{
            console.error('Error deleting data:',error);
        })
    }
}

async function getItem(id){
    try{
        const response = await axios.get(`api/goals/${id}`)
        fetchedGoal.value = response.data.data;
        console.log("Goal found", fetchedGoal.value);
    }catch(error){
        console.log("Error goal not found", error);
    }
}

async function sendData(identifier){
    if(identifier === 'UPDATE'){
        try{
            const response = await axios.put(`api/goals/${itemID.value}/update`, fetchedGoal.value);
            console.log("Goal was updated", response.data.data);
            const itemIndex = goalsList.value.findIndex((item) => {
                return item.id === response.data.data.id;
            })
            goalsList.value[itemIndex] = {...response.data.data};
            combinedGoalList.value[itemIndex] = {...response.data.data};
            console.log(combinedGoalList.value[itemIndex]);
            key.value = Math.random().toString();
            toggleModal('close');
        }catch(error){
            console.error("Error updating goal", error);
        }
    }
    if(identifier === 'CREATE') {
        console.log(goalData.value);
        goalData.value.status = Number(goalData.value.status);
        try {
            const response = await axios.post('api/goals/create', goalData.value);
            console.log("Goal created successfully", response.data);
            combinedGoalList.value = [...combinedGoalList.value, response.data.data];
            key.value = Math.random().toString();
            toggleModal('close');

        } catch (error) {
            console.error('Error creating goal', error);
        }
    }
}

onBeforeMount(getData);
</script>

<style scoped>

</style>
