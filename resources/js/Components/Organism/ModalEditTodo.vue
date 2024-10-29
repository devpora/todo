<script setup>
import {inject, ref} from 'vue'
import {generateStringForShare} from '@/utils/string';
import {FwbButton, FwbInput, FwbCheckbox, FwbModal, FwbToggle} from 'flowbite-vue'
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {useForm} from "@inertiajs/vue3";
import TextArea from "@/Components/TextArea.vue";
import InputAddress from "@/Components/Molecule/InputAddress.vue";
const showToast = inject('showToast');

const isShowModal = ref(false)
const emit = defineEmits(['updated']);

function closeModal () {
    isShowModal.value = false
}
function showModal () {
    isShowModal.value = true
}
const props = defineProps({
    todo: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: props.todo.name || '',
    description: props.todo.description || '',
    category: props?.todo?.categories?.map(category => category.id) || [],
    sharedEmails: props.todo.sharedEmails || [],
    sharedLink: props.todo.sharedLink || null,
    isShared: props.todo.isShared || false,
    isPublic: props.todo.isPublic || false,
});

const submit = () => {
    form.post(route('todo.update', props.todo.id), {
        onSuccess: () => {
            showToast('success', 'Todo was updated');
            closeModal()
            emit('updated')
        }
    });
};
const toggleCategory = (categoryId) => {
    const index = form.category.indexOf(categoryId);
    if (index === -1) {
        form.category.push(categoryId);
    } else {
        form.category.splice(index, 1);
    }
};
const onChangeIsShared = () => {
    if(!form.sharedLink){
        form.sharedLink = generateStringForShare();
    }
};

const copyLink = async () => {
    const url = import.meta.env.VITE_BASE_URL;
    await navigator.clipboard.writeText(url + '/shared/public/' + form.sharedLink);
    showToast('success', 'Copied');
};
</script>

<template>
    <svg  @click="showModal" color="blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 hover:cursor-pointer">
        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
    </svg>

    <fwb-modal v-if="isShowModal" @close="closeModal" class="text-left">
        <template #header>
            <div class="flex items-center text-lg">
                Edit ToDo
            </div>
        </template>
        <template #body>
            <form>
                <div>
                    <InputLabel for="name" value="Name" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="description" value="Description" />
                    <TextArea
                        id="description"
                        :rows="5"
                        class="mt-1 block w-full"
                        v-model="form.description"
                    />

                    <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="mt-4">
                    <InputLabel for="category" value="Category" />
                    <div class="grid grid-cols-5">
                        <div v-for="category in categories" :key="category.id" class="mb-2">
                            <input
                                type="checkbox"
                                :id="'category-' + category.id"
                                :name="'category-' + category.id"
                                :value="category.id"
                                :checked="form.category.includes(category.id)"
                                @change="toggleCategory(category.id)"
                                class="rounded"
                            />
                            <label :for="'category-' + category.id" class="pl-2 hover:cursor-pointer">{{category.name}}</label>

                        </div>
                    </div>

                    <InputError class="mt-2" :message="form.errors.category" />
                </div>

                <div class="mt-4">
                    <InputLabel for="shared" value="Shared" />
                    <FwbCheckbox
                        id="share"
                        v-model="form.isShared"
                        :checked="form.isShared"
                        @change="onChangeIsShared()"
                        label="Enable Sharing"
                        class="pb-4"
                    />
                    <div v-if="form.isShared">
                        <FwbToggle v-model="form.isPublic" :label="!form.isPublic ? 'Private' : 'Public'" size="sm"/>
                        <div v-if="!form.isPublic">
                            <InputAddress  :emails.sync="form.sharedEmails"/>
                            <InputError class="mt-2" :message="form.errors.sharedEmails" />
                        </div>


                        <div  v-else>
                            <InputLabel for="sharedLink" value="Link for Public share" />
                            <FwbInput
                                id="sharedLink"
                                type="text"
                                v-model="form.sharedLink"
                                size="lg"
                                disabled
                            >
                                <template #suffix>
                                    <span @click="copyLink" class="hover:cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                        </svg>

                                    </span>
                                </template>

                            </FwbInput>
                            <InputError class="mt-2" :message="form.errors.sharedLink" />
                        </div>
                    </div>

                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex justify-between">
                <fwb-button @click="closeModal" color="alternative">
                    Cancel
                </fwb-button>
                <fwb-button :disabled="form.processing" @click="submit" color="green"  :class="{ 'opacity-25': form.processing }">
                    Save
                </fwb-button>
            </div>
        </template>
    </fwb-modal>
</template>
