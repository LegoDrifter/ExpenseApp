<template>

<div class="text-center mt-10"><h1 class="text-3xl uppercase">Register</h1></div>

<form method="POST" action="/api/register" class="max-w-xl  mx-auto bg-gray-800 mt-15 p-5 rounded-md">
    <div class="flex flex-col gap-1">
        <label class="text-white uppercase">Email</label>
        <input placeholder="Enter email..." type="email" name="email" class="bg-white p-1 mb-2 rounded-sm pl-2">
    </div>
    <div class="flex flex-col gap-1">
        <label class="text-white uppercase">Username</label>
        <input placeholder="Enter username..." type="text" name="username" class="bg-white p-1 mb-2 rounded-sm pl-2">
    </div>
    <div class="flex flex-col gap-1">
        <label class="text-white uppercase">Password</label>
        <input placeholder="Enter password..." type="password" name="password" class="bg-white p-1 mb-2 rounded-sm pl-2">
    </div>
    <div class="flex justify-end mt-6">
        <button type="submit" class="bg-white py-1 px-2 rounded-md hover:bg-black text-gray-800">REGISTER</button>
    </div>
</form>

</template>


<script setup>

</script>

<style scoped>

</style>
