<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>
<template>
    <button class="bg-black text-white w-full p-3 font-bold" :type="type">
        <slot />
    </button>
</template>