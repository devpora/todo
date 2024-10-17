<script setup>
const model = defineModel({
    type: String,
    required: false ,
});
const props = defineProps({
    rows: {
        type: Number,
        default: 3,
    }
})
</script>

<template>
    <textarea
        v-model="model"
        :rows="rows"
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
    ></textarea>
</template>
