<script setup>
import { ref, onMounted } from 'vue';

const isVisible = ref(true);

onMounted(() => {
  setTimeout(() => {
    isVisible.value = false;
  }, 4000);
});
</script>
<template>
    <!-- Toast Notification -->
    <transition
        enter-active-class="transition-all duration-300 ease-in"
        enter-from-class="opacity-0 transform scale-75"
        enter-to-class="opacity-100 transform scale-100"
        leave-active-class="transform transition-transform duration-300 ease-in-out"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div class="p-4 w-full flex flex-row-reverse" v-if="isVisible">
            <div class="bg-[#E7F4E8] rounded-xl w-96 px-4 py-3 flex flex-row items-center gap-3 shadow-xl">
                <div class="flex-none">
                    <svg class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="grow flex flex-col">
                    <div class="font-bold text-sm">
                        <slot name="title" />
                    </div>
                    <div class="text-xs">
                        <slot name="description" />
                    </div>
                </div>
            </div>        
        </div>
    </transition>
</template>