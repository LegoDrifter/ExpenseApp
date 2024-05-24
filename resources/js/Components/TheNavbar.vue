<template>
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <router-link to="/" class="text-white text-lg font-bold">ExpenseTracker</router-link>
                <div class="ml-6 space-x-4">
                    <router-link to="/goals" class="text-gray-300 hover:text-white">Goals</router-link>
                    <router-link to="/balances" class="text-gray-300 hover:text-white">Balance</router-link>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <img src="https://via.placeholder.com/30" alt="User Icon" class="rounded-full w-8 h-8">
                <span class="text-gray-300">John Doe</span>
            </div>
        </div>
    </nav>
</template>

<script>
export default{
    name:'Navbar',
}
</script>
