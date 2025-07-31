<template>
    <div>
        <div id="selectByID">Selected by its id 'selectByID'</div>
        <p class="selectByClass">Selected by its class 'selectByClass'</p>
        <button data-step="3">Selected by the 'data-step="3"' attribute</button>

        <!-- VTour component with correct registration -->
        <VTour :steps="steps" ref="tour"/>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
const tour = ref();
const steps = [
    {
        target: '#selectByID',
        content: 'This is the first step',
    },
    {
        target: '.selectByClass',
        content: 'This is the second step, placed on the bottom of the target',
        placement: 'bottom',
    },
    {
        target: '[data-step="3"]',
        content: 'This is the third step',
    }
];

// Start the tour on mounted
onMounted(() => {
    tour.value.startTour();
});
</script>
