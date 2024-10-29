<script setup>
import {inject, onMounted, ref, watch} from "vue";
const showToast = inject('showToast');
import {
    FwbPagination,
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableRow,
    FwbCheckbox
} from 'flowbite-vue'
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
const loadingTable = ref(false);
const currentPage = ref(1);

const onChangeCompleted = (todo) => {
    loadingTable.value = true;

    axios.post(`/todo-completed/${todo.id}`, {completed: !todo.is_completed})
        .then(() => {
            loadData()
            emit('loadTableActive')
        })
        .catch((error) => {
            showToast('error', error.message);
        })
        .finally(() => {
            loadingTable.value = false;

        });
};
const loadData = (page = 1) => {
    loadingTable.value = true;

    axios.get(`/todo-completed?page=${page}`)
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
            <template v-for="todo in todos.data" v-if="todos.data && todos.data?.length > 0" :key="todo.id">
                <FwbTableRow>
                    <FwbTableCell class="w-12">
                        <FwbCheckbox v-model="todo.is_completed" @change="() => onChangeCompleted(todo)"/>
                    </FwbTableCell>
                    <FwbTableCell class="!text-left">{{todo.name}}</FwbTableCell>
                </FwbTableRow>
            </template>
            <AtomTableEmpty v-else/>
        </FwbTableBody>
    </FwbTable>
    <div class="flex justify-center pt-6">
        <FwbPagination v-if="todos.meta && todos.meta.total > 5" v-model="currentPage" :total-items="todos.meta.total" :per-page="todos.meta.per_page"></FwbPagination>
    </div>
</template>
