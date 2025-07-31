<template>
    <v-stepper  hide-actions :items="['Step 1', 'Step 2', 'Step 3','Step 4']" v-model="activeStep" :complete>
        <template v-if="activeStep === 1"  >
            <v-card title="Basic info" class="text-center h-[480px]" flat>
                <div class=" mt-3 mx-auto  rounded-md text-white text-lg">
                    <v-sheet  class="mt-10 mx-auto" width="300">
                        <v-form  v-model="step1Valid">
                            <v-text-field
                                label="Enter your monthly wage"
                                v-model="balanceObject.wage"
                                :rules="[v => !!v || 'Monthly wage is required']"
                            ></v-text-field>
                            <v-text-field
                                label="Enter your total budget"
                                v-model="balanceObject.budget"
                                :rules="[v => !!v || 'Total budget is required']"
                            ></v-text-field>
                        </v-form>
                    </v-sheet>

                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-space-between mx-14 mt-3 pb-5">
                    <v-btn color="grey" @click="prev()">Prev</v-btn>
                    <v-btn color="grey" @click="validate(1)" :disabled="!step1Valid">Next</v-btn>
                </div>

            </v-card>
        </template>

        <template v-if="activeStep === 2" >
            <v-card title="Input financial information" class="text-center h-[480px]" flat>
                <div>
                    <v-sheet  class="mt-10 mx-auto" width="300">
                        <v-form  v-model="step2Valid">
                            <v-text-field
                                label="Wants:"
                                v-model="balanceObject.wants"
                                :rules="[v => !!v || 'Wants are required']"
                            ></v-text-field>
                            <v-text-field
                                label="Needs:"
                                v-model="balanceObject.needs"
                                :rules="[v => !!v || 'Needs are required']"
                            ></v-text-field>
                            <v-text-field
                                label="Savings:"
                                v-model="balanceObject.savings"
                                :rules="[v => !!v || 'Investment and savings are required']"
                            ></v-text-field>
                        </v-form>
                    </v-sheet>
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-space-between mx-14 mt-3 pb-5">
                    <v-btn color="grey" @click="prev()">Prev</v-btn>
                    <v-btn color="grey" @click="validate(2)" :disabled="!step2Valid">Next</v-btn>
                </div>
            </v-card>
        </template>

        <template v-if="activeStep === 3">
            <v-card  title="Define your goal" flat class="text-center  h-[480px]" >
                <div>
                    <v-sheet  class="mt-10 mx-auto" width="300">
                        <v-form v-model="step3Valid" >
                            <v-text-field
                                label="Desired Goal price:"
                                v-model="balanceObject.goalPrice"
                                :rules="[v => !!v || 'Goal price is required']"
                            ></v-text-field>
                            <v-date-input label="Goal end date:" variant="outlined" v-model="balanceObject.goalEndDate" ></v-date-input>
                        </v-form>
                    </v-sheet>
                </div>
                <div class="absolute inset-x-0 bottom-0 flex justify-space-between mx-14 mt-3 pb-5">
                    <v-btn color="grey" @click="prev()">Prev</v-btn>
                    <v-btn color="grey" @click="validate(3)" :disabled="!step3Valid">Next</v-btn>
                </div>
            </v-card>
        </template>
        <template v-if="activeStep === 4" >
            <v-card title="Step Four" flat class="text-center h-[580px] md:h-[410px]">
                <div v-if="goalFlag">
                    <p>Your goal is <span class="text-green-600 font-bold">{{goalFlag ? 'FEASIBLE' : 'NOT-FEASIBLE'}}</span></p>
                    <p>The end date is: <span class="">{{endDate}}</span></p>
                    <p class="text-center font-bold">Monthly calculations and Goal estimation</p>
                    <p class="text-center">Estimated End Date: <span class="font-bold">{{ formatDate(balanceObject.goalEndDate) }}</span></p>
                    <p class="text-center font-anton text-lg mb-3">Months till Goal completion:</p>
                </div>
                <v-carousel v-if="goalFlag">
                    <v-carousel-item v-for="(item, index) in Math.ceil(resultArray.length / 3)" :key="index">
                        <div class="mt-2 p-5 bg-customGray rounded-lg shadow-md">
                            <div v-if="goalFlag" class="">
                                <div class="grid grid-cols-1  md:grid-cols-3 gap-4 text-sm">
                                    <div v-for="(innerItem, innerIndex) in resultArray.slice(index * 3, (index + 1) * 3)" :key="innerIndex" class="p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex flex-col ">
                                                <p>Month:</p> <span class="font-bold">{{ months[innerItem.month-1] }}</span>

                                            </div>
                                            <p>Balance: <span class="font-bold">{{ innerItem.balance }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </v-carousel-item>
                </v-carousel>
                <div v-else class="text-center max-w-sm mx-auto bg-customGray p-5 rounded-md">
                    <p>Your goal is <span :class="{'text-red-600 font-bold':!goalFlag}">{{goalFlag ? 'FEASIBLE' : 'NOT-FEASIBLE'}}</span></p>
                    <h1>Please lower your desires, goals are achieved by good financial management!</h1>
                    <p>Use our <span class="font-bold">ExpenseTracker App</span> to further go into details and achieve your goals</p>
                    <img :src="imageSrc" class="h-[100px] w-[100px] mx-auto rounded-lg mt-3 mb-3"/>
                    <a href="#" class="text-blue-500 font-bold uppercase">Click here to become a member</a>
                </div>
            </v-card>
            <div class=" flex justify-space-between mx-14 mt-3 pb-5">
                <v-btn color="grey" @click="prev()">Prev</v-btn>
<!--                <v-btn color="grey" @click="validate(4)">Next</v-btn>-->
            </div>
        </template>
    </v-stepper>

</template>

<script setup>
import {ref} from 'vue';
import {VDateInput} from 'vuetify/labs/VDateInput'
const imageSrc = '/img.png';
const activeStep = ref(1);
const step1Valid = ref(false);
const step2Valid = ref(false);
const step3Valid = ref(false);
const goalFlag = ref(null);
const endDate = ref(null);
const currentBalance = ref(null);
const resultArray = ref(null);
const activeStepStyle = ref('bg-grey-800 text-white')
const balanceObject = ref({
    wage:null,
    budget:null,
    wants:null,
    needs:null,
    savings:null,
    goalPrice:null,
    goalEndDate:null,
    goalDescription:null,
})
const title = ref(['Fill information about yourself', 'Define your incomes and expenses', 'Enter a goal you want', 'Result']);
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const step1Form = ref(null);
function next(){
    if(activeStep.value === 4){
        return;
    }
    activeStep.value +=1;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
}

function formatDateMonth(date){
    const d = new Date(date);
    return d.getMonth();
}

function getCurrentMonth(){
    const d = new Date();
    return d.getMonth();
}

function prev(){
    if(activeStep.value === 1){
        return;
    }
    activeStep.value -= 1;
}



async function sendData(){
    try{
        const response = await axios.post("/api/demo",balanceObject.value);
        resultArray.value = response.data.data;
        goalFlag.value = resultArray.value[resultArray.value.length - 1]?.feasible;
        endDate.value = resultArray.value[resultArray.value.length - 1].endDate;
        currentBalance.value = resultArray.value[resultArray.value.length - 1].balanceNow;
    }catch(error){
        console.error(error);
    }
}

function validate(arg) {
    const stepValid = `step${arg}Valid`;
    if(eval(stepValid).value || arg === 4){
        if(arg === 3){
            sendData();
        }
        next();
}else{
        alert("Please fill in all the inputs!")
    }}

</script>

<style scoped>
/deep/ .v-btn--icon.v-btn--density-default, >>> .v-btn--icon.v-btn--density-default {
    width: calc(var(--v-btn-height) + 12px);
    height: calc(var(--v-btn-height) + 12px);
    margin-bottom: 215px;
}

</style>
