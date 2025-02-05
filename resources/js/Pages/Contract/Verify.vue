<script setup>

import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import BookingNavigation from '@/Components/Booking/BookingNavigation.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    reference_code: String
});

const reference = ref({});

const goToEkyc = () => {

}

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
});

const submit = () => {
    form.post(route('verify.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <DefaultLayout :hide-header="true" >
        <div class="relative p-4 pt-7">
            <div class="absolute top-8 left-5">
                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </div>
            <BookingNavigation :active-page="1" />
            <div class="flex flex-col items-center mt-10">
                <h3 class="text-xl text-left w-full mb-3 font-extrabold">Submit ID & Take Selfie</h3>
                <div class="w-full p-5 px-10 bg-[#F8F9FE] rounded-xl mb-10">
                    <ol class="list-decimal list-inside text-sm space-y-1">
                        <li>Ensure good lighting with no glares.</li>
                        <li>Scan your government-issued ID.</li>
                        <li>Take a selfie.</li>
                        <li>Submit Scanned ID & Selfie</li>
                        <li>You're done!</li>
                    </ol>
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <PrimaryButton>
                        <div class="flex flex-row gap 3 items-center justify-center">
                            Continue
                            <svg class="w-6 h-6text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/>
                            </svg>
                        </div>
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </DefaultLayout>
</template>
