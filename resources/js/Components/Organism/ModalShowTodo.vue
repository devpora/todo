<script lang="ts" setup>
import { ref } from 'vue'
import { FwbButton, FwbModal } from 'flowbite-vue'

const isShowModal = ref(false)

function closeModal () {
    isShowModal.value = false
}
function showModal () {
    isShowModal.value = true
}

const props = defineProps({
    todo: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <svg @click="showModal" color="blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hover:cursor-pointer">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>

    <fwb-modal v-if="isShowModal" @close="closeModal">
        <template #header>
            <div class="flex items-center text-lg">
                Show Todo
            </div>
        </template>
        <template #body>
            <div class="text-center text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <div v-if="todo">
                    <div class="mb-4">
                        <span class="text-2xl font-bold mb-2">Title</span>
                        <p class="text-lg">{{ todo.name }}</p>
                    </div>
                    <div class="mb-4">
                        <span class="text-2xl font-bold mb-2">Description</span>
                        <p class="text-lg">{{ todo.description }}</p>
                    </div>
                </div>
            </div>

        </template>
        <template #footer>
            <div class="flex justify-between">
                <fwb-button @click="closeModal" color="alternative">
                    Close
                </fwb-button>
                <fwb-button :href="`/shared/private/${todo.sharedLink}`" target="_blank">
                    Link
                </fwb-button>
            </div>
        </template>
    </fwb-modal>
</template>
