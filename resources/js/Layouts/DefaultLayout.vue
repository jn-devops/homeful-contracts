<script setup>
import DefaultHeader from '@/Components/DefaultHeader.vue';

const props = defineProps({
    hideHeader: {
        type: Boolean,
        default: false,
    }
})

</script>

<template>
    <DefaultHeader v-if="!hideHeader" />
    <div class="min-h-screen flex flex-col items-center">
        <div class="w-full md:w-[450px]">
            <main class="">
                <slot />
            </main>
        </div>
    </div>
</template>