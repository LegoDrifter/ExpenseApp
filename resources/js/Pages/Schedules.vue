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
                    <v-toolbar-title><span class="font-oswald text-slateGray font-bold text-2xl">Schedules</span></v-toolbar-title>
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
                            <button v-bind="props" :disabled="dataIsLoaded !== 2" class="bg-white px-2 py-2 rounded-md font-bold mr-5">+ Add</button>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="flex justify-center text-2xl mt-4 font-oswald text-slateGray">{{ formTitle }}</span>

                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" md="6" sm="6">
                                            <v-row class="mb-3 flex justify-center">
                                                <v-label class="font-oswald text-slateGray">Type</v-label>
                                            </v-row>
                                            <v-btn-toggle
                                                v-model="balanceObject.type"
                                                color="dark"
                                                divided
                                            >
                                                <v-btn
                                                    size="small"
                                                    v-for="option in typeOptions"
                                                    :key="option.value"
                                                    :value="option.value"
                                                    :class="{'focus-btn': balanceObject.type === option.value, 'oswald-font': true}"
                                                >
                                                    <p class="font-bold">{{ option.text }}</p>
                                                </v-btn>
                                            </v-btn-toggle>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="6"
                                            sm="6"

                                        >
                                            <v-combobox
                                                label="Category"
                                                :disabled = "!balanceObject.type"
                                                :items="balanceObject.type === 1 ? expenseCategories : incomeCategories"
                                                v-model="balanceObject.category"
                                                class="mt-4"
                                            ></v-combobox>
                                        </v-col>
                                        <v-col
                                            v-if="balanceObject.type === 1"
                                            cols="12"
                                            md="6"
                                            sm="6"
                                        >
                                            <v-combobox
                                                label="Sub Category"
                                                :items="subCategories"
                                                v-model="balanceObject.subCategory"
                                            ></v-combobox>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="6"
                                            sm="6"
                                        >
                                            <v-text-field
                                                v-model="balanceObject.wage"
                                                label="Total"
                                                :class="{'mt-5':balanceObject.type === 2}"
                                            ></v-text-field>
                                        </v-col>
                                        <v-row>

                                        </v-row>
                                        <v-col cols="12" md="12" sm="6">
                                            <v-row class="mb-3 flex justify-center">
                                                <v-label class="font-oswald text-slateGray">Recurring</v-label>
                                            </v-row>
                                            <v-btn-toggle
                                                v-model="balanceObject.recurring"
                                                color="dark"
                                                divided
                                            >
                                                <v-btn
                                                    v-for="option in recurringOptions"
                                                    size="x-small"
                                                    :key="option.value"
                                                    :value="option.value"
                                                    :class="{'focus-btn': balanceObject.recurring === option.value}"
                                                >
                                                    <p class="font-bold">{{ option.text }}</p>
                                                </v-btn>
                                            </v-btn-toggle>
                                        </v-col>
<!--                                        <v-col-->
<!--                                            cols="12"-->
<!--                                            md="4"-->
<!--                                            sm="6"-->
<!--                                        >-->
<!--                                            <v-combobox-->
<!--                                                v-model="balanceObject.recurring"-->
<!--                                                label="Recurring"-->
<!--                                                :items="recurringOptions"-->
<!--                                                :item-title="(i)=>{return i.text}"-->
<!--                                                :item-value="(i)=>{return i.value}"-->
<!--                                            ></v-combobox>-->
<!--                                        </v-col>-->
                                        <v-row >
                                            <v-col
                                                cols="12"
                                                md="12"
                                                sm="12"
                                            >
                                                <v-text-field
                                                    v-model="balanceObject.cycle"
                                                    label="Cycle"
                                                    :class="{'mt-7':balanceObject.type === 1}"

                                                ></v-text-field>
                                            </v-col>
                                        </v-row>

                                        <v-col
                                            cols="12"
                                            md="12"
                                            sm="12"
                                        >
                                            <v-date-input  label="Date input" v-model="balanceObject.date"></v-date-input>

                                        </v-col>

                                        <v-row>
                                            <v-col
                                                cols="12"
                                                md="12"
                                                sm="12"
                                            >
                                                <v-textarea
                                                    v-model="balanceObject.description"
                                                    label="Description"
                                                ></v-textarea>

                                            </v-col>
                                        </v-row>
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
                                    @click="formTitle === 'New Schedule' ? sendData() : updateData()"
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



<script>
import { VDateInput } from 'vuetify/labs/VDateInput'
import { format } from 'date-fns';


