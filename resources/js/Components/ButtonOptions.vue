<script setup>
import {computed, ref} from 'vue';

const emit = defineEmits(['update:option']);

const props = defineProps({
    options: {
        type: [Object, Boolean],
        default: false,
    },
    option: {
        type: String,
        default: null,
    }
});

const proxyChecked = computed({
    get() {
        return props.option;
    },
    set(val) {
        emit('update:option', val)
    },
});
</script>

<template>
    <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer">
        <button
            v-for="(opt, i) in options"
            :key="opt.key"
            type="button"
            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
            :class="{'border-t border-gray-200 dark:border-gray-700 focus:border-none rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(options).length - 1}"
            @click="proxyChecked = opt.key"
        >
            <div :class="{'opacity-50': proxyChecked && proxyChecked != opt.key}">
                <!-- Role Name -->
                <div class="flex items-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400" :class="{'font-semibold': proxyChecked == opt.key}">
                        {{ JSON.parse(opt.name).name }}
                    </div>

                    <svg v-if="proxyChecked == opt.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Role Description -->
                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 text-left">
                    Loan Amortization : {{ JSON.parse(opt.description).loan_amortization }}
                </div>
            </div>
        </button>
    </div>
</template>
