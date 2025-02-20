<script setup>

import BookingNavigation from '@/Components/Booking/BookingNavigation.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import axios from "axios";
const props = defineProps({
    reference_code: String,
    payment_details: Object,
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
});

let data = JSON.stringify({
  "referenceCode": props.reference_code
});
const submit = () => {
// console.log(`${window.location.origin}/contact-paid/${props.reference_code}`);
window.location.href = `${window.location.origin}/contact-paid/${props.reference_code}`;
};

const transactionType = ref('Credit/Debit Card');
const transactionNumber = ref('045352114253');
const transactionDate = ref('12-19-2024 12:00PM');

const completeDataFormAction = () => {
    // TODO: Action if the "Complete Additional Data Form" button is clicked
}
const dashboardButtonAction = () => {
    // TODO: Action if the "Go to Dashboard" button is clicked
}

onMounted(() => {
    console.log(props.payment_details.data.orderInformation.referencedId);
    
    transactionType.value = "Credit/Card Payment"
    transactionNumber.value = props.payment_details?.data.orderInformation.referencedId ?? ''
    transactionDate.value = props.payment_details?.data.orderInformation.responseDate ?? ''

    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });

});

</script>

<template>
    <DefaultLayout :hide-header="true" >
        <div class="relative p-4 pt-7">
            <div class="absolute top-8 left-5">
                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </div>
            <BookingNavigation :active-page="3" />
            <div class="flex flex-col items-center justify-center">
                <div>
                    <div class="w-full flex items-center justify-center">
                        <img 
                            :src="usePage().props.data.appLink+'/images/successpayment.gif'" alt="Success"
                            class="w-48"    
                        >
                    </div>
                    <div class="text-center px-10">
                        <h1 class="font-extrabold text-3xl">Success!</h1>
                        <p class="text-sm leading-none mt-2">You’ve just paid the ₱10,000 fee for your home loan consultation!</p>
                    </div>
                </div>
            </div>
            <div class="mt-10">
                <h5 class="text-gray-500 ">Transaction Details</h5>
                <div class="rounded-xl px-4 py-5 grid grid-cols-12 bg-[#F8F9FE] gap-2">
                    <div class="col-span-5 text-sm">
                        Transation Type
                    </div>
                    <div class="font-bold col-span-7 text-right text-sm">
                        {{transactionType}}
                    </div>
                    <div class="col-span-5 text-sm">
                        Transation No.
                    </div>
                    <div class="font-bold col-span-7 text-right text-sm break-all">
                        {{ transactionNumber }}
                    </div>
                    <div class="col-span-5 text-sm">
                        Transation Date
                    </div>
                    <div class="font-bold col-span-7 text-right text-sm">
                        {{ transactionDate }}
                    </div>
                </div>
            </div>
            <div class="mt-10 px-5">
                <Link href="/redirect-contact" method="get">
                    <PrimaryButton >
                        <div class="py-2 font-semibold">
                            Complete Additional Data Form
                        </div>
                    </PrimaryButton>
                </Link>
                <p class="text-xs text-gray-500">
                    We may require additional information for a thorough assessment.
                </p>
                <br>
                <SecondaryButton @click="dashboardButtonAction">
                    <div class="py-2 font-semibold">
                        Go to Dashboard
                    </div>
                </SecondaryButton>
            </div>
        </div>
    </DefaultLayout>
</template>
