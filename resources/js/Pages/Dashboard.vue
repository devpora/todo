<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TodoTableActive from "@/Components/Organism/TodoTableActive.vue";
import TodoTableCompleted from "@/Components/Organism/TodoTableCompleted.vue";
import TodoTableDeleted from "@/Components/Organism/TodoTableDeleted.vue";
import TodoTableShared from "@/Components/Organism/TodoTableShared.vue";
import {FwbButton,FwbInput} from "flowbite-vue";
import TodoQuickCreateForm from "@/Components/Organism/TodoQuickCreateForm.vue";

const props = defineProps({
    todos: {
        type: Array,
        required: true,
    },
    sharedTodos: {
        type: Array,
        required: true,
    },
    completedTodos: {
        type: Array,
        required: true,
    },
    deletedTodos: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});


const filterForm = useForm({
    filter: '',
});
const applyFilter = () => {
    const filters = filterForm.filter.split(' ');
    const params = {};

    filters.forEach(filter => {
        const [key, value] = filter.split('=');
        params[key] = value;
    });
    filterForm.get('/', {
        preserveState: true,
        preserveScroll: true,
        data: {
            filter: filterForm.filter,
            ...params
        },
        only: ['todos'],
    });
};
const handleEnter = (event) => {
    if (event.key === 'Enter') {
        applyFilter();
    }
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="py-12 flex space-x-4 px-8">
            <div class="w-3/4">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <TodoQuickCreateForm />
                        </div>
                    </div>
                </div>


                <div>
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div v-if="!todos || todos?.length === 0" class="p-6 text-gray-900 dark:text-gray-100">
                                <div>
                                    <p>
                                        You have no tasks yet.
                                    </p>
                                </div>
                            </div>
                            <div v-else>
                                <FwbInput
                                    v-model="filterForm.filter"
                                    placeholder="category=Work,Shopping shared=true name=test decription=text"
                                    size="lg"
                                    class="px-6"
                                    @keydown="handleEnter"
                                >
                                    <template #suffix>
                                        <fwb-button :disabled="filterForm.processing" @click="applyFilter">
                                            <span v-if="filterForm.processing">Searching...</span>
                                            <span v-else>Search</span>
                                        </fwb-button>
                                    </template>
                                </FwbInput>

                                <TodoTableActive :todos="todos.data" :categories="categories.data"/>
                                <div class="flex justify-center">
                                    <template v-for="(link) in todos.meta.links">
                                        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                        <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-white': link.active }" :href="link.url" v-html="link.label" />
                                    </template>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-1/4">
                <div class="mb-6">
                    <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="font-bold text-xl pb-4">Shared with me</h2>
                            <TodoTableShared v-if="sharedTodos.data.length" :todos="sharedTodos.data" class="border"/>
                            <span v-else>No records</span>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="font-bold text-xl pb-4">Completed</h2>
                            <TodoTableCompleted v-if="completedTodos.data.length" :todos="completedTodos.data" class="border"/>
                            <span v-else>No records</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="font-bold text-xl pb-4">Deleted</h2>
                            <TodoTableDeleted v-if="deletedTodos.data.length" :todos="deletedTodos.data" class="border"/>
                            <span v-else>No records</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

