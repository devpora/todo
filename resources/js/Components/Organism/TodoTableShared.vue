<script setup>
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableRow
} from 'flowbite-vue'
import ModalShowTodo from "@/Components/Organism/ModalShowTodo.vue";

const props = defineProps({
    todos: {
        type: Array,
        required: true,
    },
});
</script>
<template>
    <FwbTable striped>
        <FwbTableBody>
            <template v-for="todo in todos" :key="todo.id">
                <FwbTableRow>
                    <FwbTableCell class="w-12">
                        <ModalShowTodo :todo="todo" />
                    </FwbTableCell>
                    <FwbTableCell class="!text-left">{{todo.name}}</FwbTableCell>
                </FwbTableRow>
            </template>
        </FwbTableBody>
    </FwbTable>
</template>
