<script setup>
import {inject, onMounted, ref, watch} from "vue";
const showToast = inject('showToast');
import {
    FwbPagination,
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableRow,
} from 'flowbite-vue'
import ModalDeleteForceTodo from "@/Components/Organism/ModalDeleteForceTodo.vue";
import ActionImage from "@/Components/atoms/AtomSpin.vue";
import AtomTableProcessing from "@/Components/atoms/AtomTableProcessing.vue";
import AtomTableEmpty from "@/Components/atoms/AtomTableEmpty.vue";
const emit = defineEmits(['loadTableActive']);
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
const loadingTable = ref(true);
const currentPage = ref(1);

const restore = (todo) => {
    todo.loading = true
    axios.post(`/todo-restore/${todo.id}`, {})
        .then(() => {
            loadData()
        })
        .catch((error) => {
            showToast('error', error.message);
        })
        .finally(() => {
            todo.loading = false;
            emit('loadTableActive')
        });
};
const loadData = (page = 1) => {
    loadingTable.value = true;

    axios.get(`/todo-deleted?page=${page}`)
        .then((response) => {
            todos.value = response.data;
        })
        .catch((error) => {
            showToast('error', error.message);
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
            <template v-if="todos.data && todos.data?.length > 0" v-for="todo in todos.data" :key="todo.id">
                <FwbTableRow>
                    <FwbTableCell class="w-12">
                        <ActionImage :loading="todo.loading" @clicked="restore(todo)">
                            <svg
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </ActionImage>
                    </FwbTableCell>
                    <FwbTableCell class="!text-left">{{todo.name}}</FwbTableCell>
                    <FwbTableCell class="w-24 text-xs !p-2">{{todo.deleted_at}}</FwbTableCell>
                    <FwbTableCell class="w-12 text-xs !text-end">
                        <ModalDeleteForceTodo @loadTable="loadData" :todo="todo" />
                    </FwbTableCell>
                </FwbTableRow>
            </template>
             <AtomTableEmpty v-else/>
        </FwbTableBody>
    </FwbTable>
    <div class="flex justify-center pt-6">
        <FwbPagination v-if="todos.meta && todos.meta.total > 5" v-model="currentPage" :total-items="todos.meta.total" :per-page="todos.meta.per_page"></FwbPagination>
    </div>
</template>
