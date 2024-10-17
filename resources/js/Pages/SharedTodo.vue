<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
const props = defineProps({
    todo: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Shared Todo" />
    <GuestLayout>
        <div class="container mx-auto">
            <div v-if="todo.data">
                <div class="mb-4">
                    <span class="text-2xl font-bold mb-2">Title</span>
                    <p class="text-lg">{{ todo.data.name }}</p>
                </div>
                <div class="mb-4">
                    <span class="text-2xl font-bold mb-2">Description</span>
                    <p class="text-lg">{{ todo.data.description }}</p>
                </div>
                <div v-if="todo.data.categories && todo.data.categories.length > 0" class="mb-4">
                    <h2 class="text-xl font-semibold mb-2">Category</h2>
                    <ul class="list-disc pl-5">
                        <li v-for="category in todo.data.categories" :key="category.id" class="text-lg">
                            {{ category.name }}
                        </li>
                    </ul>
                </div>
            </div>

            <div v-else>
                <p>Not Found</p>
            </div>
        </div>
    </GuestLayout>

</template>
