<script setup>
import { defineProps, defineEmits, onMounted, ref, watch } from 'vue'
import DefaultGradientBorder from '../Container/DefaultGradientBorder.vue';

const props = defineProps({
  label: {
    type: String,
    default: 'Default Label',
  },
  required: {
    type: Boolean,
    default: false,
  },
  verifiedPromoCode: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Enter text...',
  },
  errorMessage: {
    type: String,
    default: null,
  },
  helperMessage: {
    type: String,
    default: null,
  },
  successMessage: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'text',
  },
  max: {
    type: Number,
    default: null,
  },
  readOnly: {
    type: Boolean,
    default: false,
  },
  modelValue: {
    type: String,
    default: '',
  }
});

const localValue = ref('')

const emit = defineEmits(['update:modelValue', 'update:verifiedPromoCode'])

const updateValue = (newVal) => {
    // if (newVal != null && newVal != ''){
    //     // localValue.value = formatValue(newVal)
    //     localValue.value = newVal
    //     // emit('update:modelValue', newVal.replace(/[^0-9]/g, ''))
    //   }
      emit('update:modelValue', newVal)
}

const onBlur = () => {
  // localValue.value = formatValue(props.modelValue);
  localValue.value = props.modelValue;

};

const onFocus = () => {
  localValue.value = props.modelValue;
};

const formatValue = (str) => {
    if (str != null && str != ''){
        let value = str.replace(/[^0-9]/g, '');
        if (value.length <= 6) {
            return value; 
        } else if (value.length <= 9) {
            return value.replace(/^(\d{6})(\d{0,3})/, '$1-$2'); 
        } else {
            return value.replace(/^(\d{6})(\d{3})(\d{0,2})$/, '$1-$2-$3')
        }
    }
}
const edit = () => {
    emit('update:verifiedPromoCode', false)
}

const empty = () => {
    localValue.value = ""
    emit('update:modelValue', "")
}

// watch(() => props.verifiedPromoCode, (newValue) => {
//     editable.value = newValue
// })

onMounted(() => {
    if(props.modelValue){
        // localValue.value = formatValue(props.modelValue)
        localValue.value = props.modelValue
    }
})

</script>
<template>
    <div class="w-full">
        <label class="text-sm font-semibold mb-1 text-gray-900" 
        :class="{
          'text-red-600': errorMessage, 
        }"
        >
          {{label}} <span v-if="required" class="text-red-600">*</span>
        </label>
        <!-- <div 
            class="w-full border border-gray-500 focus-within:border-black focus-within:border-2"
            :class="errorMessage ? 'border-red-700' : ''"
        > -->
        <DefaultGradientBorder :has-error="(errorMessage) ? true : false">
          <div class="flex flex-row items-center justify-center">
            <div class="w-full grow">
              <input
                :type="type"
                :value="modelValue"
                @input="updateValue($event.target.value)"
                class="w-full border-none focus:ring-0" 
                :class="{
                  'text-red-700': errorMessage,
                  'text-gray-700': verifiedPromoCode
                }"
                :placeholder="placeholder"
                :maxlength="max"
                :readonly="verifiedPromoCode"
              />
            </div>
            <div v-show="verifiedPromoCode" @click="edit" class="flex-none pe-2 font-semibold text-blue-600 underline text-sm flex items-center cursor-pointer">
              <svg class="w-6 h-6 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
              </svg>
            </div>
          </div>
        </DefaultGradientBorder>
        <!-- </div> -->
        <p class="text-xs text-red-700 mt-1" v-if="errorMessage">{{ errorMessage }}</p>
        <p class="text-xs text-gray-600 mt-1" v-if="helperMessage">{{ helperMessage }}</p>
        <p class="text-xs text-green-700 mt-1" v-if="verifiedPromoCode">{{ successMessage }}</p>
    </div>
</template>