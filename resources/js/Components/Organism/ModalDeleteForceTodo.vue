<script lang="ts" setup>
import { inject, ref } from 'vue'
import { FwbButton, FwbModal } from 'flowbite-vue'
import {router} from "@inertiajs/vue3";
const showToast = inject('showToast');
const emit = defineEmits(['loadTable']);

const isShowModal = ref(false)

function closeModal () {
    isShowModal.value = false
}
function showModal () {
    isShowModal.value = true
}

defineProps({
    todo: {
        type: Object,
        required: true,
    },
});

const loading = ref(false)
function destroyTodo (id) {
    loading.value = true
    router.delete(`/todo-force/${id}`, {
        onSuccess: () => {
            closeModal();
            showToast('success', 'Todo was permanently deleted');
            emit('loadTable');
        },
        onError: (error) => {
            showToast('error', error.message);
        },
        onFinish: () => {
            loading.value = false;
        }
    });
}
</script>

<template>
    <svg color="red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hover:scale-125 hover:cursor-pointer" @click="showModal">
        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
    </svg>

    <FwbModal v-if="isShowModal" @close="closeModal">
        <template #header>
            <div class="flex items-center text-lg">
                Permanent Delete Todo
            </div>
        </template>
        <template #body>
            <div class="text-center text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <p class="pb-8">
                    Are you sure you want to permanently delete this todo item?
                </p>
                <p class="pb-8">
                    <strong>{{ todo.name }}</strong>
                </p>
                <p class="pb-8 text-red-600 font-bold">
                    This action is irreversible, and the item cannot be restored.
                </p>
            </div>

        </template>
        <template #footer>
            <div class="flex justify-between">
                <FwbButton color="alternative" @click="closeModal">
                    Cancel
                </FwbButton>
                <FwbButton :disabled="loading" color="red" @click="destroyTodo(todo.id)">
                    <span v-if="loading">Deleting...</span>
                    <span v-else>Delete</span>
                </FwbButton>
            </div>
        </template>
    </FwbModal>
</template>
