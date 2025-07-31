

<template>
    <div class="flex-col justify-center gap-2">
        <div>
            <h1 class="text-center mt-5 uppercase font-oswald text-2xl">Dashboard</h1>
        </div>
        <div class="flex justify-center mt-3">
            <v-btn
                color="#3B5555"
                @click="buttonFunction"
                class="text-white bg-blue-600 px-4 py-2 rounded-full shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out"
            >
                <v-icon left class="text-white mr-2">mdi-help-circle-outline</v-icon>
                <span class="font-oswald uppercase tracking-wide">Tutorial</span>
            </v-btn>
        </div>
    </div>
    <div v-if="dataLoaded !== 3" class="flex justify-center my-40" >
        <v-progress-circular indeterminate :size="120" color="slateGray"></v-progress-circular>
    </div>
    <div v-else-if="dataLoaded === 3" class="mt-5 grid grid-cols-1 gap-5  md:grid-cols-4 md:mx-auto md:w-[80%]">
        <transition name="fade" mode="out-in">
        <v-card v-if="step === 1" class="custom-shadow relative bg-white z-20 transition-shadow duration-300 ease-in-out transform scale-105"
                :style="{ boxShadow: '0 4px 20px rgba(0, 0, 0, 0.8)' }">
            <h1 class="text-center font-oswald font-bold mt-5 text-xl">Step 1</h1>
            <p class="p-3 text-justify text-sm">This is the Incomes card, here you can add your incomes click <strong class="uppercase">LIST</strong> to view all your incomes, you can also
                <strong class="uppercase">ADD</strong> new incomes and use the graph to check your increase of incomes in the span of a year. Also your last inputted income is included as information.</p>
            <div class="flex justify-between mx-3 mt-10">
                <button class="bg-slateGray px-2 py-1 rounded-md text-sm text-white font-oswald" @click="buttonFunctionPrev">End</button>
                <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunction">Next</button>
            </div>
        </v-card>
            <v-card v-else
                class="mt-8 mx-auto overflow-visible w-[90%] md:w-[100%] custom-shadow"

            >
                <v-sheet
                    class="v-sheet--offset mx-auto"
                    color="green"
                    elevation="12"
                    max-width="calc(100% - 32px)"
                    rounded="lg"
                >
                    <v-sparkline
                        :labels="labels"
                        :model-value="lineDataIncome"
                        color="white"
                        line-width="2"
                        padding="16"
                    ></v-sparkline>
                </v-sheet>

                <v-card-text class="pt-0">
                    <div class="text-h6 font-weight-light mb-2">
                        Incomes <span class="text-sm font-bold">${{totalIncome}}</span>
                    </div>
