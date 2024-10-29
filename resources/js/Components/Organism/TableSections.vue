<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
});
defineEmits('clicked')
</script>
<template>
    <div class="mb-6">
        <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="font-bold text-xl pb-4">{{title}}</h2>
                <slot id="table" @clicked="emit('clicked')"></slot>
            </div>
        </div>
    </div>
</template>
