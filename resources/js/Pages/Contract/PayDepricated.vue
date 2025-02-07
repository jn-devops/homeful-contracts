<script setup>

import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import eWalletGrabpay from '@/Components/PrimaryButton.vue';
import eWalletGcash  from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from "vue";
import axios from "axios";
const props = defineProps({
    reference_code: String,
    amount: String
});

const reference = ref({});
const errorBox = ref(""); // State to hold error messages
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
    amount: props.amount
});

// let data = JSON.stringify({
//   "referenceCode": props.reference_code,
//   "amount": props.amount,
// //   "callbackParam": route('pay.success')
// });
const submit = (paymentMethod,ewallet = null) => {
    errorBox.value = "";
    
const baseURL = window.location.origin;
let postUrl;
let data ;
// console.log(paymentMethod);
if(paymentMethod=="cashier")
{
    data  = JSON.stringify({
  "referenceCode": props.reference_code,
  "amount": props.amount,
//   "callbackParam": route('pay.success')
});
    postUrl = `${baseURL}/api/homeful-cashier`
}

else if(paymentMethod=="eWallet")
{
     data = JSON.stringify({
  "referenceCode": props.reference_code,
  "amount": props.amount,
  "wallet": ewallet,
//   "callbackParam": route('pay.success')
});
    postUrl = `${baseURL}/api/homeful-wallet` 
}
console.log(postUrl);
let config = {
  method: 'post',
  maxBodyLength: Infinity,
  url: postUrl,
  headers: { 
    'Content-Type': 'application/json',
     'Access-Control-Allow-Origin':'*'
  },
  data : data
};

axios.request(config)
        .then((response) => {
            if (paymentMethod === "cashier") {
                const cashierUrl = response.data?.data?.cashierUrl;
                if (cashierUrl) {
                    window.location.href = cashierUrl;
                } else {
                    errorBox.value = response.data.message;
                }
            } else if (paymentMethod === "eWallet") {
                const payUrl = response.data?.pay_url;
                if (payUrl) {
                    window.location.href = payUrl;
                } else {
                    errorBox.value = response.data.message;
                }
            }
        })
        .catch((error) => {
            errorBox.value = error.response?.data?.message || 'An unexpected error occurred. Please try again.';
        });

};

// const submit = () => {
//     form.post(route('pay.store'), {
//         onFinish: () => form.reset(),
//     });
// };
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <div v-if="errorBox" class="mb-4 p-4 bg-red-100 text-red-700 rounded text-center">
            {{ errorBox }}
        </div>
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
            <div>
                <InputLabel for="amount" value="Amount" />
                <TextInput
                    id="amount"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.amount"
                    required
                    autofocus
                />
            <InputError class="mt-2" :message="form.errors.amount" />
            </div>
            <div class="mt-4 flex items-center justify-center">
                <PrimaryButton
                    class="ms-4 "
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click.prevent="submit('cashier')"
                >
                    Pay via Debit/Credit
                </PrimaryButton>
            </div>
            <div class="flex items-center justify-center mt-2">
                Pay via eWallet
            </div>
            <div class="flex items-center justify-center mt-2">
            <PrimaryButton
                class="ms-4 flex items-center bg-white bg-no-repeat bg-center bg-contain rounded-pill"
                :style="{ backgroundImage: 'url(https://logolook.net/wp-content/uploads/2024/02/GCash-Logo.png)', height: '60px', width: '120px',outline: '2px solid #ccc' }"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click.prevent="submit('eWallet', 'gcash')"
            >
            </PrimaryButton>

            <PrimaryButton
                class="ms-4 flex items-center bg-white bg-no-repeat bg-center bg-contain rounded-pill"
                :style="{ backgroundImage: 'url(https://faq.goodwork.ph/wp-content/uploads/2020/08/grabpay.png)', height: '60px', width: '120px',outline: '2px solid #ccc' }"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click.prevent="submit('eWallet', 'grabpay')"
            >
            </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
    
</template>