<!--                    <div class="subheading font-weight-light text-grey">-->
<!--                        Last Income input-->
<!--                    </div>-->

                    <v-divider class="my-2"></v-divider>
                    <v-icon
                        class="me-2"
                        size="small"
                    >
                        mdi-clock
                    </v-icon>
                    <span class="text-caption text-grey font-weight-light">last income {{lastIncome}}.</span>
                    <div class="mt-3 flex justify-space-between">
                        <v-dialog max-width="500">
                            <template v-slot:activator="{ props: activatorProps }">
                                <v-btn
                                    size="small"
                                    v-bind="activatorProps"
                                    color="surface-variant"
                                    text="List"
                                    variant="flat"
                                ></v-btn>
                            </template>

                            <template v-slot:default="{ isActive }">
                                <v-card title="Dialog">
                                    <v-data-table
                                    :headers="tableHeaders"
                                    :items="sortedIncomes"
                                    :key="key"
                                    item-value="id"
                                    class="elevation-1">
                                    </v-data-table>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text="Close Dialog"
                                            @click="isActive.value = false"
                                        ></v-btn>
                                    </v-card-actions>
                                </v-card>
                            </template>
                        </v-dialog>

                        <v-dialog v-model="addIncomeDialog" max-width="500">
                            <template v-slot:activator="{ props: activatorProps }">
                                <v-btn
                                    size="small"
                                    v-bind="activatorProps"
                                    color="surface-variant"
                                    text="Add"
                                    variant="flat"
                                    @click = "whichForm = false"
                                >
                                    Add
                                </v-btn>
                            </template>

                            <template v-slot:default>
                                <v-card>
                                    <v-card-title>
                                        <span class="flex justify-center text-2xl mt-4 font-oswald text-slateGray">{{ formTitle }}</span>
                                    </v-card-title>

                                    <v-card-text>
                                        <v-container>
                                            <v-row>
                                                <v-col v-if="whichForm === true"
                                                       cols="12"
                                                       md="6"
                                                       sm="6"
                                                >
                                                    <v-combobox
                                                        label="Category"
                                                        :items="categories"
                                                        v-model="balanceObject.category"
                                                    ></v-combobox>
                                                </v-col>
                                                <v-col
                                                    cols="12"
                                                    md="6"
                                                    sm="6"
                                                >
                                                    <v-combobox
                                                        label="Sub Category"
                                                        :items="subCategories"
                                                        v-model="balanceObject.sub_category"
                                                    ></v-combobox>
                                                </v-col>
                                                <v-col
                                                    cols="10"
                                                    md="6"
                                                    sm="6"
                                                >
                                                    <v-text-field
                                                        v-model="balanceObject.wage"
                                                        label="Wage"
                                                    ></v-text-field>
                                                </v-col>

                                                <v-col
                                                    cols="12"
                                                    md="6"
                                                    sm="7"
                                                >
                                                    <v-date-input  label="Date input" v-model="balanceObject.date"></v-date-input>

                                                </v-col>

                                                <v-col
                                                    cols="12"
                                                    md="12"
                                                    sm="5"
                                                >
                                                    <v-textarea
                                                        v-model="balanceObject.description"
                                                        label="Description"
                                                    ></v-textarea>

                                                </v-col>
                                                <!--                                            <div v-if="errors" class="ml-3">-->
                                                <!--                                                <p v-for="error in errors" class="text-red-500">-->
                                                <!--                                                    {{error}}-->
                                                <!--                                                </p>-->
                                                <!--                                            </div>-->
                                            </v-row>

                                        </v-container>
                                    </v-card-text>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            color="blue-darken-1"
                                            variant="text"
                                            @click="addIncomeDialog = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            color="blue-darken-1"
                                            variant="text"
                                            @click="sendData"
                                        >
                                            Save
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </template>
                        </v-dialog>

                    </div>
                </v-card-text>

            </v-card>
        </transition>
        <transition name="fade" mode="out-in">
        <v-card v-if="step === 2" class="custom-shadow relative bg-white z-20 transition-shadow duration-300 ease-in-out transform scale-105"
                :style="{ boxShadow: '0 4px 20px rgba(0, 0, 0, 0.8)' }">
            <h1 class="text-center font-oswald font-bold mt-4 text-xl">Step 2</h1>
            <p class="p-3 text-justify text-sm">This is the Goals card, here you can see the difference between incomes and expenses which is <strong class="uppercase">BALANCE</strong> , and you keep track of your
            goals here how many of them are in <strong>progress</strong>, how many of them are <strong>planned</strong> and which goals are <strong>completed</strong>,you can also view that information each goal card bellow.</p>
            <div class="flex justify-between mx-3 my-1">
                <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunctionPrev">Prev</button>
                <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunction">Next</button>
            </div>
        </v-card>
        <v-card v-else
            class="mx-auto w-[90%] md:w-[100%] custom-shadow"
        >
