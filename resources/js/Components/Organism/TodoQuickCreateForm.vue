<script setup>
import {inject} from "vue";
import { useForm } from '@inertiajs/vue3';
import { FwbInput, FwbButton } from 'flowbite-vue';
const showToast = inject('showToast');

const form = useForm({
    name: ''
});
const submitForm = () => {
    form.post(route('todo.quickStore'), {
        onSuccess: () => {
            form.reset();
            showToast('success', 'Success');
        },
    });
};
const handleEnter = (event) => {
    if (event.key === 'Enter') {
        submitForm();
    }
};
</script>

<template>
    <div>
        <FwbInput
            v-model="form.name"
            placeholder="Create todo quickly"
            size="lg"
            @keydown="handleEnter"
        >
            <template #suffix>
                <fwb-button :disabled="form.processing" @click="submitForm">
                    <span v-if="form.processing">Creating...</span>
                    <span v-else>Create</span>
                </fwb-button>
            </template>
        </FwbInput>
        <p v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</p>
    </div>
</template>
