<script setup>
import { ref, watch } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    options: {
        type: Array,
        default: null,
    },
    label: {
        type: String,
        default: 'Sample',
    },
    modelValue: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:modelValue'])
const publishingOptions = props.options;
const selected = ref(null)

watch(selected, (newValue) => {
    emit('update:modelValue', newValue.title)
})
</script>
<template>
    <Listbox as="div" v-model="selected">
      <ListboxLabel class="sr-only">Change published status</ListboxLabel>
      <div class="relative">
        <div class="inline-flex divide-x divide-gray-700 rounded-md outline-none">
          <div class="inline-flex items-center gap-x-1.5 rounded-xl border border-gray-700 px-3 py-2 text-gray-800">
            <slot name="icon">
                <CheckIcon class="-ml-0.5 size-5" aria-hidden="true" />
            </slot>
            <p class="text-sm font-semibold">{{ (selected) ? selected.title : label }}</p>
            <ListboxButton class="inline-flex items-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-gray-400">
              <span class="sr-only">Change published status</span>
              <ChevronDownIcon class="size-5 text-gray-700 forced-colors:text-[Highlight]" aria-hidden="true" />
            </ListboxButton>
          </div>
        </div>
  
        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
          <ListboxOptions class="absolute right-0 z-10 mt-2 w-48 origin-top-right divide-y divide-gray-200 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
            <ListboxOption as="template" v-for="option in publishingOptions" :key="option.title" :value="option" v-slot="{ active, selected }">
              <li :class="[active ? 'bg-gray-600 text-white' : 'text-gray-900', 'cursor-default select-none p-4 text-sm']">
                <div class="flex flex-col">
                  <div class="flex justify-between">
                    <p :class="selected ? 'font-semibold' : 'font-normal'">{{ option.title }}</p>
                    <span v-if="selected" :class="active ? 'text-white' : 'text-gray-600'">
                      <CheckIcon class="size-5" aria-hidden="true" />
                    </span>
                  </div>
                  <!-- <p :class="[active ? 'text-gray-200' : 'text-gray-500', 'mt-2']">{{ option.description }}</p> -->
                </div>
              </li>
            </ListboxOption>
          </ListboxOptions>
        </transition>
      </div>
    </Listbox>
  </template>
  
