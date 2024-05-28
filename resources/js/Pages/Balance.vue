<template>
    <div class="bg-stone-600">
        <h1 v-if="loading === true">Please wait</h1>
        <v-data-table
            :headers="headers"
            :items="items"
            :sort-by="[{ key: 'calories', order: 'asc' }]"


        >
            <template v-slot:top>
                <v-toolbar
                    flat
                >
                    <v-toolbar-title>Balances</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog
                        v-model="dialog"
                        max-width="500px"
                    >
                        <template v-slot:activator="{ props }">
                            <v-btn
                                class="mb-2"
                                color="primary"
                                dark
                                v-bind="props"
                            >
                                New Item
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="text-h5">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col
                                            cols="12"
                                            md="4"
                                            sm="6"
                                        >
                                            <v-combobox
                                                label="Category"
                                                :items="categories"
                                                v-model="defaultItem.category"
                                            ></v-combobox>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="4"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="defaultItem.wage"
                                                label="Wage"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="4"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="defaultItem.recurring"
                                                label="Recurring"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="4"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="defaultItem.type"
                                                label="Type"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="5"
                                            sm="7"
                                        >
                                            <v-date-input  label="Date input" v-model="defaultItem.date"></v-date-input>

                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="3"
                                            sm="5"
                                        >
                                            <v-text-field
                                                v-model="defaultItem.cycle"
                                                label="Cycle"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="12"
                                            sm="5"
                                        >
                                            <v-textarea
                                                v-model="defaultItem.description"
                                                label="Description"
                                            ></v-textarea>

                                        </v-col>

                                    </v-row>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="blue-darken-1"
                                    variant="text"
                                    @click="close"
                                >
                                    Cancel
                                </v-btn>
                                <v-btn
                                    color="blue-darken-1"
                                    variant="text"
                                    @click="formTitle === 'New Balance' ? sendData() : console.log('no')"
                                >
                                    Save
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn  color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
                                <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon
                    class="me-2"
                    color="black"
                    dark
                    size="small"
                    @click="editItem(item)"
                >
                    mdi-pencil
                </v-icon>



                <v-icon
                    size="small"
                    color="black"
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn
                    color="primary"
                    @click="initialize"
                >
                    Reset
                </v-btn>
            </template>
        </v-data-table>
<!--        <div class="mt-5">-->
<!--            <div class="flex justify-center gap-4">-->
<!--                <input  type="text" class="input-field" placeholder="Category">-->
<!--                <input  type="text" class="input-field" placeholder="Title">-->
<!--                <input  type="text" class="input-field" placeholder="Description">-->
<!--                <input  type="date" class="input-field" placeholder="Start Date">-->
<!--                <input  type="number" class="input-field" placeholder="Price">-->
<!--                <input  type="number" class="input-field" placeholder="Initial Budget">-->
<!--                <button @click="addGoal" class="btn-add bg-indigo-600 text-white px-2 py-1 rounded-md">Add Goal</button>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</template>
<script setup>
import { mdiAccount } from '@mdi/js'
</script>
<script>
import {balances} from "./balances.js";
import { VDateInput } from 'vuetify/labs/VDateInput'
import axios from "axios";


export default {
    components: {
        VDateInput
    },
    data: () => ({
        dialog: false,
        items: null,
        categories:null,
        loading:null,
        dialogDelete: false,
        headers: [

            { title: 'ID', key: 'id' },
            { title: 'User ID', key: 'user_id' },
            { title: 'Category ID', key: 'category_id' },
            { title: 'Wage', key: 'wage' },
            { title: 'Recurring', key: 'recurring' },
            { title: 'Description', key: 'description' },
            { title: 'Date', key: 'date' },
            { title: 'Cycle', key: 'cycle' },
            { title: 'Type', key: 'type' },
            { title: 'Actions', key: 'actions', sortable: false },
        ],
        date:null,
        editedIndex: -1,
        editedItem: {
            id: '',
            user_id: '',
            category: '',
            wage: 0,
            recurring: 0,
            description: '',
            date: '',
            cycle: '',
            type: '',
        },
        defaultItem: {
            user_id: 1,
            wage: 0,
            recurring: 0,
            description: '',
            date: null,
            cycle: '',

        },
    }),

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'New Balance' : 'Edit Balance'
        },
    },

    watch: {
        dialog (val) {
            val || this.close()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
    },

    // created () {
    //     this.initialize()
    // },

    beforeMount() {
        this.getData()
        this.getCategories()
    },
    methods: {
        // initialize () {
        //     this.items = balances;
        // },

        async sendData(){
            try{
                const response = await axios.post('/api/balances/create', this.defaultItem);
                console.log(response);

            }catch(error){

            }
            console.log(this.defaultItem);
        },

        async getData(){
            this.loading = true;
            try{
             const response = await axios.get('/api/balances');
             console.log("Balances fetched",response.data);
             this.items = response.data.data;
             this.loading = false;
            }catch(error){
                console.error("Error");
            }
        },

        async getCategories(){
            const response = await axios.get('/api/categories')
            this.categories = response.data;
            console.log(this.categories)
        },

        editItem (item) {
            // this.editedIndex = this.balances.indexOf(item)
            // console.log(this.editedIndex);
            this.editedIndex = 1;
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            console.log(item);
            // this.editedIndex = this.balances.findIndex(balance => balance.id === item.id);
            // this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        deleteItemConfirm () {
            this.balances.splice(this.editedIndex, 1)
            this.closeDelete()
        },

        close () {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        closeDelete () {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save () {
            if (this.editedIndex > -1) {
                Object.assign(this.desserts[this.editedIndex], this.editedItem)
            } else {
                this.desserts.push(this.editedItem)
            }
            this.close()
        },
    },
}
</script>

<!--<script>-->
<!--import Table from "../Components/TheTable.vue"; // Adjust the import path as needed-->
<!--import {balances} from "./balances.js"-->
<!--export default {-->
<!--    components: {-->
<!--        Table-->
<!--    },-->
<!--    data() {-->
<!--        return {-->
<!--            tableHeaders: ["User ID", "Category ID", "Wage", "Recurring", "Description", "Date", "Cycle", "Type"],-->
<!--            tableFields: ["user_id", "category_id", "wage", "recurring", "description", "date","cycle","type"],-->
<!--            balances: balances,-->
<!--        };-->
<!--    }-->
<!--};-->
<!--</script>-->
<style scoped>
.black-icon{
    color:black;
}
</style>
