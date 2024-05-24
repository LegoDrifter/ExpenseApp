<template>
    <the-navbar></the-navbar>
    <router-view v-slot="{ Component, route }">
        <div :key="route.name">
            <Component :is="Component" />
        </div>
    </router-view>
</template>


<script>
import TheNavbar from "./Components/TheNavbar.vue";

export default{
    name:'App',
    components:{
      TheNavbar,
    },
}


</script>
