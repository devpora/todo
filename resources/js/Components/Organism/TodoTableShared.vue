<script setup>
import {inject, ref, onMounted, watch} from "vue";
import axios from 'axios';
const showToast = inject('showToast');

import {
    FwbPagination,
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableRow
} from 'flowbite-vue';
import ModalShowTodo from "@/Components/Organism/ModalShowTodo.vue";
import AtomTableEmpty from "@/Components/atoms/AtomTableEmpty.vue";
import AtomTableProcessing from "@/Components/atoms/AtomTableProcessing.vue";
const props = defineProps({
    tableRefresh: {
        type: Number,
        required: true,
    },
});
const todos = ref({
    data: [],
    meta: {
        total: 0,
    },
    links: {}
});
const loadingTable = ref(false);
const currentPage = ref(1);

const loadData = (page = 1) => {
    loadingTable.value = true;

    axios.get(`/todo-shared?page=${page}`)
        .then((response) => {
            todos.value = response.data;
        })
        .catch(() => {
            showToast('error', 'Error in loadingTable Shared data');
        })
        .finally(() => {
            loadingTable.value = false;
        });
};

watch(currentPage, (newPage, oldPage) => {
    if (newPage !== oldPage) {
        loadData(newPage);
    }
});
watch(props, () => {
    loadData();
}, {
    immediate:true
});
onMounted(() => {
    loadData();
});
</script>

<template>
    <FwbTable striped class="border">
        <AtomTableProcessing v-if="loadingTable"/>
        <FwbTableBody>
            <template v-if="todos.data && todos.data?.length > 0">
                <FwbTableRow v-for="todo in todos.data" :key="todo.id">
                    <FwbTableCell class="w-12">
                        <ModalShowTodo :todo="todo" />
                    </FwbTableCell>
                    <FwbTableCell class="!text-left">{{ todo.name }}</FwbTableCell>
                </FwbTableRow>
            </template>
             <AtomTableEmpty v-else/>
        </FwbTableBody>
    </FwbTable>
    <div class="flex justify-center pt-6">
        <FwbPagination v-if="todos.meta && todos.meta.total > 5" v-model="currentPage" :total-items="todos.meta.total" :per-page="todos.meta.per_page"></FwbPagination>
    </div>
</template>
