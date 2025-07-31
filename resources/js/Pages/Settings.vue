<template>
    <div v-if="isLoading === 'loading'" class="flex justify-center my-40" >
        <v-progress-circular indeterminate :size="120" color="slateGray"></v-progress-circular>
    </div>
    <div v-if="isLoading === 'loaded'" class="flex flex-col gap-3 bg-stone-100 mx-auto max-w-xs rounded-md shadow-lg justify-center items-center mt-5">
        <div class="flex gap-2 mt-5 justify-center">
            <v-icon color="slateGray" size="large" class="mt-1">
                mdi-cog
            </v-icon>
            <div><h1 class="text-3xl font-oswald text-slateGray">Settings</h1></div>
        </div>
        <div class="flex flex-col gap-1  text-slateGray px-2 py-2 rounded-md w-[85%]">
            <div class="flex gap-1">
                <label class="font-oswald font-bold text-xl">Wants:</label>
                <span class="font-oswald font-bold text-xl">{{settingsObject.wants === null ? 'NOT SET' : settingsObject.wants}}%</span>
            </div>
            <input class="bg-white rounded-full px-1 py-1 mt-1 text-center border border-slateGray" v-model="data.wants" placeholder="change wants">
        </div>
        <div class="flex flex-col gap-1 text-slateGray px-2 py-2 rounded-md w-[85%]">
            <div class="flex gap-1">
                <label class="font-oswald font-bold text-xl">Needs:</label>
                <span class="font-oswald font-bold text-xl">{{settingsObject.needs === null ? 'NOT SET' : settingsObject.needs}}%</span>
            </div>
            <input class="bg-white rounded-full  px-1 py-1 mt-1 text-center border border-slateGray" v-model="data.needs" placeholder="change needs">
        </div>
        <div class="flex flex-col gap-1 text-slateGray px-2 py-2 rounded-md w-[85%]">
            <div class="flex gap-1">
                <label class="font-oswald font-bold text-xl">Invest and save:</label>
                <span class="font-oswald font-bold text-xl">{{settingsObject.savingsInvest === null ? 'NOT SET' : settingsObject.savingsInvest}}%</span>
            </div>
            <input class="bg-white rounded-full px-1 py-1 mt-1 text-center border border-slateGray" v-model="data.saveInvest" placeholder="change invest & savings">
        </div>
        <div class="flex ml-auto mr-8 mb-4"><button @click="saveCategories" class="bg-slateGray px-2 py-1 text-white font-bold font-oswald rounded-md">Save</button></div>
    </div>
</template>

<script setup>
import {onBeforeMount, ref} from 'vue';
const isLoading = ref(null);
const settingsObject = ref({
    wants:null,
    needs:null,
    savingsInvest:null,
});
const data = ref({
    wants:null,
    needs:null,
    saveInvest:null,
});

async function getCategoriesData(){
    isLoading.value = 'loading';
    try{
        const response = await axios.get('api/categories')
        console.log(response);
        settingsObject.value = {
            wants:response.data[0].percentage,
            needs:response.data[1].percentage,
            savingsInvest:response.data[2].percentage,
        }
        isLoading.value = 'loaded';
    }catch(error){
        console.error(error);
    }
    console.log(settingsObject.value);
}

async function saveCategories(){
    try{
        const response = await axios.post('api/categories/save',data.value)
        console.log(response);

    }catch(error){
        console.error(error);
    }
}
function sendData(){
    console.log(data.value);
}

onBeforeMount(getCategoriesData);

</script>

<style scoped>

</style>
