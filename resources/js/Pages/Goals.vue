<template>
    <div>
        <h1 class="text-3xl font-bold text-center my-5">[Goals]</h1>
        <div v-if="isLoading === 'loading'" class="flex justify-center my-10" >
            <v-progress-circular indeterminate :size="100"></v-progress-circular>
        </div>

        <Table v-else-if="isLoading === 'loaded'" :headers="tableHeaders" :fields="tableFields" :items="goals" @getID="getID" :key="initialKey"/>

        <!-- Inline Form -->
        <modal :show="modal === 'create'" @close="toggleModal('close')">
            <div class="flex flex-col bg-stone-200 p-3 space-y-2 rounded-md">
                <h1 class="text-2xl font-bold">Add New Goal</h1>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Title: </label>
                    <input type="text" class="bg-white" name="title" v-model="goalData.title">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Description: </label>
                    <input type="text" class="bg-white" name="description" v-model="goalData.description">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Start Date: </label>
                    <input type="date" class="pl-2 bg-white" name="start_date" v-model="goalData.start_date">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Price: </label>
                    <input type="number" class="bg-white" name="price" v-model="goalData.price">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Initial Budget: </label>
                    <input type="number" class="bg-white" name="initial_budget" v-model="goalData.initial_budget">
                </div>
                <div class="flex flex-col">
                    <label class="font-bold">Status: </label>
                    <select name="status" type="number" v-model="goalData.status" class="block w-full px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-800 shadow-sm focus:outline-none focus:border-blue-400">
                        <option value="2">Planned</option>
                        <option value="1">Completed</option>
                        <option value="3">In Progress</option>
                    </select>
                </div>
                <button @click="sendData('CREATE')" type="submit" class="px-2 py-1 font-bold">Create</button>
            </div>
        </modal>
        <modal :show="modal === 'edit'"  @close="toggleModal('close')">
            <div class="flex flex-col bg-stone-200 p-3 space-y-2 rounded-md">
                <h1 class="text-2xl font-bold">Edit Goal</h1>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Title: </label>
                    <input type="text" class="bg-white" name="title" v-model="fetchedGoal.title" value="fetchedGoal.title">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Description: </label>
                    <input type="text" class="bg-white" name="description" v-model="fetchedGoal.description" value="fetchedGoal.description">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Start Date: </label>
                    <input type="date" class="pl-2 bg-white" name="start_date" v-model="fetchedGoal.start_date" value="fetchedGoal.start_date">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Price: </label>
                    <input type="number" name="price" class="bg-white" v-model="fetchedGoal.price" value="fetchedGoal.price">
                </div>
                <div class="flex flex-col">
                    <label class="mb-2 font-bold">Initial Budget: </label>
                    <input type="number" class="bg-white" name="initial_budget" v-model="fetchedGoal.initial_budget" value="fetchedGoal.initial_budget">
                </div>
                <div class="flex flex-col">
                    <label class="font-bold">Status: </label>
                    <select name="status"  v-model="fetchedGoal.status"  class="block w-full px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-800 shadow-sm focus:outline-none focus:border-blue-400">
                        <option value="0">Planned</option>
                        <option value="2">Completed</option>
                        <option value="1">In Progress</option>
                    </select>
                </div>
                <button @click="sendData('UPDATE')" type="submit" class="px-2 py-1 font-bold">Edit</button>
            </div>
        </modal>
        <div class="flex gap-4 justify-center my-5">
            <button @click="toggleModal('create')" class="btn-add bg-indigo-600 text-white px-2 py-1 rounded-md">Add Goal</button>
        </div>



    </div>
</template>

<script>
import Table from "../Components/TheTable.vue"; // Adjust the import path as needed
import Modal from "../Components/TheModal.vue"
import {ref, onBeforeMount} from 'vue';
export default {
    components: {
        Table,
        Modal
    },
    setup(){
        const tableHeaders = ["Title", "Description", "Start Date", "Price", "Initial Budget", "Status", "Action"];
        const tableFields = ["title", "description", "start_date", "price", "initial_budget", "status"];
        const goalsList = ref();
        const modal = ref('close');
        const fetchedGoal = ref();
        const itemID = ref(0);
        const goalData = ref({
            title:'',
            description:'',
            start_date:'',
            price:null,
            initial_budget:null,
            status:null,
        });
        const isLoading = ref('loading');
        const error = ref(null);
        const initialKey = ref("initial");
        const newGoal = {
            user_id:1,
            category_id:null,
            wage:null,
            recurring:null,
            description:"",
            date:null,
            cycle:null,
            status:null,
            type:null,
        }

        const toggleModal = (arg) =>{
            modal.value = arg;
        }

        const getID = (arg, action) =>{
            console.log(arg);
            console.log(action);
            itemID.value = arg;
            if(action === 'EDIT'){
                getItem(arg);
                modal.value = 'edit';
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


        async function sendData(identifier){
            if(identifier === 'UPDATE'){
                try{
                    const response = await axios.put(`api/goals/${itemID.value}/update`, fetchedGoal.value);
                    console.log("Goal was updated", response.data);
                }catch(error){
                    console.error("Error updating goal", error);
                }
            }
            if(identifier === 'CREATE') {
                console.log(goalData.value);
                try {
                    const response = await axios.post('api/goals/create', goalData.value);
                    console.log("Goal created successfully", response.data);
                } catch (error) {
                    console.error('Error creating goal', error);
                }
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

        async function getData(){
            isLoading.value = 'loading';
            console.log(isLoading.value);
            try{
                const response = await axios.get('api/goals');
                console.log("Goals fetched.", response.data.data);
                goalsList.value = response.data.data;
                isLoading.value = 'loaded';
                console.log(isLoading.value);

            }catch(error){
                error.value = error;
                console.error("Error fetching goals", error)
                isLoading.value = 'error';
            }
        }

        onBeforeMount(getData);

        return{
            tableHeaders,
            tableFields,
            goals:goalsList,
            modal,
            toggleModal,
            goalData,
            sendData,
            getData,
            isLoading,
            getID,
            fetchedGoal,
            initialKey
        }
    }

};
</script>
