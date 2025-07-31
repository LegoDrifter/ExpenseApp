<template>
    <the-navbar v-if="showNavbar"></the-navbar>
    <router-view v-slot="{ Component, route }">
        <transition name="fade" mode="out-in">
        <div :key="route.name">
            <Component :is="Component" />
        </div>
        </transition>
    </router-view>

</template>


<script>
import TheNavbar from "./Components/TheNavbar.vue";
import {computed} from "vue";
import {useRoute} from "vue-router";

export default{
    name:'App',
    components:{
      TheNavbar,
    },
    setup() {
        const route = useRoute();

        // Compute whether to show navbar based on current route
        const showNavbar = computed(() => {
            return route.path !== '/demo';
        });
        return {
            showNavbar,
        };
    },
}


</script>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>
