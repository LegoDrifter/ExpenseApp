<template>
    <transition       enter-active-class="transition-transform duration-1000"
                      enter-from-class="transform -translate-x-full"
                      enter-to-class="transform translate-x-0"
                      leave-active-class="transition-opacity duration-500"
                      leave-from-class="opacity-100"
                      leave-to-class="opacity-0">
        <div v-if="show" class="fixed inset-0  bg-opacity-50 flex items-center justify-center rounded-lg">
            <div class="bg-white relative p-6 rounded shadow-2xl max-w-lg lg:max-w-4xl w-full">
                <button @click="close" class="absolute top-2 left-2 bg-slateGray text-white px-2 py-1 rounded-full">X</button>
                <slot></slot>
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
.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>