export default {
    components: {
        VDateInput
    },
    data: () => ({
        dialog: false,
        items: null,
        categories:null,
        subCategory:null,
        expenseCategories:null,
        incomeCategories:null,
        dataIsLoaded:0,
        subCategories:null,
        loading:null,
        dialogDelete: false,
        tableKey:1,
        errors:null,
        headers: [

            { title: 'ID', key: 'id' },
            { title: 'User ID', key: 'user_id' },
            { title: 'Category', key: 'category.title' },
            { title: 'Description', key: 'description' },
            { title: 'Total Cycles', key: 'cycle' },
            { title: 'Recurring', key: 'recurring' },
            { title: 'Remaining Cycles', key: 'cycles_left' },
            { title: 'Type', key: 'type' },
            { title: 'Date', key: 'date' },
            { title: 'Actions', key: 'actions', sortable: false },
        ],
        date:null,
        editedIndex: -1,
        deleteIndex:-1,
        deleteID:null,
        recurringOptions: [
            { text: 'Days', value: 1 },
            { text: 'Months', value: 2 },
            { text: 'Years', value: 3 },
        ],
        typeOptions:[
            {text:'Income', value:2},
            {text:'Expense', value:1}
        ],
        editedItem: {
            id: '',
            user_id: '',
            wage: 0,
            recurring: 1,
            description: '',
            date: '',
            cycle: '',
            type: 1,
        },
        defaultItem: {
            user_id: 1,
            description:'',
            wage: 0,
            type:1,
            recurring: 1,
            date: null,
            cycle: '',

        },
    }),

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'New Schedule' : 'Edit Schedule'
        },
        balanceObject(){
            return this.formTitle === 'New Schedule' ? this.defaultItem : this.editedItem
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

        resetItem(){
            this.defaultItem = {
                user_id: 1,
                description:'',
                wage: 0,
                type:0,
                recurring: 0,
                date: null,
                cycle: '',
            };
        },
        getTodayDate() {
            // const today = new Date();
            // const day = String(today.getDate()).padStart(2, '0');
            // const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            // const year = today.getFullYear();
            // return `${day}/${month}/${year}`;
            return new Date();
        },
        mapRecurring(value) {
            const recurringMap = {
                1: 'Days',
                2: 'Months',
                3: 'Years',
                // Add other mappings as needed
            };
            return recurringMap[value] || 'Unknown';
        },
        mapType(value) {
            const typeMap = {
                2: 'Income',
                1: 'Expense',
                // Add other mappings as needed
            };
            return typeMap[value] || 'Unknown';
        },

        addStringProperties(item) {
            return {
                ...item,
                recurringString: this.mapRecurring(item.recurring),
                typeString: this.mapType(item.type)
            };
        },

        async sendData(){
            try{
                console.log(this.defaultItem.recurring);
                this.defaultItem.date = format(new Date(this.defaultItem.date),'yyyy-MM-dd');
                const response = await axios.post('/api/schedules/create', this.defaultItem);
                console.log(response);
                const newItem = response.data.data;
                this.items.push(newItem);

            }catch(error){
                this.errors = error.response.data.errors;
                console.log(error.response);
            }
            this.close();
            this.resetItem();
            console.log(this.defaultItem);
        },


        getRec(arg){
            if(arg === 'Day'){
                return 1;
            }
            if(arg === 'Month'){
                return 2;
            }
            if(arg === 'Year')
            {
                return 3;
            }
        },

        getType(arg){
            if(arg === 'Income'){
                return 2;
            }
            if(arg === 'Expense'){
                return 1;
            }
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
                // if(this.editedItem.recurring){
                //     this.editedItem.recurring = this.getRec(this.editedItem.recurring);
                // }
                // if(this.editedItem.type){
                //     this.editedItem.type = this.getType(this.editedItem.type);
                // }
                const response = await axios.put(`/api/schedules/${this.editedItem.id}/update`, this.editedItem);
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
                const response = await axios.get('/api/schedules');
                console.log("Schedules fetched",response.data);
                this.items = response.data.data.map(this.addStringProperties);
                this.defaultItem.date = this.getTodayDate();
                console.log(this.items)
                this.loading = false;
                this.dataIsLoaded+=1;
            }catch(error){
                console.error("Error");
            }
        },

        deleteData(){
            axios.delete(`/api/schedules/${this.deleteID}/delete`);
            this.items.splice(this.deleteIndex, 1);
            return this.closeDelete();
        },

        async getCategories(){
            const response = await axios.get('/api/categories')
            const response2 = await axios.get('/api/categories/sub')
            this.subCategories = response2.data;
            this.categories = response.data;
            this.incomeCategories = this.categories.filter((item) => {
                return item.title === 'Income';
            });
            this.expenseCategories = this.categories.filter((item) => {
                return item.title !== 'Income';
            });
            this.dataIsLoaded+=1;
        },

        editItem (item) {

            console.log(item);
            this.editedIndex = 1;
            this.editedItem = Object.assign({}, item)
            if(typeof this.balanceObject.type === 'string'){
                this.balanceObject.type = this.getType(this.balanceObject.type);
            }
            if(typeof this.balanceObject.recurring === 'string'){
                this.balanceObject.recurring = this.getRec(this.balanceObject.recurring);
            }
            if (typeof this.balanceObject.date === 'string') {
                this.balanceObject.date = new Date(this.balanceObject.date);
            }
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


    },
}
</script>


<style scoped>

</style>
