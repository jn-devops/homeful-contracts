<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        default: "Test Type"
    },
    imgLink: {
        type: String,
        default: '',
    },
    location: {
        type: String,
        default: "Test Location",
    },
    tcp: {
        type: String,
        default: "000,000",
    },
    monthlyPayment: {
        type: String,
        default: "0,000",
    },
    term: {
        type: Number,
        default: 0,
    },
    discoverLink: {
        type: String,
        default: '',
    },
    sku: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['showDiscoverPage']);

function showDiscover(sku) {
    emit('showDiscoverPage', sku);
}

</script>
<template>
    <div class="relative w-full mx-auto rounded-xl my-3">
        <div class="absolute bottom-0 left-0 w-full rounded-b-xl bg-gradient-to-b from-transparent to-black text-white pt-12 pb-3 shadow-lg px-5">
            <div class="flex flex-row items-center">
                <div class="basis-4/6 leading-none">
                    <p class="text-sm leading-none">{{ name }}</p>
                    <p class="font-bold ">{{ location }}</p>
                    <p class="text-3xl font-bold my-1">₱{{ tcp }}</p>
                    <p class="text-xs">Monthly Payment: ₱{{ monthlyPayment }}</p>
                    <p class="text-xs">Years to Pay: {{ term }} years</p>
                </div>
                <div class="basis-2/6">
                    <a :href="discoverLink">
                    <button class="font-bold px-5 py-2 bg-black/10 backdrop-blur-sm" @click.prevent="showDiscover(sku)">
                        Discover
                    </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="w-full h-[300px]">
            <img 
                :src="imgLink" 
                alt="Property Image" 
                class="object-cover w-full h-full rounded-xl"
            />
        </div>
    </div> 
</template>