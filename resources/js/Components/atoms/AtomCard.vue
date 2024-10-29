<script setup>
const emit = defineEmits(['clicked']);
const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 pb-4">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-4 text-gray-900 dark:text-gray-100">
                <slot></slot>
            </div>
        </div>
    </div>
</template>
