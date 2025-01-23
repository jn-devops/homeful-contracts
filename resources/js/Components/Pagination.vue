<template>
    <div class="mt-4 flex items-center justify-center py-3 bg-gray-50 border border-gray-200 rounded">
        <div class="inline-flex flex-wrap items-center space-x-1">
            <template v-for="(link, key) in props.links" :key="key">
                <div
                    v-if="link.url === null"
                    class="px-4 py-2 text-sm text-gray-400 border border-transparent rounded cursor-default"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    class="px-4 py-2 text-sm leading-4 border rounded hover:bg-white focus:outline-none focus:ring focus:ring-indigo-200 transition ease-in-out duration-150"
                    :class="{
            'bg-indigo-600 text-white hover:bg-indigo-700 border-indigo-600': link.active,
            'text-gray-700 bg-gray-100 border-gray-300 hover:border-gray-400': !link.active
          }"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    links: {
        type: Array,
        default: () => [],
    },
})
</script>
