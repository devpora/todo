<script setup>
import {inject, ref} from "vue";
import { formatDate } from '@/utils/date';
import { router } from "@inertiajs/vue3";
const showToast = inject('showToast');
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableRow,
} from 'flowbite-vue'

const props = defineProps({
    todos: {
        type: Array,
        required: true,
    },
});
const loading = ref(false);

const restore = (todo) => {
    loading.value = true
    router.post(`/todo-restore/${todo.id}`, {},{
        onSuccess: () => {
            showToast('success', 'Success');
        },
        onError: (errors) => {
            showToast('error', 'Error');
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};
</script>
<template>
    <FwbTable striped>
        <FwbTableBody>
            <template v-for="todo in todos" :key="todo.id">
                <FwbTableRow>
                    <FwbTableCell>
                        <svg
                            @click="() => restore(todo)"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hover:scale-125 hover:cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                    </FwbTableCell>
                    <FwbTableCell>{{todo.name}}</FwbTableCell>
                    <FwbTableCell class="w-24 text-xs !p-2">{{formatDate(todo.deleted_at)}}</FwbTableCell>
                </FwbTableRow>
            </template>
        </FwbTableBody>
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
            <div class="loader">Processing...</div>
        </div>
    </FwbTable>
</template>
