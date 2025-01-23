<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue'
import NavLink from "@/Components/NavLink.vue";
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    contracts: Object
});
</script>

<template>
    <Head title="Contracts" />

    <AuthenticatedLayout>
        <!-- Header for the page -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Contracts
            </h2>
        </template>

        <!-- Main Content -->
        <div class="py-12">
            <!-- Center the main content and apply spacing for different breakpoints -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Table Container -->
                <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Table Header -->
                        <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <th scope="col" class="px-6 py-3">Field 1</th>
                            <th scope="col" class="px-6 py-3">Field 2</th>
                            <th scope="col" class="px-6 py-3">Field 3</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-gray-200 bg-white text-sm">
                        <!-- Loop through the projects data -->
                        <tr
                            v-for="contract in contracts.data"
                            :key="contract.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100"
                        >
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">
                                {{ contract.name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                                {{ contract.location }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                                {{ contract.seller_commission_code }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <!-- Example action link -->
                                <NavLink
                                    :href="route('contracts.edit', contract.id)"
                                    class="text-indigo-500 hover:text-indigo-700"
                                >
                                    Edit
                                </NavLink>
                                <NavLink
                                    :href="route('contracts.show', contract.id)"
                                    class="text-indigo-500 hover:text-indigo-700"
                                >
                                    Delete
                                </NavLink>
                            </td>
                        </tr>
                        <!-- If there are no projects -->
                        <tr v-if="contracts.data.length === 0">
                            <td class="px-6 py-4 text-center text-gray-500" colspan="4">
                                No contracts found.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <pagination :links="contracts.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
