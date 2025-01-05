<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import ButtonOptions from "@/Components/ButtonOptions.vue";

const props = defineProps({
    matches: Object,
    buttonOptions: {
        type: [Object, Boolean],
        default: [],
    }
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
    reference_code: reference.value,
    sku: null,
    option: null,
});

const submit = () => {
    form.post(route('avail.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            {{ form.option }}

            <div class="col-span-6 lg:col-span-4">
                <ButtonOptions :options="buttonOptions" v-model:option="form.sku"/>
            </div>

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

<!--            <div class="mt-4">-->
<!--                <InputLabel for="sku" value="SKU" />-->

<!--                <TextInput-->
<!--                    id="sku"-->
<!--                    type="text"-->
<!--                    class="mt-1 block w-full"-->
<!--                    v-model="form.sku"-->
<!--                    required-->
<!--                />-->

<!--                <InputError class="mt-2" :message="form.errors.sku" />-->
<!--            </div>-->

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Avail
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
