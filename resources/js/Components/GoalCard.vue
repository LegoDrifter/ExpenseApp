
<template>
    <v-card
        class="mx-auto"
        :color=status()
        prepend-icon="$vuetify"
        :subtitle = "goal.description"
        width="300"
    >
        <template v-slot:title>
            <span class="font-weight-black">{{goalValue.title}}</span>
        </template>
        <v-card-text class="">
<!--            <div class="flex gap-2"><p class="font-bold">User ID: </p><span>{{goalValue.user_id}}</span></div>-->
            <div class="flex gap-2">
                <p class="font-bold">Start date: </p><span>{{goalValue.start_date}}</span>

            </div>
            <div class="flex gap-2">
                <p class="font-bold">Price: </p><span>{{goalValue.price}}</span>

            </div>
            <div class="flex gap-2">
                <p class="font-bold">End date: </p><span>{{goalValue.end_date}}</span>
            </div>
            <div v-if="goalValue.status !== 2" class="flex gap-2">
                <p class="font-bold">Status: </p><span>{{statusValue()}}</span>
            </div>
        </v-card-text>
        <div v-if="goalValue.status !== 2" class="mt-2 mb-2 ml-4 flex gap-2 text-white">
            <button v-if="dashboard !== true" class="px-1 py-1 bg-yellow-500 rounded-md" @click="sendID(goal.id, 'EDIT')">Edit</button>
            <button v-if="dashboard !== true" class="px-1 py-1 bg-red-500 rounded-md" @click="sendID(goal.id,'DELETE')">Delete</button>
            <button v-if="dashboard !== true" :disabled="finishAvailable()" @click="finishGoal(goal.id)" :class="{'bg-green-500 px-1 py-1 rounded-md'
            : !finishAvailable(), 'bg-gray-400 px-1 py-1 rounded-md cursor-not-allowed': finishAvailable()}">Finish</button>
        </div>
        <h1 v-else class="text-center mr-2 font-bold mb-3">GOAL COMPLETED</h1>
    </v-card>
</template>

<script setup>
import {defineEmits} from 'vue';
import {ref} from 'vue';
const props = defineProps({
    goal:Object,
    dashboard:Boolean
})
const emit = defineEmits(['getID']);
const goalValue = ref({
    title:props.goal.title,
    user_id:props.goal.user_id,
    start_date:props.goal.start_date,
    price:props.goal.price,
    end_date:props.goal.end_date,
    status:props.goal.status,
})

const dashboard = props.dashboard;

function finishAvailable(){
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0];

    console.log(props.goal.end_date);
    console.log(formattedDate); // Output: 2024-06-21
    if(formattedDate >= props.goal.end_date){
        return false;
    }else return true;
}

function status(){
    if(goalValue.value.status === 1){
        return "yellow light-1"
    }else if(goalValue.value.status === 3){
        return "blue darken-1"
    }else if (goalValue.value.status === 2){
        return "green darken-1"
    }
}

function statusValue(){
    if(goalValue.value.status === 1){
        return "Planned"
    }else if(goalValue.value.status === 2){
        return "Completed"
    }else {
        return "In progress"
    }
}

async function finishGoal(id){
    try{
        const response = await axios.post('api/goals/finish',{id:id});
        updateGoal(response.data.data)
    }catch(error){
        console.error('An error has occured');
    }
}

function updateGoal(data){
    goalValue.value.title = data.title;
    goalValue.value.user_id = data.user_id;
    goalValue.value.start_date = data.start_date;
    goalValue.value.price = data.price;
    goalValue.value.end_date = data.end_date;
    goalValue.value.status = data.status;
}

function sendID(arg,action){
    emit('getID',arg,action);
}

</script>


<style scoped>

</style>
