<script setup>
import { inject, onMounted, ref, toRaw } from 'vue';


const props = defineProps({
    lists: {
        type: Array,
        default: []
    },
    label: {
        type: String,
        default: null
    },
    listSelected: {
        type: Array,
        default: []
    },
})

const emit = defineEmits(['update:listSelected']) 
const selectedAll = ref(arraysAreEqual(props.lists, props.listSelected))

const select = (item) => {
    if(props.listSelected.some(sel => sel.id === item.id)){
        emit('update:listSelected', props.listSelected.filter(opt => opt.id !== item.id))
    }else{
        props.listSelected.push(item)
    }
}

const selectAll = () => {
    if(selectedAll.value){
        selectedAll.value = false
        emit('update:listSelected', [])
    }else{
        emit('update:listSelected', props.lists)
        selectedAll.value = true
    }

}

function arraysAreEqual(arr1, arr2) {
    if (arr1.length !== arr2.length) return false;

    // Sort both arrays
    const sortedArr1 = [...arr1].sort();
    const sortedArr2 = [...arr2].sort();

    // Compare elements
    return sortedArr2.every((value, index) => toRaw(value) === toRaw(sortedArr1[index]));
}

onMounted(() => {
})

</script>
<template>
    <div class="px-6">
        <div class="flex flex-row ">
            <label for="" class="text-sm font-semibold basis-5/6">{{ label }}</label>
            <div class="basis-1/6">
                <div class="w-fit bg-black text-white px-2 py-1 rounded-full text-xs font-bold mx-auto">
                    {{ listSelected.length }}
                </div>
            </div>
        </div>
        <div class="flex gap-2 mt-2 flex-wrap">
            <div 
                class="bg-[#EAF2FF] text-black w-fit text-xs px-3 py-1 pt-2 rounded-full uppercase font-semibold cursor-pointer leading-none"
                :class="{'bg-black text-white' : selectedAll}"
                @click="selectAll"
            >
                All
            </div>
            <div 
                class="bg-[#EAF2FF] text-black w-fit text-xs px-3 py-2  rounded-full uppercase font-semibold cursor-pointer leading-none"
                :class="{'bg-black text-white' : listSelected.some(opt => opt.id === item.id)}"
                v-for="(item, index) in lists"
                :key="index"
                @click="select(item)"
            >
                {{ item.name }}
            </div>
        </div>
    </div>
</template>