<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center rounded-lg">
            <div class="bg-white p-6 rounded shadow-lg max-w-lg w-full">
                <slot></slot>
                <button @click="close" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </transition>
</template>

<script>

export default{
    props:{
      show:{
          type:Boolean,
          required: true,
      }
    },
    setup(props, {emit}){
        function close(){
            emit('close');
        }

        return{
            close,
        }
    }
}

</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>