<!--            <template v-slot:title>-->
<!--                <span class="text-md font-weight-black">Goals</span>-->
<!--            </template>-->

            <h1 class="text-slateGray font-bold font-oswald text-2xl text-center mt-5 mb-5">Balance: ${{totalIncome - totalExpense}}</h1>


            <div class="text-xl font-bold text-center font-oswald mb-7">Goals:</div>
            <div class="text-center  mb-3 flex-col  gap-4">
                <div class="mb-5">
                    <h1 class="text-xl uppercase font-bold font-oswald text-#ffeb3b">
                        In progress: <span style="color:  #2196f3; text-shadow: 2px 2px 0 black;" class="font-bold text-2xl">{{inProgress}}</span>
                    </h1>
                </div>
                <div class="mb-7">
                    <h1 class="text-xl uppercase font-bold font-oswald">Planned: <span style="color: #ffeb3b; text-shadow: 2px 2px 0 black;" class="font-bold text-2xl">{{planned}}</span></h1>

                </div>
            </div>

        </v-card>
        </transition>
        <transition name="fade" mode="out-in">
            <v-card v-if="step === 3" class="custom-shadow relative bg-white z-20 transition-shadow duration-300 ease-in-out transform scale-105"
                    :style="{ boxShadow: '0 4px 20px rgba(0, 0, 0, 0.8)' }">
                <h1 class="text-center font-oswald font-bold mt-4 text-xl">Step 3</h1>
                <p class="p-3 text-justify text-sm">
                    This card holds information about your expenses divided into the three main categories: wants,needs and savings. It will calculate the percentage
                    of your expenses and see how much percent you have in each category. Since you yourself input your desired percentages if your expenses go more then that the line will overflow and go red.
                </p>
                <div class="flex justify-between mx-3 my-1">
                    <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunctionPrev">Prev</button>
                    <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunction">Next</button>
                </div>
            </v-card>
        <v-card v-else
            class="mx-auto w-[90%] md:w-[100%] custom-shadow"
        >


            <h1 class="text-slateGray font-bold font-oswald text-2xl text-center mt-3 mb-3">Expenses: ${{totalExpense}}</h1>

            <h1 class="text-center font-bold">Wants <span>${{expenses.wants}}</span> <span :class="{'text-red':testArray[0] > wants[0].percentage,
            'text-green-500': testArray[0] <= wants[0].percentage}">{{testArray[0]}}</span>/{{wants[0].percentage}}%</h1>
            <div class="relative w-[90%] h-5 mb-3 border-black border-2 mx-auto rounded-sm">
                <!-- First bar (background bar) -->
                <div
                    :class="{
            'bg-red-500 ': wants[0].percentage < testArray[0],
            'bg-green-600': wants[0].percentage >= testArray[0]
        }"
                    class="absolute h-full left-0 top-0 border-black border-r-2"
                    :style="{ width: testArray[0] + '%' }"
                >
                    <p class="text-right ml-2 text-xs font-bold text-white"></p>
                </div>

                <!-- Second bar (overlay for exceeding threshold) -->
                <div
                    class="absolute h-full left-0 top-0 bg-stone-400 opacity-15 border-black border-r-2"
                    :style="{ width: wants[0].percentage + '%', zIndex: 1 }"
                ></div>
            </div>
            <h1 class="text-center font-bold">Needs <span>${{expenses.needs}}</span> <span :class="{'text-red':testArray[1] > needs[0].percentage,
