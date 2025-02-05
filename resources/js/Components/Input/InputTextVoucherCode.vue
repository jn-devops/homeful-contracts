<script setup>
import { defineProps, defineEmits } from 'vue'
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
    type: [String, Number],
    default: null,
  }
});

const emit = defineEmits(['update:modelValue'])

const updateValue = (newVal) => {
  emit('update:modelValue', newVal)
}

</script>
<template>
    <div class="w-full">
        <label class="text-sm font-semibold mb-1" :class="errorMessage ? 'text-red-600' : 'text-gray-900'">{{label}} <span v-if="required" class="text-red-600">*</span></label>
        <!-- <div 
            class="w-full border border-gray-500 focus-within:border-black focus-within:border-2"
            :class="errorMessage ? 'border-red-700' : ''"
        > -->
        <DefaultGradientBorder :has-error="(errorMessage) ? true : false">
            <!-- <div class="flex flex-row items-center justify-center"> -->
                <!-- <div class="w-full grow"> -->
                    <input
                        :type="type"
                        :value="modelValue"
                        @input="updateValue($event.target.value)"
                        class="w-full border-none focus:ring-0" 
                        :class="errorMessage ? 'text-red-700' : ''"
                        :placeholder="placeholder"
                        :maxlength="max"
                        :readonly="readOnly"
                    />
                <!-- </div>
                <div class="flex-none pe-2 font-semibold text-orange-600 text-sm flex items-center">
                    <svg class="w-5 h-5 text-orange-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                    </svg>
                    Apply

                </div> -->
            <!-- </div> -->
        </DefaultGradientBorder>
        <!-- </div> -->
        <p class="text-xs text-red-700 mt-1" v-if="errorMessage">{{ errorMessage }}</p>
        <p class="text-xs text-gray-600 mt-1" v-if="helperMessage">{{ helperMessage }}</p>
    </div>
</template>