<script setup>

import PropertyCard from '@/Components/Card/PropertyCard.vue';
import PropertyDiscoverPage from '@/Components/Card/PropertyDiscoverPage.vue';
import SecondaryDropdown from '@/Components/Input/SecondaryDropdown.vue';
import HomeMatch from '@/Components/Match/HomeMatch.vue';
import SuccessToast from '@/Components/Toast/SuccessToast.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, provide, ref, watch } from "vue";

const props = defineProps({
    buttonOptions: {
        type: [Object, Boolean],
        default: [],
    },
    contactData: {
        type: Object,
        default: [],
    },
    ref_code: {
        type: String,
        default: '',
    },
});

const reference = ref(props.ref_code);

const form = useForm({
    reference_code: reference.value,
    sku: null,
    seller_voucher_code: null
});

const submit = () => {
    form.reference_code = reference.value;
    console.log(form.reference_code, '::', form.sku, form.seller_voucher_code);
    form.post(route('avail.store'), {
        onFinish: () => form.reset(),
    });
};

const sort = ref('Highest');
const sortOption = [
        { title: 'Lowest', description: 'Lowest', current: true },
        { title: 'Highest', description: 'Highest', current: false },
    ]
const discoverPage = ref(false);

const numberFormatter = (num) => new Intl.NumberFormat('en-US', {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
}).format(num);

const propertyDetail = ref(null)

const showDiscoverPage = (sku) => {
    form.sku = sku
    discoverPage.value = true
    propertyDetail.value = props.buttonOptions[sku]
}

const homeMatch = ref(false)

const selectedHouseType = ref([]);
const updateSelectedHouseType = (newData) => {
    selectedHouseType.value = newData
}

const selectedLocation = ref([]);
const updateSelectedLocation = (newData) => {
    selectedLocation.value = newData
}

const grossMonthlyIncome = ref("");
const updateGrossMonthlyIncome = (newData) => {
    grossMonthlyIncome.value = newData
}

const birthdate = ref(null)
const updateBirthdate = (newData) => {
    birthdate.value = newData
}

const houseTypes = [
    {name: "House & Lot", id: "1"},
    {name: "Condominium", id: "2"},
]

const locations = [
    {name: "Pampanga", id: "1"},
    {name: "Laguna", id: "2"},
    {name: "Cavite", id: "3"},
    {name: "Bulacan", id: "4"},
    {name: "Rizal", id: "5"},
]

const notif = ref(false)
const keys = Object.keys(props.buttonOptions)

const valHolder1 = props.buttonOptions[keys[keys.length - 1]]
const valHolder2 = props.buttonOptions[Object.keys(props.buttonOptions)[0]]
const startRange = (valHolder1.details['price'] > valHolder2.details['price']) ? valHolder2 : valHolder1
const lastRange = (valHolder1.details['price'] < valHolder2.details['price']) ? valHolder2 : valHolder1

const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(2) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(0) + 'K';
    }
    return num;
}

const age = computed(() => {
  const birthDate = new Date(props.contactData.date_of_birth);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  
  // Adjust age if the birthday hasn't occurred yet this year
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }

  return age;
});

provide('houseTypes', houseTypes)
provide('locations', locations)

const list_properties = ref([]);

const descending_order = (object_properties) => {
    return Object.values(object_properties)
        .map((item) => {
        const parsedDescription = JSON.parse(item.description);
        const totalContractPrice = parsedDescription.property.total_contract_price;
        return { ...item, totalContractPrice };
        })
        .sort((a, b) => b.totalContractPrice - a.totalContractPrice)
}

const ascending_order = (object_properties) => {
    return Object.values(object_properties)
        .map((item) => {
        const parsedDescription = JSON.parse(item.description);
        const totalContractPrice = parsedDescription.property.total_contract_price;
        return { ...item, totalContractPrice };
        })
        .sort((a, b) => a.totalContractPrice - b.totalContractPrice);
}

onMounted(() => {
    list_properties.value = descending_order(props.buttonOptions);
})

watch (sort, (newVal) => {
    if (newVal === 'Lowest') {
        list_properties.value = ascending_order(props.buttonOptions);
    } else {
        list_properties.value = descending_order(props.buttonOptions);
    }
})

</script>

