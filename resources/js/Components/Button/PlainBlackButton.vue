<script setup>
const props = defineProps({
    type: {
        type: String,
        default: "button"
    },
    disabled: {
        type: Boolean,
        default: false
    }
})
</script>
<template>
    <button :type="type" :disabled="disabled" class="bg-black text-white w-full p-3 font-bold" :class="disabled ? 'bg-gray-700' : 'bg-black'">
        <slot />
    </button>
</template>