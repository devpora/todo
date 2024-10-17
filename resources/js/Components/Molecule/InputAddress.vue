<script setup>
import { ref } from 'vue';
const props = defineProps({
    emails: {
        type: Array,
        required: true,
    },
});

const inputEmail = ref('');
const addEmail = () => {
    const email = inputEmail.value.trim();

    if (email && !props.emails.includes(email) && validateEmail(email)) {
        props.emails.push(email);
        inputEmail.value = '';
    }
};

const validateEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
};

const removeEmail = (index) => {
    props.emails.splice(index, 1);
};
</script>

<template>
    <div class="flex flex-col pb-2">
        <input
            id="email"
            type="email"
            v-model="inputEmail"
            @keyup.enter="addEmail"
            class="mt-1 block w-full border rounded-md p-2"
            placeholder="Add email and press Enter"
        />
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <span
            v-for="(email, index) in props.emails"
            :key="index"
            class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md flex items-center space-x-2"
        >
      <span>{{ email }}</span>
      <span @click="removeEmail(index)" class="text-red-500 hover:text-red-700 hover:cursor-pointer">
        &times;
      </span>
    </span>
    </div>
</template>
