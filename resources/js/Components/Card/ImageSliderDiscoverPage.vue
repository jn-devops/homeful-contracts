<script setup>
import { usePage } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref, watch } from 'vue';

const emit = defineEmits(['updateCurrentImg'])

const props = defineProps({
    imgList: {
        type: Array,
        default: null
    },
    currentImgIndex: {
        type: Number,
        default: null
    }
})
const currentIndex = ref(props.currentImgIndex);
const divRefs = ref([]);
const intervalTime = 5000
let interval

const changeImg = (newImgIndex) => {
    currentIndex.value = newImgIndex
    emit('updateCurrentImg', newImgIndex)
}

const manualChange = (newImgIndex) => {
    resetInterval()
    changeImg(newImgIndex)
    interval = setInterval(nextSlide, intervalTime)
    scrollToCurrent()
}

const nextSlide = () =>{
    let firstIndex = 0
    let lastIndex = props.imgList.length - 1
    let newIndex = currentIndex.value
    if(currentIndex.value >= firstIndex && currentIndex.value < lastIndex){
        newIndex = currentIndex.value + 1
    }else{
        newIndex = firstIndex
    }
    changeImg(newIndex)
    scrollToCurrent()

}
const previousSlide = () =>{
    let firstIndex = 0
    let lastIndex = props.imgList.length - 1
    let newIndex = currentIndex.value
    if(currentIndex.value > firstIndex && currentIndex.value <= lastIndex){
        newIndex = currentIndex.value - 1
    }else{
        newIndex = lastIndex
    }
    changeImg(newIndex)
    scrollToCurrent()
}

const resetInterval = () => {
    if (interval) clearInterval(interval)
}

function scrollToCurrent() {
    const currentDiv = divRefs.value[currentIndex.value];
    if (currentDiv) {
        currentDiv.scrollIntoView({
        behavior: "smooth",
        inline: 'center', 
        block: 'nearest'
        });
        currentDiv.focus();
    }
}

const targetDiv = ref(null);
let observer = null; 

const handleIntersect = (entries) => {
  if (entries[0].isIntersecting) {
    // Target div is in view or has passed!
  } else {
    // Target div is out of view.
    resetInterval()
  }
};



onMounted(() => {
    resetInterval()
    interval = setInterval(nextSlide, intervalTime)

    observer = new IntersectionObserver(handleIntersect, {
        root: null, // Observe within the viewport
        threshold: 0, // Trigger as soon as any part is visible
    });

    if (targetDiv.value) {
        observer.observe(targetDiv.value);
    }

});

onUnmounted(() => {
  clearInterval(interval);
  if (observer && targetDiv.value) {
    observer.unobserve(targetDiv.value);
  }
});

</script>
<template>
    <div class="w-full bg-black text-white">
        <div ref="targetDiv"></div>
        <div class="flex justify-center items-center px-4">
            <div class="basis-4/6 leading-none">
                <p class="text-xs font-semibold">House & Lot</p>
                <p class="text-3xl font-bold">Agapeya Town</p>
                <p class="text-base font-semibold">Calamba, Laguna</p>
            </div>
            <div class="basis-2/6">
                <div class="flex gap-4 justify-center">
                    <div class="text-white bg-gray-700 p-1" @click="previousSlide">
                        <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                        </svg>
                    </div>
                    <div class="text-white bg-gray-700 p-1" @click="nextSlide">
                        <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-container overflow-x-auto overflow-y-hidden whitespace-nowrap mt-2 px-4 pb-3">
            <div class="flex space-x-3 min-w-max">
                <div 
                    class="cursor-pointer w-[100px]" 
                    v-for="(item, index) in imgList" 
                    :key="index" 
                    @click="manualChange(index)"
                    :ref="el => (divRefs[index] = el)"
                >
                    <div class="w-full h-[70px]">
                        <img :src="item.imgLink" class="w-full h-full object-cover rounded-md" :class="{'border-2 border-[#CC035C]': currentIndex == index}">
                    </div>
                    <p class="text-xs font-semibold" :class="{'text-[#CC035C]': currentIndex == index}">{{ item.description }}</p>
                </div>
            </div>
        </div>
    </div>
</template>