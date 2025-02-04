<script setup>
import { ref } from 'vue';


const props = defineProps({
    activePage: {
        type: Number,
        default: 1,
    }
})

const navigations = ref([
    {label: 'Authentication', link: '#', step: 1},
    {label: 'Payment', link: '#', step: 2},
    {label: 'Complete Form', link: '#', step: 3},
])
</script>
<template>
    <div>
        <div class="mb-5">
            <h1 class="text-center font-bold text-xl">Booking Process</h1>
        </div>
        <div class="grid grid-cols-3 gap-4 items-center justify-center">
            <div class="flex flex-col items-center justify-center" v-for="(item, key) in navigations">
                <div 
                    class="rounded-full w-8 h-8 flex items-center justify-center font-extrabold text-sm"
                    :class="activePage === item.step ? 'bg-[#006FFD] text-white' : ((item.step < activePage) ? 'bg-[#B4DBFF]' : 'bg-[#F8F9FE] text-[#8F9098]')"
                >
                    <div v-if="item.step < activePage">
                        <svg class="w-6 h-6 text-[#006FFD]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                        </svg>

                    </div>
                    <div v-else>
                        {{ item.step }}
                    </div>
                </div>
                <div 
                    class="font-semibold text-sm mt-2"
                    :class="activePage === item.step ? 'text-black' : 'text-[#8F9098]'"
                >
                    {{ item.label }}
                </div>
            </div>
            <!-- <div class="flex flex-col items-center justify-center">
                <div class="bg-[#F8F9FE] rounded-full w-8 h-8 flex items-center justify-center font-extrabold text-[#8F9098] text-sm">2</div>
                <div class="font-semibold text-sm mt-2 text-[#8F9098]">Payment</div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="bg-[#F8F9FE] rounded-full w-8 h-8 flex items-center justify-center font-extrabold text-[#8F9098] text-sm">3</div>
                <div class="font-semibold text-sm mt-2 text-[#8F9098]">Complete Form</div>
            </div> -->
        </div>
    </div>
</template>