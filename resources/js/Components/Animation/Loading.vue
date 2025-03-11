<script setup>
import { onMounted, ref } from "vue";
import lottie from "lottie-web";
import LoadingAnimation from "@/../Assets/Loading.json";
const lottieContainer = ref(null);

onMounted(() => {
  lottie.loadAnimation({
    container: lottieContainer.value,
    renderer: "svg",
    loop: true,
    autoplay: true,
    animationData: LoadingAnimation,
  });
});
</script>

<style scoped>
.lottie-animation {
  width: 300px;
  height: 300px;
}
</style>

<template>
    <div ref="lottieContainer" class="w-full"></div>
</template>