<script setup>
import {inject, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
    FwbAvatar,
    FwbButton, FwbCheckbox, FwbBadge
} from 'flowbite-vue'
import ModalEditTodo from "@/Components/Organism/ModalEditTodo.vue";
import ModalDeleteTodo from "@/Components/Organism/ModalDeleteTodo.vue";
const showToast = inject('showToast');

const props = defineProps({
    todos: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const expandedRows = ref({});
const loading = ref(false);

const toggleRow = (todoId) => {
    expandedRows.value[todoId] = !expandedRows.value[todoId];
};

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
</script>
<template>
    <FwbTable striped>
        <FwbTableHead>
            <FwbTableHeadCell class="w-12"></FwbTableHeadCell>
            <FwbTableHeadCell class="w-24">Completed</FwbTableHeadCell>
            <FwbTableHeadCell>Name</FwbTableHeadCell>
            <FwbTableHeadCell>Category</FwbTableHeadCell>
            <FwbTableHeadCell>Shared</FwbTableHeadCell>
            <FwbTableHeadCell class="w-24">Action</FwbTableHeadCell>
        </FwbTableHead>
        <FwbTableBody>
            <template v-for="todo in todos" :key="todo.id">
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
                        <ModalDeleteTodo :todo="todo" />
                        <ModalEditTodo :todo="todo" :categories="categories"/>
                    </FwbTableCell>
                </FwbTableRow>
                <FwbTableRow v-if="expandedRows[todo.id]">
                    <FwbTableCell colspan="6">
                        <p class="text-left">{{ todo.description || 'No description available.' }}</p>
                    </FwbTableCell>
                </FwbTableRow>
            </template>

        </FwbTableBody>
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
            <div class="loader">Processing...</div>
        </div>
    </FwbTable>
</template>
