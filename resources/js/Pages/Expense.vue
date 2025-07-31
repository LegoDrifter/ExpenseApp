<template>
    <div>
        <div v-if="loading" class="flex justify-center my-40" >
            <v-progress-circular indeterminate :size="120" color="slateGray"></v-progress-circular>
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
                    <v-toolbar-title><span class="font-oswald text-slateGray  font-bold text-2xl">Expenses</span></v-toolbar-title>
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
                               <button v-bind="props" :disabled="dataLoaded !== 2" class="bg-white px-2 py-1 rounded-md font-bold mr-5">+ Add</button>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="flex justify-center text-2xl mt-4 font-oswald text-slateGray">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col
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
                                            cols="12"
                                            md="6"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="balanceObject.wage"
                                                label="Wage"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col
                                            cols="10"
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
                                        <div v-if="errors" class="ml-3">
                                            <p v-for="error in errors" class="text-red-500">
                                                {{error}}
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
        todayDate:null,
        categories:null,
        loading:null,
        dialogDelete: false,
        tableKey:1,
        errorFlag:false,
        errors:null,
        headers: [
            // { title: 'ID', key: 'id' },
            // { title: 'User ID', key: 'user_id' },
            { title: 'Category', key: 'category.title' },
            { title: 'Wage', key: 'wage' },
            {title:'Sub Category', key:'sub_category.title'},
            { title: 'Description', key: 'description' },
            { title: 'Date', key: 'date' },
            { title: 'Actions', key: 'actions', sortable: false },
        ],
        date:null,
        editedIndex: -1,
        deleteIndex:-1,
        subCategories:null,
        dataLoaded:0,
        deleteID:null,
        editedItem: {
            id: '',
            user_id: '',
            category:null,
            sub_category:null,
            wage: 0,
            description: '',
            date: '',
            type: 1,
        },
        defaultItem: {
            user_id: 1,
            category:null,
            sub_category:null,
            wage: 0,
            description: '',
            date: '',
            type:1
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
        },
        dateToday(){
            return this.getTodayDate();
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
                const date = new Date(this.defaultItem.date);
                this.defaultItem.date = date.toLocaleDateString('en-CA');
                const response = await axios.post('/api/balances/create', this.defaultItem);
                console.log(response);
                const newItem = response.data.data;
                this.items.push(newItem);
                this.errorFlag = false;
                this.errors = null;
                this.resetForm();

            }catch(error){
                this.errors = error.response.data.errors;
                this.errorFlag = true;
                console.log(error.response);
            }
            if(this.errorFlag === false){
                this.close();

            }
        },


        resetForm(){
            this.defaultItem.category = null;
            this.defaultItem.subCategory = null;
            this.defaultItem.wage = 0;
            this.defaultItem.date = null;
            this.defaultItem.type = 1;
            this.defaultItem.description = '';
        },

        formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        },

        async updateData(){
            try{
                if (this.editedItem.date) {
                    this.editedItem.date = this.formatDate(this.editedItem.date);
                }
                // const date = new Date(this.editedItem.date);
                // this.editedItem.date = date.toLocaleDateString('en-CA');
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
            try{
                const response = await axios.get('/api/balances/expenses');
                console.log("Balances fetched",response.data);
                this.items = response.data.data;
                this.defaultItem.date =  this.getTodayDate();
                this.loading = false;
                this.dataLoaded += 1;
            }catch(error){
                console.error("Error");
            }
        },
        getTodayDate() {
            return new Date();
    // const today = new Date();
    // const day = String(today.getDate()).padStart(2, '0');
    // const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    // const year = today.getFullYear();
    // return `${day}/${month}/${year}`;
},

        deleteData(){
            axios.delete(`/api/balances/${this.deleteID}/delete`);
            this.items.splice(this.deleteIndex, 1);
            return this.closeDelete();
        },

        async getCategories(){
            const response = await axios.get('/api/categories')
            const response2 = await axios.get('/api/categories/exp')

            this.subCategories = response2.data;
            this.categories = response.data;
            this.categories = this.categories.filter((item) => {
                return item.title !== 'Income';
            })
            this.dataLoaded+=1;
        },

        editItem (item) {

            console.log(item);
            this.editedIndex = 1;
            console.log(this.editedItem);
            this.editedItem = Object.assign({}, item)
            if (typeof this.balanceObject.date === 'string') {
                this.balanceObject.date = new Date(this.balanceObject.date);
            }
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
