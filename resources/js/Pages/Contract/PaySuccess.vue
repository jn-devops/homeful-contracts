<script setup>

import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from "vue";
import axios from "axios";
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
});

let data = JSON.stringify({
  "referenceCode": props.reference_code,
  "amount": 600,
  "callbackParam": ""
});
const submit = () => {

// console.log(`${window.location.origin}/contact-paid/${props.reference_code}`);
window.location.href = `${window.location.origin}/contact-paid/${props.reference_code}`;
let config = {
  method: 'get',
  maxBodyLength: Infinity,
  url: `${window.location.origin}/contact-paid/${props.reference_code}`,
  headers: { 
    'Content-Type': 'application/json'
  },
//   data : data
};

axios.request(config)
.then((response) => {
  if(response.data.data.cashierUrl)
  {
           // Redirect to the cashier URL
           window.location.href = response.data.data.cashierUrl;
  } 
  else 
  {
            console.log('Cashier URL not found in response.');
  }
  
  console.log(response.data.data.cashierUrl);

})
.catch((error) => {
  console.log(error);
});
};
// const submit = () => {
//     form.post('/api/homeful-cashier')
//     form.post(route('pay.store'), {
//         onFinish: () => form.reset(),
//     });
// };
</script>

<template>
    <GuestLayout>
        <Head title="Payment Confirmation" />

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    OK - Paid
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