<template>
    <DefaultLayout>
        <div :class="{'overflow-hidden h-screen': discoverPage}">
            <div class="h-80 w-full relative">
                <img :src="usePage().props.data.appLink + '/images/ModelUnitWalkthrough.gif'" alt="GIF" class="object-cover w-full h-full absolute top-0 left-0 -z-10">
                <div class="flex items-end h-full">
                    <div class="inset-0 bg-gradient-to-t from-white to-transparent opacity-100 h-40 w-full bottom-0 flex items-end pb-4 ps-4">
                        <div class="flex flex-col">
                            <h3 class="text-2xl font-crimson font-bold">Home Fit For You</h3>
                            <span class="text-sm">Range: ₱ {{ formatNumber(startRange.details['price']) }} - ₱ {{ formatNumber(lastRange.details['price']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-3 pt-2">
                <div class="flex flex-row">
                    <div class="basis-4/6">
                        <div @click="homeMatch = !homeMatch" class="flex items-center gap-1 border border-gray-800 w-fit px-3 py-2 rounded-xl cursor-pointer">
                            <svg class="w-4 h-4 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800 font-semibold">Home Match Calculator</span>
                        </div>
                    </div>
                    <div class="basis-2/6 text-right">
                        <SecondaryDropdown 
                            :options="sortOption"
                            v-model="sort"
                            label="Sort"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 20V10m0 10-3-3m3 3 3-3m5-13v10m0-10 3 3m-3-3-3 3"/>
                                </svg>
                            </template>
                            Sort 
                        </SecondaryDropdown>
                    </div>
                </div>
                <div class="text-gray-500 text-xs mt-3">
                    <h6 class="font-bold text-sm">Showing:</h6>
                    <span>All Property Types, All Locations, {{ age }} years of age with an income of P{{ numberFormatter(contactData.monthly_gross_income) }}.</span>
                </div>
                <div class="mt-3">
                    <div v-for="(opt, i) in list_properties">
                        <PropertyCard 
                            :name="JSON.parse(opt.name).name"
                            :market_segment="opt.details['market_segment']"
                            :imgLink="opt.details['facade_url']"
                            :tcp="numberFormatter(opt.details['price'])"
                            :monthlyPayment="numberFormatter(JSON.parse(opt.description).loan_amortization)"
                            :term="JSON.parse(opt.description).bp_term"
                            discoverLink="#"
                            :sku="opt.key"
                            @showDiscoverPage="showDiscoverPage"
                        />
                    </div>
                </div>
            </div>
            <Transition
                enter-active-class="transform transition-transform duration-200 ease-in-out"
                enter-from-class="translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transform transition-transform duration-200 ease-in-out"
                leave-from-class="translate-x-0"
                leave-to-class="translate-x-full"
            >
                <PropertyDiscoverPage 
                    v-if="discoverPage" 
                    v-model:discoverPage="discoverPage"
                    v-model:voucherCode="form.seller_voucher_code"
                    :property-detail="propertyDetail"
                    :contact-data="contactData"
                    :submitEvent="submit"
                />
            </Transition>
            <Transition
                enter-active-class="transform transition-transform duration-200 ease-in-out"
                enter-from-class="-translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transform transition-transform duration-200 ease-in-out"
                leave-from-class="translate-x-0"
                leave-to-class="-translate-x-full"
            >
                <HomeMatch 
                    v-if="homeMatch" 
                    v-model:homeMatch="homeMatch"  
                    :selectedHouseType="selectedHouseType"
                    :selectedLocation="selectedLocation"
                    :grossMonthlyIncome="grossMonthlyIncome"
                    :birthdate="birthdate"
                    @update:selectedHouseType="updateSelectedHouseType"
                    @update:selectedLocation="updateSelectedLocation"
                    @update:grossMonthlyIncome="updateGrossMonthlyIncome"
                    @update:birthdate="updateBirthdate"
                />
            </Transition>
            <div v-if="notif">
                <div class="absolute z-50 top-0 left-0 w-full pt-10" >
                    <SuccessToast >
                        <template #title>
                            Successfully Registered
                        </template>
                        <template #description>
                            Thank you for signing up. You may now proceed to book this property. You will be redirected in 3 seconds.
                        </template>
                    </SuccessToast>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
