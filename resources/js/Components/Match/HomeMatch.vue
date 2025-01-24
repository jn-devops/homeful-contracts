<script setup>
import { inject, onMounted, ref, watch } from 'vue'
import TagSelector from './TagSelector.vue'
import InputAmount from './InputAmount.vue'
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import PrimaryButton from '../Button/PrimaryButton.vue';

const props = defineProps({
    homeMatch: {
        type: Boolean,
        default: false
    },
    selectedHouseType: {
        type: Array,
        default: []
    },
    selectedLocation: {
        type: Array,
        default: []
    },
    grossMonthlyIncome: {
        type: String,
        default: ""
    },
    birthdate: {
        type: Date,
    }
})

const emit = defineEmits(['update:homeMatch', 'update:selectedHouseType', 'update:selectedLocation', 'update:grossMonthlyIncome', 'update:birthdate'])
const localBirthdate = ref(new Date());
const localHomeMatch = ref(props.homeMatch)

const toggleHomeMatch = () => {
    localHomeMatch.value = !localHomeMatch.value
    emit('update:homeMatch', localHomeMatch.value)
}

const locations = inject('locations')
const houseTypes = inject('houseTypes')
const updateSelectedHouseType = (newData) => {
    emit('update:selectedHouseType', newData)
}
const updateSelectedLocation = (newData) => {
    emit('update:selectedLocation', newData)
}
const updateGrossMonthlyIncome = (newValue) => {
    emit('update:grossMonthlyIncome', newValue)
}
const updateBirthdate = (newValue) => {
    emit('update:birthdate', newValue)
}

const format = (date) => {
  const day = date.getDate();
  const month = date.getMonth() + 1;
  const year = date.getFullYear();

  return `${year}-${month}-${day}`;
}

watch(
    localBirthdate,
    () => {
        emit('update:birthdate', localBirthdate.value)
    },
)
watch(() => props.homeMatch, (newVal) => {
    localHomeMatch.value = newVal
})

onMounted(() => {
    localBirthdate.value = props.birthdate
})

</script>
<template>
    <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-10">
        <div class="bg-white w-full max-w-[450px] h-screen overflow-y-auto rounded shadow-lg pt-24">
            <div class="grid grid-cols-3 gap-2">
                <div class="px-5 cursor-pointer" @click="toggleHomeMatch">
                    <h3 class="underline text-sm font-semibold">Back</h3>
                </div>
                <div class="text-center">
                    <h1 class="font-bold">HomeMatch</h1>
                </div>
                <div class="px-5 text-right cursor-pointer">
                    <h3 class="text-sm font-semibold">Clear All</h3>
                </div>
            </div>
            <hr class="mt-4 mb-8 mx-3" />
            <TagSelector
                :lists="houseTypes"
                label="House Type"
                :listSelected="selectedHouseType"
                @update:listSelected="updateSelectedHouseType"
            />
            <hr class="my-8 mx-3" />
            <TagSelector
                :lists="locations"
                label="Location"
                :listSelected="selectedLocation"
                @update:listSelected="updateSelectedLocation"
            />
            <hr class="mt-8 mb-2 mx-3" />
            <div class="px-6">
                <h3 class="text-sm font-semibold mb-1">Gross Monthly Income</h3>
                <InputAmount 
                    :rawValue="grossMonthlyIncome"
                    @update:raw-value="updateGrossMonthlyIncome"
                />
            </div>
            <hr class="mt-8 mb-2 mx-3" />
            <div class="px-6">
                <h3 class="text-sm font-semibold mb-1">Birthday</h3>
                <Datepicker 
                    v-model="localBirthdate" 
                    :enable-time-picker="false" 
                    format="LLL d, yyyy"
                    :class="{'bg-green-200': true}"
                 />
            </div>
            <div class="absolute bottom-0 left-0 w-full p-3">
                <PrimaryButton />
            </div>
        </div>
    </div>
</template>
<style>
.dp__theme_light {
    --dp-background-color: #fff;
    --dp-text-color: #212121;
    --dp-hover-color: #f3f3f3;
    --dp-hover-text-color: #212121;
    --dp-hover-icon-color: #959595;
    --dp-primary-color: #0f0f10;
    --dp-primary-disabled-color: #404040;
    --dp-primary-text-color: #f8f5f5;
    --dp-secondary-color: #c0c4cc;
    --dp-border-color: #ddd;
    --dp-menu-border-color: #ddd;
    --dp-border-color-hover: #aaaeb7;
    --dp-border-color-focus: #aaaeb7;
    --dp-disabled-color: #f6f6f6;
    --dp-scroll-bar-background: #f3f3f3;
    --dp-scroll-bar-color: #959595;
    --dp-success-color: #76d275;
    --dp-success-color-disabled: #a3d9b1;
    --dp-icon-color: #959595;
    --dp-danger-color: #ff6f60;
    --dp-marker-color: #ff6f60;
    --dp-tooltip-color: #fafafa;
    --dp-disabled-color-text: #8e8e8e;
    --dp-highlight-color: rgb(25 118 210 / 10%);
    --dp-range-between-dates-background-color: var(--dp-hover-color, #f3f3f3);
    --dp-range-between-dates-text-color: var(--dp-hover-text-color, #212121);
    --dp-range-between-border-color: var(--dp-hover-color, #f3f3f3);
}
</style>