'text-green-500': testArray[1] <= needs[0].percentage}">{{testArray[1]}}</span>/{{needs[0].percentage}}%</h1>
            <div class="relative w-[90%] h-5 mb-3 border-black border-2 mx-auto rounded-sm">
                <!-- First bar (background bar) -->
                <div
                    :class="{
            'bg-red-500': needs[0].percentage < testArray[1],
            'bg-green-500': needs[0].percentage >= testArray[1]
        }"
                    class="absolute h-full left-0 top-0 border-black border-r-2"
                    :style="{ width: testArray[1] + '%' }"
                >
                    <p class="text-right mr-2 text-xs font-bold text-white"></p>
                </div>

                <!-- Second bar (overlay) -->
                <div
                    class="absolute h-full left-0 top-0 bg-stone-400 opacity-15 border-black border-r-2"
                    :style="{ width: investSavings[0].percentage + '%', zIndex: 1 }"
                ></div>
            </div>
            <h1 class="text-center font-bold">Invest and Savings <span>${{expenses.savings}}</span> <span :class="{'text-red':testArray[2] > investSavings[0].percentage,
            'text-green-500': testArray[2] <= investSavings[0].percentage}">{{testArray[2]}}</span>/{{investSavings[0].percentage}}%</h1>
            <div class="relative w-[90%] h-5 mb-3 border-black border-2 mx-auto rounded-sm">
                <!-- First bar (background bar) -->
                <div
                    :class="{
            'bg-red-500': investSavings[0].percentage < testArray[2],
            'bg-green-500': investSavings[0].percentage >= testArray[2]
        }"
                    class="absolute h-full left-0 top-0 border-black border-r-2"
                    :style="{ width: testArray[2] + '%' }"
                >
                    <p class="text-right mr-2 text-xs font-bold text-white"></p>
                </div>

                <!-- Second bar (overlay with opacity) -->
                <div
                    class="absolute h-full left-0 top-0 bg-stone-400 opacity-15 border-black border-r-2"
                    :style="{ width: investSavings[0].percentage + '%', zIndex: 1 }"
                ></div>
            </div>


        </v-card>
        </transition>
        <transition name="fade" mode="out-in">
            <v-card v-if="step === 4" class="custom-shadow relative bg-white z-20 transition-shadow duration-300 ease-in-out transform scale-105"
                    :style="{ boxShadow: '0 4px 20px rgba(0, 0, 0, 0.8)' }">
                <h1 class="text-center font-oswald font-bold mt-4 text-xl">Step 4</h1>
                <p class="p-3 text-justify text-sm">This is the Expenses card, here you can add your expenses click <strong class="uppercase">LIST</strong> to view all your expenses, you can also
                    <strong class="uppercase">ADD</strong> new expenses and use the graph to check your increase of expenses in the span of a year. Also your last inputted expense is included as information.
                </p>
                <div class="flex justify-between mx-3 mt-6">
                    <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunctionPrev">Prev</button>
                    <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunction">Next</button>
                </div>
            </v-card>
            <v-card v-else-if="step === 5" class="custom-shadow relative bg-white z-20 transition-shadow duration-300 ease-in-out transform scale-105"
                    :style="{ boxShadow: '0 4px 20px rgba(0, 0, 0, 0.8)' }">
                <h1 class="text-center font-oswald font-bold mt-4 text-xl">Step 5</h1>
                <p class="p-3 text-justify text-sm">Below are all the goals with key details like the <strong class="uppercase">start date</strong>, <strong class="uppercase">end date</strong>, <strong class="uppercase">price</strong>, and <strong class="uppercase">status</strong>. Track your goals to see which are <strong>in progress</strong>, <strong>planned</strong>, or <strong>completed</strong>, and adjust as needed to stay on track with your plans.</p>

                <div class="flex justify-between mx-3 mt-11">
                    <button class=" bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunctionPrev">Prev</button>
                    <button class="bg-slateGray px-2 font-oswald py-1 rounded-md text-sm text-white" @click="buttonFunction">Finish</button>
                </div>
            </v-card>
        <v-card v-else
            class="mt-8 mx-auto overflow-visible w-[90%] md:w-[100%] custom-shadow"
        >
            <v-sheet
                class="v-sheet--offset mx-auto"
                color="red"
                elevation="12"
                max-width="calc(100% - 32px)"
                rounded="lg"
            >
                <v-sparkline
                    :labels="labels"
                    :model-value="lineDataExpense"
                    color="white"
                    line-width="2"
                    padding="16"
                ></v-sparkline>
            </v-sheet>

            <v-card-text class="pt-0">
                <div class="text-h6 font-weight-light mb-2">
                    Expenses <span class="text-sm font-bold">${{totalExpense}}</span>
                </div>

                <v-divider class="my-2"></v-divider>

                <v-icon
                    class="me-2"
                    size="small"
                >
                    mdi-clock
                </v-icon>
                <span class="text-caption text-grey font-weight-light">last expense {{lastExpense}}.</span>


                <div class="mt-3 flex justify-space-between">
                    <!-- Dialog for Listing Expenses -->
                    <v-dialog v-model="listDialog" max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn
                                size="small"
                                v-bind="activatorProps"
                                color="surface-variant"
                                text="List"
                                variant="flat"
                            >
                                List
                            </v-btn>
                        </template>

                        <template v-slot:default>
                            <v-card title="Expenses">
                                <v-data-table
                                    :headers="tableHeaders"
                                    :items="sortedExpenses"
                                    item-value="id"
                                    class="elevation-1"
                                >
                                </v-data-table>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text="Close Dialog"
                                        @click="listDialog = false"
                                    >
                                        Close Dialog
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>

                    <!-- Dialog for Adding Expense -->
                    <v-dialog v-model="addExpenseDialog" max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn
                                size="small"
                                v-bind="activatorProps"
                                color="surface-variant"
                                text="Add"
                                variant="flat"
                                @click = "whichForm = false"
                            >
                                Add
                            </v-btn>
                        </template>

                        <template v-slot:default>
                            <v-card>
                                <v-card-title>
                                    <span class="flex justify-center text-2xl mt-4 font-oswald text-slateGray">{{ formTitle }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col v-if="addExpenseDialog === true"
                                                cols="12"
                                                md="6"
                                                sm="6"
                                            >
                                                <v-combobox
                                                    label="Category"
                                                    :items="categories"
                                                    v-model="balanceObject.category"
                                                ></v-combobox>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                md="6"
                                                sm="6"
                                            >
                                                <v-combobox
                                                    label="Sub Category"
                                                    :items="subCategories"
                                                    v-model="balanceObject.sub_category"
                                                ></v-combobox>
                                            </v-col>
                                            <v-col
                                                cols="10"
                                                md="6"
                                                sm="6"
                                            >
                                                <v-text-field
                                                    v-model="balanceObject.wage"
                                                    label="Wage"
                                                ></v-text-field>
                                            </v-col>

                                            <v-col
                                                cols="12"
                                                md="6"
                                                sm="7"
                                            >
                                                <v-date-input  label="Date input" v-model="balanceObject.date"></v-date-input>

                                            </v-col>

                                            <v-col
                                                cols="12"
                                                md="12"
                                                sm="5"
                                            >
                                                <v-textarea
                                                    v-model="balanceObject.description"
                                                    label="Description"
                                                ></v-textarea>

                                            </v-col>
<!--                                            <div v-if="errors" class="ml-3">-->
<!--                                                <p v-for="error in errors" class="text-red-500">-->
<!--                                                    {{error}}-->
<!--                                                </p>-->
<!--                                            </div>-->
                                        </v-row>

                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="blue-darken-1"
                                        variant="text"
                                        @click="addExpenseDialog = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        color="blue-darken-1"
                                        variant="text"
                                        @click="sendData"
                                    >
                                        Save
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                </div>
            </v-card-text>
        </v-card>
        </transition>
        <v-card
            class="mx-auto w-[90%] md:w-[100%] rounded-md p-4 custom-shadow"
            v-for="goal in goalList"
            :key="goal.id"
            outlined
        >
            <v-card-title class="text-lg font-bold flex items-center">
                <v-icon class="mr-2">{{ goal.status === 2 ? 'mdi-flag-checkered' : 'mdi-flag' }}</v-icon>
                Goal: {{ goal.title }}
            </v-card-title>

            <v-card-text>
                <p class="text-gray-600"><strong>Description: </strong>{{ goal.description }}</p>

                <v-divider class="my-2"></v-divider>

                <p><strong>Start date:</strong> {{ goal.start_date }}</p>
                <p><strong>Price:</strong> ${{ goal.price }}</p>
                <p><strong>End date:</strong> {{ goal.end_date }}</p>

                <p class="mt-2 flex items-center">
                    <strong>Status:</strong>
                    <v-icon
                        class="ml-1 mr-1"
                        :class="{
          'text-green-500': goal.status === 2,
          'text-yellow-500': goal.status === 3,
          'text-blue-500': goal.status === 1,
          'text-gray-500': goal.status !== 1 && goal.status !== 2 && goal.status !== 3
        }"
                    >
                        {{ goal.status === 3 ? 'mdi-flag' : goal.status === 1 ? 'mdi-flag-outline' : goal.status === 2 ? 'mdi-flag-checkered' : 'mdi-help-circle' }}
                    </v-icon>
                    {{ goal.status === 3 ? ' In progress' : goal.status === 1 ? ' Planned' : goal.status === 2 ? ' Completed' : ' Unknown' }}
                </p>
            </v-card-text>
        </v-card>





    </div>
    <div>

    </div>
