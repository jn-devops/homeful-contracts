<script setup>
// import Loading from '@/Components/Animation/Loading.vue';
import PlainBlackButton from '@/Components/Button/PlainBlackButton.vue';
import WarningToast from '@/Components/Toast/WarningToast.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reference: String,
});

const form = useForm({
    reference: props.reference,
});

const warningToast = ref(false);

const submit = () => {
    console.log("Submit");
    form.post(route('post-manual-onboard'), {
        onSuccess: () => {

        },
        onError: () => {
            warningToast.value = true;

        },
    });
};

</script>

<template>
<!-- Manual Onboard here
    check if state is verified if not, wait
    if verified,
    continue
    form.post(route('post-manual-onboard'), {
    }); -->
    <DefaultLayout :hide-header="true">
        <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
        >
            <WarningToast 
                v-if="warningToast"
                v-model:show="warningToast"
                message="Please wait..."
            />
        </Transition>
        <div class="relative p-4 pt-56">
            <div class="text-center">
                <form @submit.prevent="submit">
                    <div class="w-full flex flex-col justify-center items-center mb-28">
                        <div class="w-32 ">
                            <!-- <Loading /> -->
                        </div>
                        Please wait while we verify your account
                    </div>

                    <PlainBlackButton type="submit">Refresh</PlainBlackButton>
                </form>
            </div>
        </div>
    </DefaultLayout>
</template>

<style scoped>

</style>
