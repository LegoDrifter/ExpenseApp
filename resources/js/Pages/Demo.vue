<template>
    <div class="flex flex-col max-w-lg mx-auto border-gray-800   rounded-lg overflow-hidden md:flex-row md:max-w-6xl md:h-screen md:mt-12 ">
        <!-- Background image div -->
        <div class="relative w-full h-64 bg-left-bottom md:bg-center md:h-[600px] md:w-[500px] rounded-md" style="background-image: url('https://images.pexels.com/photos/2773528/pexels-photo-2773528.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500');">
            <h1 class="absolute bottom-4 left-4 text-3xl font-bold text-white">Expense Tracker</h1>
        </div>
        <div class="ml-0.5 md:ml-1">
            <TheDemo class="min-h-[600px] w-[500px]" />
        </div>
    </div>
</template>

<script setup>

import TheDemo from "@/Components/TheDemo.vue";
</script>

<style scoped>
</style>
