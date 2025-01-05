<script setup>

import PrimaryButton from '@/Components/PrimaryButton.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import ButtonOptions from '@/Components/ButtonOptions.vue';
import {router, useForm, usePage} from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import {computed, ref, watch} from "vue";

const props = defineProps({
    input: {
        type: String,
        default: null,
    },
    context: {
        type: Array,
        default: [],
    },
    option: {
        type: String,
        default: null,
    },
    title: {
        type: String,
        default: null,
    },
    description: {
        type: Object,
        default: null,
    },
    action: {
        type: String,
        default: 'Go',
    },
    buttonOptions: {
        type: [Object, Boolean],
        default: [],
    }
});

const form = useForm({
    input: props.input,
    context: props.context,
    option: props.option,
});

const updateCommand = () => {
    form.get(route('inputs.create'), {
        errorBag: 'updateCommand',
        preserveScroll: true,
    });
};

const sendCommand = () => {
    form.post(route('inputs.store'), {
        errorBag: 'sendCommand',
        preserveScroll: true,
    });
};

const getDescription = () => {
    return props.description;
}

</script>

<template>
    <FormSection @submitted="sendCommand">
        <template #title>
            {{ props.title }}
        </template>
        <template #description>
            <template v-for="(value, key) in props.description">
                <div>
                    {{ key }}: {{ value }}
                </div>
            </template>
        </template>
        <template #form>
            <div class="col-span-6 lg:col-span-4">
                <ButtonOptions :options="buttonOptions" v-model:option="form.option"/>
<!--                <div>{{ form.input }}</div>-->
<!--                <div>{{ form.context }}</div>-->
<!--                <div>{{ form.option }}</div>-->
            </div>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Done.
            </ActionMessage>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ action }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>
