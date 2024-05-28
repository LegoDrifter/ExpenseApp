<template>
    <table class="min-w-full divide-y divide-gray-200 max-w-lg mx-auto">
        <thead class="bg-gray-50">
        <tr>

                <th v-for="header in headers" :key="header.id" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ header }}
                </th>

            <!-- Add more header columns if needed -->
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(item,index) in items" :key="item.id" :class="{'bg-gray-100': index % 2 !== 0 }">
            <td v-for="field in fields" class="px-6 py-4 flex-wrap">
                <div class="text-sm font-medium text-gray-900" v-if="field === 'type'" >
                    {{ item[field] == 1 ? 'Income'  : 'Expense'}}
                </div>

                <div class="text-sm font-medium text-gray-900" v-else >{{ item[field] }}</div>
            </td>
            <td class="flex gap-1 justify-items-center my-5 mx-2">
                <button @click="sendID(item['id'],'EDIT')" class="bg-yellow-500 px-2 py-2 text-white rounded-md text-xs">Edit</button>
                <button @click="sendID(item['id'],'DELETE')" class="bg-red-600 px-2 py-2 text-white rounded-md text-xs">Delete</button>
            </td>


            <!-- Add more table cells if needed -->
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: {
        headers: {
            type: Array,
            required: true
        },
        fields: {
            type: Array,
            required: true
        },
        items: {
            type: Array,
            required: true
        }
    },
    setup(props,{emit}){
        function sendID(arg,action){
            emit('getID',arg,action);
        }

        return{
            sendID
        }
    }
};
</script>
