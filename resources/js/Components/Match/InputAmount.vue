<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    rawValue: {
        type: String,
        default: ""
    }
})

const emit = defineEmits(['update:rawValue'])

const formattedValue = ref("");
const range = ref(0);

const onInput = (event) => {
    if(event.target.value){
        const value = event.target.value.replace(/[^0-9]/g, "");
        if (!isNaN(value)) {
            emit('update:rawValue', value)
            formattedValue.value = formatNumber(value);
            range.value = value
        }
    }else{
        emit('update:rawValue', "")
        formattedValue.value = "";
        range.value = 0
    }
};

const onBlur = () => {
  formattedValue.value = formatNumber(props.rawValue);
};

const onFocus = () => {
  formattedValue.value = props.rawValue;
};

const formatNumber = (value) => {
  return parseFloat(value).toLocaleString("en-US");
}

const updateValueThruRange = (event) => {
    formattedValue.value = formatNumber(event.target.value)
    emit('update:rawValue', event.target.value)
}

onMounted(() => {
    if(props.rawValue){
        formattedValue.value = formatNumber(props.rawValue)
        range.value = props.rawValue
    }else{
        formattedValue.value = ""
        range.value = 0
    }
})

</script>
<template>
    <div>
        <div class="flex items-center w-full border border-gray-400 p-0 px-3">
            <span class="text-gray-300 flex-none font-bold">PHP</span>
            <input 
                type="text"
                class="border-none grow focus:ring-0 text-sm"
                placeholder="Gross Monthly Income"
                v-model="formattedValue"
                @input="onInput"
                @blur="onBlur"
                @focus="onFocus"
            >
        </div>
        <input
            type="range"
            v-model="range"
            @input="updateValueThruRange"
            min="10000"
            max="200000"
            step="10000"
            class="w-full mt-2 h-2 bg-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>
</template>