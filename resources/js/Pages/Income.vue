<template>
    <div>
        <div v-if="loading" class="flex justify-center my-10" >
            <v-progress-circular indeterminate :size="100"></v-progress-circular>
        </div>
        <v-data-table
            v-else
            :headers="headers"
            :items="items"
            :sort-by="[{ key: 'calories', order: 'asc' }]"
            :key="tableKeyComp"
        >
            <template v-slot:top>
                <v-toolbar
                    flat
                >
                    <v-toolbar-title>Incomes</v-toolbar-title>
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
                                                v-model="balanceObject.category"
                                            ></v-combobox>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="4"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="balanceObject.wage"
                                                label="Wage"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col
                                            cols="12"
                                            md="4"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="balanceObject.type"
                                                label="Type"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="5"
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
                                        <div v-if="errors" class="ml-3">
                                            <p v-for="error in errors" class="text-red-500">
                                                {{error[0]}}
                                            </p>
                                        </div>
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
                                    @click="formTitle === 'New Balance' ? sendData() : updateData()"
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
                                <v-btn color="blue-darken-1" variant="text" @click="deleteData">OK</v-btn>
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

    </div>
</template>
<script setup>
import { mdiAccount } from '@mdi/js'
</script>
<script>

import { VDateInput } from 'vuetify/labs/VDateInput'


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
        tableKey:1,
        errors:null,
        headers: [

            { title: 'ID', key: 'id' },
            { title: 'User ID', key: 'user_id' },
            { title: 'Category', key: 'category.title' },
            { title: 'Wage', key: 'wage' },
            { title: 'Description', key: 'description' },
            { title: 'Date', key: 'date' },
            { title: 'Type', key: 'type' },
            { title: 'Actions', key: 'actions', sortable: false },
        ],
        date:null,
        editedIndex: -1,
        deleteIndex:-1,
        deleteID:null,
        editedItem: {
            id: '',
            user_id: '',
            category:'',
            wage: 0,
            recurring: 0,
            description: '',
            date: '',
            cycle: '',
            type: '',
        },
        defaultItem: {
            user_id: 1,
            category:'',
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
        balanceObject(){
            return this.formTitle === 'New Balance' ? this.defaultItem : this.editedItem
        },
        tableKeyComp(){
            return this.tableKey;
        }

    },

    watch: {
        dialog (val) {
            val || this.close()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
    },

    created() {
        this.getData()
        this.getCategories()
    },
    methods: {
        async sendData(){
            try{
                const response = await axios.post('/api/balances/create', this.defaultItem);
                console.log(response);
                const newItem = response.data.data;
                this.items.push(newItem);

            }catch(error){
                this.errors = error.response.data.errors;
                console.log(error.response);
            }
            this.close();
            console.log(this.defaultItem);
        },

        async updateData(){
            try{
                const response = await axios.put(`/api/balances/${this.editedItem.id}/update`, this.editedItem);
                console.log(response.data.data);
                const newItem = response.data.data;
                const itemToUpdate = this.items.findIndex(item => item.id === response.data.data.id);
                if(itemToUpdate !== -1){
                    this.items[itemToUpdate] = newItem;
                }
            }catch(error){
                console.error(error);
            }
            this.close();
            this.tableKey++;
            console.log(this.tableKey);
        },

        async getData(){
            this.loading = true;
            console.log(this.loading);
            try{
                const response = await axios.get('/api/balances/incomes');
                console.log("Balances fetched",response.data);
                this.items = response.data.data;
                this.loading = false;
                console.log(this.loading);
            }catch(error){
                console.error("Error");
            }
        },

        deleteData(){
            axios.delete(`/api/balances/${this.deleteID}/delete`);
            this.items.splice(this.deleteIndex, 1);
            return this.closeDelete();
        },

        async getCategories(){
            const response = await axios.get('/api/categories')
            this.categories = response.data;
        },

        editItem (item) {

            console.log(item);
            this.editedIndex = 1;
            console.log(this.editedItem);
            this.editedItem = Object.assign({}, item)
            console.log(this.editedItem);
            this.dialog = true
        },

        deleteItem (item) {
            console.log(item);
            this.deleteID = item.id;
            this.deleteIndex = this.items.findIndex(arrayItem => arrayItem.id === item.id);
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


<style scoped>
.black-icon{
    color:black;
}
</style>