<!--    <div class="grid grid-cols-3 w-[77%] mx-auto">-->
<!--        <div  class="my-6 grid grid-cols-1 md:grid-cols-4  md:gap-x-80">-->
<!--            <GoalCard v-for="goal in goalList" :key="null" :goal="goal" class="mb-5 mx-auto" @getID="null" :dashboard="true"></GoalCard>-->
<!--        </div>-->
<!--    </div>-->
</template>

<script setup>
import {VDateInput} from "vuetify/labs/VDateInput";


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
import {computed, onMounted} from "vue";
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, ArcElement, Legend,BarElement);
import {ref} from 'vue';
import GoalCard from "@/Components/GoalCard.vue";

const labels = [
    "1", "2", "3", "4", "5", "6",
    "7", "8", "9", "10", "11", "12"
];

const data1 = [Math.random(), Math.random(), Math.random(), Math.random(),
    Math.random(), Math.random(), Math.random(), Math.random(),
    Math.random(), Math.random(), Math.random(), Math.random()]

const dataLine = computed(() => {
    return {
        labels:[
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ],
        datasets:
            [
                {
                    label: "Income per month",
                    backgroundColor: "#17f00c",
                    data:  [
                        Math.random(), Math.random(), Math.random(), Math.random(),
                        Math.random(), Math.random(), Math.random(), Math.random(),
                        Math.random(), Math.random(), Math.random(), Math.random()
                    ],
                }
            ]
    };
})

