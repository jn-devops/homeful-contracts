<script setup>
import { defineProps, defineEmits } from 'vue'

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
        <div 
            class="w-full border border-gray-500 focus-within:border-black focus-within:border-2"
            :class="errorMessage ? 'border-red-700' : ''"
        >
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
        </div>
        <p class="text-xs text-red-700 mt-1" v-if="errorMessage">{{ errorMessage }}</p>
        <p class="text-xs text-gray-600 mt-1" v-if="helperMessage">{{ helperMessage }}</p>
    </div>
</template>