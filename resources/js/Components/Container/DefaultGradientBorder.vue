<script setup>
const props = defineProps({
  hasError: {
    type: Boolean,
    default: false,
  },
});
</script>
<template>
    <div 
        class="w-full bg-black focus-within:bg-gradient-to-b focus-within:from-[#F7C947] focus-within:to-[#E94572] p-[2px] mb-0 "
        :class="hasError ? 'bg-red-500' : ''"
    >
        <div class=" w-full bg-white p-1">
            <slot />
        </div>
    </div>
</template>