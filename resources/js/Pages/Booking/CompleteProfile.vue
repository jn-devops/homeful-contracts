<script setup>
import BookingNavigation from '@/Components/Booking/BookingNavigation.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const transactionType = ref('Credit/Debit Card');
const transactionNumber = ref('045352114253');
const transactionDate = ref('12-19-2024 12:00PM');

const completeDataFormAction = () => {
    // TODO: Action if the "Complete Additional Data Form" button is clicked
}
const dashboardButtonAction = () => {
    // TODO: Action if the "Go to Dashboard" button is clicked
}

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
                <div class="rounded-xl px-4 py-5 grid grid-cols-2 bg-[#F8F9FE] gap-2">
                    <div class="">
                        Transation Type
                    </div>
                    <div class="font-bold text-right">
                        {{transactionType}}
                    </div>
                    <div class="">
                        Transation No.
                    </div>
                    <div class="font-bold text-right">
                        {{ transactionNumber }}
                    </div>
                    <div class="">
                        Transation Date
                    </div>
                    <div class="font-bold text-right">
                        {{ transactionDate }}
                    </div>
                </div>
            </div>
            <div class="mt-10 px-5">
                <PrimaryButton @click="completeDataFormAction">
                    <div class="py-2 font-semibold">
                        Complete Additional Data Form
                    </div>
                </PrimaryButton>
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