<script setup>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TodoTableActive from "@/Components/Organism/TodoTableActive.vue";
import TodoTableCompleted from "@/Components/Organism/TodoTableCompleted.vue";
import TodoTableDeleted from "@/Components/Organism/TodoTableDeleted.vue";
import TodoTableShared from "@/Components/Organism/TodoTableShared.vue";
import TodoQuickCreateForm from "@/Components/Organism/TodoQuickCreateForm.vue";
import TableSections from "@/Components/Organism/TableSections.vue";
import {ref} from "vue";
import AtomCard from "@/Components/atoms/AtomCard.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const tableActiveRefreshKey = ref(0)
const tableSharedRefreshKey = ref(0)
const tableCompletedRefreshKey = ref(0)
const tableDeletedRefreshKey = ref(0)
</script>

<template>
    <Head><title>Dashboard</title></Head>
    <AuthenticatedLayout>
        <div class="py-12 flex px-8">
            <div class="w-3/4">
                <AtomCard>
                    <TodoQuickCreateForm />
                </AtomCard>

                <TableSections title="Active" class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <TodoTableActive
                        :tableRefresh="tableActiveRefreshKey"
                        @loadTableDeleted="tableDeletedRefreshKey++"
                        @loadTableUpdated="tableSharedRefreshKey++"
                        @loadTableCompleted="tableCompletedRefreshKey++"
                        :categories="categories.data"/>
                </TableSections>
            </div>

            <div class="w-1/4">
                <TableSections title="Shared with me">
                    <TodoTableShared :tableRefresh="tableSharedRefreshKey"/>
                </TableSections>

                <TableSections title="Completed">
                    <TodoTableCompleted :tableRefresh="tableCompletedRefreshKey" @loadTableActive="tableActiveRefreshKey++"/>
                </TableSections>

                <TableSections title="Deleted">
                    <TodoTableDeleted :tableRefresh="tableDeletedRefreshKey" @loadTableActive="tableActiveRefreshKey++"/>
                </TableSections>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

