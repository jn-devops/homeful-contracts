<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {ref, watch} from "vue";

const props = defineProps({
    contact_reference_code: String
});

const form = useForm({
    contact_reference_code: props.contact_reference_code,
});

const submit = () => {
    form.post(route('consult.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Consult" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="contact_reference_code" value="Contact Reference Code" />

                <TextInput
                    id="contact_reference_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.contact_reference_code"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.contact_reference_code" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('register-contact')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Not yet registered?
                </Link>
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Consult
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