const dataLine2 = computed(() => {
    return {
        labels:[
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ],
        datasets:
            [
                {
                    label: "Expense per month",
                    backgroundColor: "#ad0d0a",
                    data:  [
                        Math.random(), Math.random(), Math.random(), Math.random(),
                        Math.random(), Math.random(), Math.random(), Math.random(),
                        Math.random(), Math.random(), Math.random(), Math.random()
                    ],
                }
            ]
    };
})

const options = {
    responsive:true,
    maintainAspectRatio:false
}

async function getIncomes(){
    try{
        const responseIncomes = await axios.get('/api/balances/incomes');
        const responseExpenses = await axios.get('/api/balances/expenses');
        console.log(responseIncomes.data.data);
        dataLoaded.value ++;
        totalIncomes(responseIncomes.data.data);
        totalExpenses(responseExpenses.data.data)



    }catch(error){

    }
}



const lineDataIncome = ref(new Array(12).fill(0));
const lineDataExpense =  ref(new Array(12).fill(0));
const totalIncome = ref(null);
const totalExpense = ref(null);
const flag = ref(0);
const flag2 = ref(0);
const lastIncome = ref(null);
const lastExpense = ref(null);
const planned = ref(null);
const inProgress = ref(null);
const progress = ref(80);
const whichForm = ref(false);
const expensesPercentages = ref(null);
const sortedIncomes = ref(null);
const sortedExpenses = ref(null);
const wants = ref(null);
const needs = ref(null);
const investSavings = ref(null);
const key = ref(0);
const incomeCategory = ref(null);
const dataLoaded = ref(null);
const expenses = ref({
    wants: 0,
    needs: 0,
    savings: 0
});
const step = ref(0);
const formTitle = ref('Test');
const listDialog = ref(false);
const addIncomeDialog = ref(false);
const goalList = ref(false);
const addExpenseDialog = ref(false);
const categories = ref([]);
const subCategories = ref([]);
const balanceObject = ref({
        category:null,
        sub_category:null,
        wage: 0,
        description: '',
        date: null,
        type:1
});
const  tableHeaders = [
    { text: 'Category', value: 'category.title' },
    { text: 'Sub Category', value: 'sub_category.title' },
    { text: 'Date', value: 'date' },
    { text: 'Description', value: 'description' },
    { text: 'Wage', value: 'wage' } ]


async function sendData(){
    try{
        if(addIncomeDialog.value === true){
            balanceObject.value.category = incomeCategory.value[0];
        }
        addIncomeDialog.value === true ? balanceObject.value.type = 2 : balanceObject.value.type = 1;
        const date = new Date(balanceObject.value['date']);
        balanceObject.value.date = date.toLocaleDateString('en-CA');
        const wage = Number(balanceObject.value.wage);
        if(addExpenseDialog){
            let categoryArg = balanceObject.value.category.title.toLowerCase();
            categoryArg === 'investment and savings' ? categoryArg = 'savings' : '';
            console.log(expenses.value[`${categoryArg}`]);
            expenses.value[`${categoryArg}`] += wage;

        }
        const response = await axios.post('/api/balances/create', balanceObject.value);
        const dateMonth = new Date(balanceObject.value.date).getMonth()+1;
        addIncomeDialog.value === true ? lineDataIncome.value[dateMonth] += wage : lineDataExpense.value[dateMonth] += wage;
        addIncomeDialog.value === true ? totalIncome.value += wage : totalExpense.value += wage;
        addIncomeDialog.value === true ? totalExpenses(sortedExpenses.value,balanceObject.value) : totalIncomes(sortedIncomes.value,balanceObject.value);
        addIncomeDialog.value === true ? addIncomeDialog.value = false : addExpenseDialog.value = false;

        key.value = Math.random();
        balanceObject.value = {
            category:null,
            sub_category:null,
            wage: 0,
            description: '',
            date: null,
            type:1
        }
        console.log(response);
    }catch(error){
        console.log(error);
    }

}

