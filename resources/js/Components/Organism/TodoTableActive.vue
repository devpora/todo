<script setup>
import {inject, onMounted, ref, watch} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
    FwbAvatar,
    FwbButton, FwbCheckbox, FwbBadge, FwbPagination, FwbInput
} from 'flowbite-vue'
import ModalEditTodo from "@/Components/Organism/ModalEditTodo.vue";
import ModalDeleteTodo from "@/Components/Organism/ModalDeleteTodo.vue";
import axios from "axios";
import AtomTableProcessing from "@/Components/atoms/AtomTableProcessing.vue";
import AtomTableEmpty from "@/Components/atoms/AtomTableEmpty.vue";
const showToast = inject('showToast');

const emit = defineEmits(['loadTableDeleted', 'loadTableUpdated', 'loadTableCompleted']);
const props = defineProps({
    tableRefresh: {
        type: Number,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const expandedRows = ref({});
const todos = ref({
    data: [],
    meta: {
        total: 0,
    },
    links: {}
});

const toggleRow = (todoId) => {
    expandedRows.value[todoId] = !expandedRows.value[todoId];
};

const onChangeCompleted = (todo) => {
    loadingTable.value = true;

    router.post(`/todo-completed/${todo.id}`, {
        completed: todo.is_completed,
    },{
        onSuccess: () => {
            showToast('success', 'Todo was completed');
            emit('loadTableCompleted')
        },
        onError: () => {
            showToast('error', 'Error');
        },
        onFinish: () => {
            loadingTable.value = false;
        }
    });
};

const copyLink = async (isPublic, sharedLink) => {
    const url = import.meta.env.VITE_BASE_URL;
    let linkType = '/shared'
    if(isPublic){
        linkType += '/public/'
    }else{
        linkType += '/private/'
    }
    await navigator.clipboard.writeText(url + linkType + sharedLink);
    showToast('success', 'Copied');
};

const loadingTable = ref(false);
const currentPage = ref(1);

const loadData = (page = 1) => {
    loadingTable.value = true;

    axios.get(`/todo-active?page=${page}`)
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

const filterForm = useForm({
    filter: '',
});
const applyFilter = () => {
    loadingTable.value = true;
    const filters = filterForm.filter.split(' ');
    const params = {};

    filters.forEach(filter => {
        const [key, value] = filter.split('=');
        params[key] = value;
    });
    const queryString = new URLSearchParams(params).toString();
    axios.get(`/todo-active?page=${currentPage.value}&${queryString}`)
        .then((response) => {
            todos.value = response.data
        })
        .catch((error) => {
            showToast('error', error.message);
        })
        .finally(() => {
            // console.log('finnaly')
            loadingTable.value = false;
        });
};
const clearFilter = () => {
    filterForm.filter = ''
    applyFilter()
}
const handleEnter = (event) => {
    if (event.key === 'Enter') {
        applyFilter();
    }
};


const deleted = () => {
    loadData()
    emit('loadTableDeleted')
};
const updated = () => {
    loadData()
    emit('loadTableUpdated')
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
    <FwbInput
        v-model="filterForm.filter"
        placeholder="eg: category=Work,Shopping shared=true name=test decription=text"
        size="lg"
        @keydown="handleEnter"
    >
        <template #suffix>
            <div class="grid grid-cols-2 gap-2">
                <FwbButton :disabled="filterForm.processing" @click="applyFilter">
                    <span v-if="filterForm.processing">Searching...</span>
                    <span v-else>Search</span>
                </FwbButton>
                <FwbButton :disabled="filterForm.processing" @click="clearFilter" class="bg-red-400 hover:bg-red-600">
                    <span>Clear</span>
                </FwbButton>
            </div>

        </template>
    </FwbInput>

    <FwbTable striped class="border mt-4">
        <FwbTableHead>
            <FwbTableHeadCell class="w-12"></FwbTableHeadCell>
            <FwbTableHeadCell class="w-24">Completed</FwbTableHeadCell>
            <FwbTableHeadCell>Name</FwbTableHeadCell>
            <FwbTableHeadCell>Category</FwbTableHeadCell>
            <FwbTableHeadCell>Shared</FwbTableHeadCell>
            <FwbTableHeadCell class="w-24">Action</FwbTableHeadCell>
        </FwbTableHead>
        <FwbTableBody>
            <AtomTableProcessing v-if="loadingTable"/>
            <template v-if="todos.data && todos.data?.length > 0" v-for="todo in todos.data" :key="todo.id">
                <FwbTableRow>
                    <FwbTableCell>
                        <FwbButton size="xs" @click="toggleRow(todo.id)" class="bg-gray-400 hover:bg-gray-500">
                            {{ expandedRows[todo.id] ? '-' : '+' }}
                        </FwbButton>
                    </FwbTableCell>
                    <FwbTableCell>
                        <FwbCheckbox v-model="todo.is_completed" @change="() => onChangeCompleted(todo)"/>
                    </FwbTableCell>
                    <FwbTableCell>{{todo.name}}</FwbTableCell>
                    <FwbTableCell class="flex flex-row gap-2">
                        <span v-if="todo.categories?.length === 0"> - </span >
                        <FwbBadge v-for="category in todo.categories">{{ category.name }}</FwbBadge>
                    </FwbTableCell>
                    <FwbTableCell>
                    <span v-if="todo.isShared" @click="copyLink(todo.isPublic, todo.sharedLink)" class="hover:cursor-pointer">
                        {{ todo.isPublic ? 'Public Link' : 'Private Link' }}
                    </span>
                        <div v-if="todo.isShared && !todo.isPublic">
                            <div class="flex -space-x-4">
                                <FwbAvatar
                                    v-for="item in todo.emails"
                                    :initials="item.email.substring(0, 2).toUpperCase()" rounded stacked class="rounded-full border-2 border-green-400"/>

                                <FwbAvatar
                                    v-if="todo.userCounter"
                                    :initials="`+ ${todo.userCounter}`"
                                    rounded
                                    class="rounded-full border-2 border-orange-400"/>
                            </div>

                        </div>
                        <span v-if="!todo.isShared">-</span>
                    </FwbTableCell>
                    <FwbTableCell class="flex gap-2">
                        <ModalDeleteTodo :todo="todo" @deleted="deleted"/>
                        <ModalEditTodo :todo="todo" :categories="categories" @updated="updated"/>
                    </FwbTableCell>
                </FwbTableRow>
                <FwbTableRow v-if="expandedRows[todo.id]">
                    <FwbTableCell colspan="6">
                        <p class="text-left">{{ todo.description || 'No description available.' }}</p>
                    </FwbTableCell>
                </FwbTableRow>
            </template>
            <template v-else>
                <td colspan="6">
                    <AtomTableEmpty />
                </td>
            </template>
        </FwbTableBody>
    </FwbTable>
    <div class="flex justify-center pt-6">
        <FwbPagination v-if="todos.meta && todos.meta.total > 5" v-model="currentPage" :total-items="todos.meta.total" :per-page="todos.meta.per_page"></FwbPagination>
    </div>
</template>
