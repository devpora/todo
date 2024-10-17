<script setup>
import {inject, ref} from "vue";
import {router} from "@inertiajs/vue3";
const showToast = inject('showToast');
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableRow,
    FwbCheckbox
} from 'flowbite-vue'

const props = defineProps({
    todos: {
        type: Array,
        required: true,
    },
});
const loading = ref(false);

const onChangeCompleted = (todo) => {
    loading.value = true;

    router.post(`/todo-completed/${todo.id}`, {
        completed: todo.is_completed,
    },{
        onSuccess: () => {
            showToast('success', 'Success');
        },
        onError: () => {
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
                    <FwbTableCell class="w-12">
                        <FwbCheckbox v-model="todo.is_completed" @change="() => onChangeCompleted(todo)"/>
                    </FwbTableCell>
                    <FwbTableCell class="!text-left">{{todo.name}}</FwbTableCell>
                </FwbTableRow>
            </template>
        </FwbTableBody>
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
            <div class="loader">Processing...</div>
        </div>
    </FwbTable>
</template>