function buttonFunction(){
    step.value ++;
    if(step.value === 6){
        step.value = 0;
    }
}

function buttonFunctionPrev(){
    step.value --;
}

async function getCategories(){
    const currentYear = new Date().getFullYear();
    const response = await axios.get('/api/categories')
    const response2 = await axios.get('/api/categories/exp')
    const response3 = await axios.post('/api/balances/dashboard/percentage');
    const percentages = response3.data;
    expenses.value = {
        wants: percentages.total.wants || 0,
        needs: percentages.total.needs || 0,
        savings: percentages.total.savings || 0
    };

    subCategories.value = response2.data;
    categories.value = response.data;
    dataLoaded.value ++;
    incomeCategory.value = categories.value.filter((item) => {
        return item.title === 'Income';
    })
    categories.value = categories.value.filter((item) => {
        return item.title !== 'Income';
    })
    wants.value = categories.value.filter((item) => {
        return item.title === 'Wants';
    })
    needs.value = categories.value.filter((item) => {
        return item.title === 'Needs';
    })
    investSavings.value = categories.value.filter((item) => {
        return item.title === 'Investment and Savings'
    })
};

async function getData(){

    try{
        const response = await axios.get('api/goals');
        // console.log(response);
        console.log("Goals fetched.", response.data);
        dataLoaded.value ++;
        goalList.value = response.data.data;
        goalList.value.map((item) =>{
            if(item.status === 1 ){
                planned.value ++;
            }else if(item.status === 3){
                inProgress.value ++;
            }
        })


    }catch(error){
        error.value = error;
        console.error("Error fetching goals", error)
    }
}

function getProgressColor(progress) {
    if (progress < 50) {
        return 'green';
    } else if (progress < 100) {
        return 'yellow';
    } else {
        return 'red'; // Exceeds the limit
    }
}

function totalIncomes(array, optional){

    if(optional){
        array = [...array, optional];
    }
    const sortedArray = array.sort((a,b) => new Date(a.date) - new Date(b.date));
    sortedIncomes.value = sortedArray;
    key.value = Math.random();
    lastIncome.value = sortedArray[sortedArray.length-1]['date'];
    sortedArray.forEach(item => {
        const month = new Date(item['date']).getMonth()+1;
        lineDataIncome.value[month]+= item.wage;
    })
    if(flag.value === 0){
        totalIncome.value = lineDataIncome.value.reduce((accumulator,currentValue) => {
            accumulator+= currentValue;
            return accumulator;
        },0)
    }
    flag.value++;

}
function totalExpenses(array,optional){
    if(optional){
        array = [...array, optional];
    }

    const sortedArray = array.sort((a,b) => new Date(a.date) - new Date(b.date));
    sortedExpenses.value = sortedArray;
    lastExpense.value = sortedArray[sortedArray.length-1]['date'];
    sortedArray.forEach(item => {
        const month = new Date(item['date']).getMonth()+1;
        lineDataExpense.value[month]+= item.wage;
    })
    if(flag2.value === 0){
        totalExpense.value = lineDataExpense.value.reduce((accumulator,currentValue) => {
            accumulator+= currentValue;
            return accumulator;
        },0)
    }
    flag2.value++;

}


const testArray = computed(() => {
    if (totalExpense.value === 0) return [0, 0, 0]; // Avoid division by zero
    return [
        Math.round((expenses.value.wants / totalExpense.value) * 100),
        Math.round((expenses.value.needs / totalExpense.value) * 100),
        Math.round((expenses.value.savings / totalExpense.value) * 100)
    ];
});




onMounted(() => {
    getIncomes();
    getCategories();
    getData();
});


</script>

<style scoped>
.v-sheet--offset {
    top: -24px;
    position: relative;
}
.v-data-table-header th {
    color: black !important; /* Change this to a color that will be visible */
}

.custom-shadow {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 -4px 8px rgba(0, 0, 0, 0.1);
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}


</style>
