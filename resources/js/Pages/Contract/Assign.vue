<script setup>

import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from "vue";

const props = defineProps({
    reference_code: String
});

const reference = ref({});

watch (
    () => usePage().props.flash.event,
    (event) => {
        switch (event?.name) {
            case 'reference':
                console.log('event:', event?.data);
                reference.value = event?.data;
                break;
        }
    },
    { immediate: true }
);

const form = useForm({
    reference_code: props.reference_code,
    assigned_contact: "Juan de la Cruz (auto-generated)"
});

const submit = () => {
    form.post(route('assign.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="reference_code" value="Reference Code" />

                <TextInput
                    id="reference_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.reference_code"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.reference_code" />
            </div>

            <div class="mt-4">
                <InputLabel for="assigned_contact" value="Assigned Contact" />

                <TextInput
                    id="assigned_contact"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.assigned_contact"
                    required
                    readonly
                />

                <InputError class="mt-2" :message="form.errors.assigned_contact" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Accept Assignment
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
