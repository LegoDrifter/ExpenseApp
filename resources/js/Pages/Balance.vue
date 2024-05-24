<template>
    <div>
        <h1 class="text-3xl font-bold text-center my-5">[Balances]</h1>
        <Table :headers="tableHeaders" :fields="tableFields" :items="balances" />
        <div class="mt-5">
            <div class="flex justify-center gap-4">
                <input  type="text" class="input-field" placeholder="Category">
                <input  type="text" class="input-field" placeholder="Title">
                <input  type="text" class="input-field" placeholder="Description">
                <input  type="date" class="input-field" placeholder="Start Date">
                <input  type="number" class="input-field" placeholder="Price">
                <input  type="number" class="input-field" placeholder="Initial Budget">
                <button @click="addGoal" class="btn-add bg-indigo-600 text-white px-2 py-1 rounded-md">Add Goal</button>
            </div>
        </div>
    </div>
</template>

<script>
import Table from "../Components/TheTable.vue"; // Adjust the import path as needed
import {balances} from "./balances.js"
export default {
    components: {
        Table
    },
    data() {
        return {
            tableHeaders: ["User ID", "Category ID", "Wage", "Recurring", "Description", "Date", "Cycle", "Type"],
            tableFields: ["user_id", "category_id", "wage", "recurring", "description", "date","cycle","type"],
            balances: balances,
        };
    }
};
</script>
