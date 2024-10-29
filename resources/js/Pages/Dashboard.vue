<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TodoTableActive from '@/Components/Organism/TodoTableActive.vue'
import TodoTableCompleted from '@/Components/Organism/TodoTableCompleted.vue'
import TodoTableDeleted from '@/Components/Organism/TodoTableDeleted.vue'
import TodoTableShared from '@/Components/Organism/TodoTableShared.vue'
import TodoQuickCreateForm from '@/Components/Organism/TodoQuickCreateForm.vue'
import TableSections from '@/Components/Organism/TableSections.vue'
import { ref } from 'vue'
import AtomCard from '@/Components/atoms/AtomCard.vue'

defineProps({
  categories: {
    type: Array,
    required: true
  }
})

const tableActiveRefreshKey = ref(0)
const tableSharedRefreshKey = ref(0)
const tableCompletedRefreshKey = ref(0)
const tableDeletedRefreshKey = ref(0)
</script>

<template>
  <p class="fixed w-full bg-red-500 text-center z-10 top-0 text-gray-900">
    For presentation purposes only. All data is removed daily at midnight.
  </p>
  <Head><title>Dashboard</title></Head>
  <AuthenticatedLayout>
    <div class="py-12 flex flex-col lg:flex-row px-8 gap-2">
      <div class="w-full lg:w-3/4">
        <AtomCard class="mx-auto max-w-7xl">
          <TodoQuickCreateForm />
        </AtomCard>

        <TableSections title="Active" class="mx-auto max-w-7xl">
          <TodoTableActive
            :tableRefresh="tableActiveRefreshKey"
            :categories="categories.data"
            @loadTableDeleted="tableDeletedRefreshKey++"
            @loadTableUpdated="tableSharedRefreshKey++"
            @loadTableCompleted="tableCompletedRefreshKey++"
          />
        </TableSections>
      </div>

      <div class="w-full lg:w-1/4">
        <TableSections title="Shared with me">
          <TodoTableShared :tableRefresh="tableSharedRefreshKey" />
        </TableSections>

        <TableSections title="Completed">
          <TodoTableCompleted
            :tableRefresh="tableCompletedRefreshKey"
            @loadTableActive="tableActiveRefreshKey++"
          />
        </TableSections>

        <TableSections title="Deleted">
          <TodoTableDeleted
            :tableRefresh="tableDeletedRefreshKey"
            @loadTableActive="tableActiveRefreshKey++"
          />
        </TableSections>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
