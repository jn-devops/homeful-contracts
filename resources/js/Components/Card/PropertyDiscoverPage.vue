<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import ImageSliderDiscoverPage from './ImageSliderDiscoverPage.vue'
import InputTextVoucherCode from '../Input/InputTextVoucherCode.vue'
import PrimaryButton from '../Button/PrimaryButton.vue'

const props = defineProps({
    discoverPage: {
        type: Boolean,
        default: false
    },
    voucherCode: {
        type: String,
        default: ''
    },
    submitEvent: Function,
})

const elanvitalLogo = ref(usePage().props.data.appLink + '/logos/elanvital_logo.png')
const voucher_code = ref(props.voucherCode)

const emit = defineEmits(['update:discoverPage', 'update:voucherCode'])

const localDiscoverPage = ref(props.discoverPage)

watch(() => props.discoverPage, (newVal) => {
    localDiscoverPage.value = newVal
})

const toggleDiscoverPage = () => {
    localDiscoverPage.value = !localDiscoverPage.value
    emit('update:discoverPage', localDiscoverPage.value)
}

const imgList = ref([
    {imgLink: usePage().props.data.appLink + '/images/PropertyDiscoverImg.png', description: 'Sample'},
    {imgLink: 'https://jn-img.enclaves.ph/Everyhome/Pagsibol%20Village%20Magalang%20Pampanga/pagsibol-village-magalang-pampanga-facade.png?updatedAt=1726545316360', description: 'Sample2'},
    {imgLink: usePage().props.data.appLink + '/images/PropertyDiscoverImg.png', description: 'Sample1'},
    {imgLink: 'https://picsum.photos/id/237/200/300', description: 'Sample3'},
    {imgLink: 'https://images.unsplash.com/2/08.jpg?q=80&w=2500&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', description: 'Sample4'},
    {imgLink: usePage().props.data.appLink + '/images/PropertyDiscoverImg.png', description: 'Sample5'},
    {imgLink: usePage().props.data.appLink + '/images/PropertyDiscoverImg.png', description: 'Sample6'},
])

const currentImg = ref(
    imgList.value.length === 0
        ? {imgLink: usePage().props.data.appLink + '/images/sample1.png'}
        : imgList.value[0]
);
const currentImgIndex = ref(
    imgList.value.length === 0
        ? null
        : 0
)

const updateCurrentImg = (newIndex) => {
    currentImg.value = imgList.value[newIndex]
}

watch(() => voucher_code.value, (newVal) => {
    emit('update:voucherCode', newVal)
})  

</script>
<template>
    <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-10">
        <div class="bg-white w-full max-w-[450px] h-screen overflow-y-auto rounded shadow-lg">
            <!-- Content -->
            <div :style="{ backgroundImage: `url(${currentImg.imgLink})` }" class="bg-cover bg-center h-80 w-full flex flex-col justify-end">
                <div class="h-full pt-20 ps-5 cursor-pointer" @click="toggleDiscoverPage">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                    </svg>
                </div>
                <div class="inset-0 bg-gradient-to-t text-white from-black to-transparent opacity-100 h-28 w-full bottom-0 flex items-end  px-4">
                </div>
            </div>
            <ImageSliderDiscoverPage
                :imgList="imgList"
                @updateCurrentImg="updateCurrentImg"
                :currentImgIndex="currentImgIndex"
            />
            <div class="p-6">
                <div class="flex gap-4">
                    <div>
                        <p class="text-gray-500 text-xs">Starts at</p>
                        <p class="font-extrabold text-xl">₱2,850,000</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Total Sold</p>
                        <p class="font-extrabold text-xl">200+</p>
                    </div>
                    <div class="flex-auto flex flex-row-reverse">
                        <div class="rounded-full shadow-xl w-[90px] px-3 flex justify-center items-center">
                            <img :src="elanvitalLogo" class="w-full">
                        </div>
                    </div>
                </div>
                <div class="border-2 border-gray-700 mt-4 p-3">
                    <h2 class="text-base font-bold">Home Match Qualification:</h2>
                    <ul class="px-2 text-sm">
                        <li class="flex items-center gap-2 my-2 text-gray-600">
                            <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                            </svg>
                            Gross Monthly Income: P100,000
                        </li>
                        <li class="flex items-center gap-2 my-2 text-gray-600">
                            <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                            </svg>
                            Age: 30 years old
                        </li>
                        <li class="flex items-center gap-2 my-2 text-gray-600">
                            <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                            </svg>
                            Monthly Amortization: ₱17,144 
                        </li>
                        <li class="flex items-center gap-2 my-2 text-gray-600">
                            <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                            </svg>
                            Years to Pay: 30 years 
                        </li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-base font-bold underline mt-5 mb-7">Project Introduction</h5>
                    <p class="leading-none text-sm pb-5">Agapeya Towns features 2-storey duplex units with a practical floor area of 50sqm, ideally constructed on a typical lot area of 70sqm.</p>
                    <p class="leading-none text-sm pb-5">Each unit consists of 3 bedrooms, 1 toilet and bath, and is thoughtfully designed to include 2 carport provisions, ensuring convenience and functionality for residents.</p>
                    <p class="leading-none text-sm pb-5">Its efficient use of space and modern design make Agapeya a compelling choice for individuals or families seeking affordable yet stylish housing solutions.</p>

                </div>
                <div class="mb-4">
                    <h5 class="text-base font-bold underline mt-5 mb-7">House Features</h5>
                    <div class="border-2 border-gray-700 mt-3 p-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex gap-2 text-sm items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                    <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                                    <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                                </svg>
                                <span>House Type:</span>
                            </div>
                            <div class="text-sm font-bold">
                                Residential House & Lot 2-Storey Duplex
                            </div>
                            <div class="flex gap-2 text-sm items-center">
                                <svg class="size-4" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.194336 0.194824V12.7386H12.7381V0.194824H8.2584V0.79222H12.1407V6.76514H10.0501V7.36253H12.1407V12.1412H6.16777V7.36253H8.2584V6.76514H3.47975V7.36253H5.57038V12.1412H0.791732V0.79222H4.57454L6.88444 2.52451L7.24277 2.04639L4.77402 0.194824H0.194336Z" fill="#1F2024"/>
                                </svg>
                                <span>House/Floor Area:</span>
                            </div>
                            <div class="text-sm font-bold">
                                50 sqm
                            </div>
                            <div class="flex gap-2 text-sm items-center">
                                <svg class="size-4" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.492773 0.298462C0.32819 0.298462 0.194336 0.432316 0.194336 0.59742V1.49325C0.194336 1.52034 0.197461 1.5469 0.204752 1.57294L0.575065 2.91357L0.855794 5.09482C0.856315 5.09898 0.856836 5.10315 0.857357 5.10732L1.2834 7.6094C1.29173 7.65992 1.31361 7.70732 1.3459 7.74742L1.68756 8.16982V8.51513C1.68756 8.62867 1.75215 8.73232 1.85423 8.78284L1.986 8.84742V8.85732L1.58184 9.37555C1.55423 9.41044 1.53496 9.45159 1.52559 9.49534L1.3959 10.0912C1.38131 10.1578 1.39017 10.2271 1.42038 10.288L1.66934 10.787L1.39746 11.8745C1.37246 11.9761 1.40215 12.0839 1.47663 12.1578L1.68756 12.3688V13.1412C1.68756 13.2203 1.71881 13.2964 1.77506 13.3526L2.0735 13.6511C2.12611 13.7037 2.19642 13.7349 2.2709 13.738L8.54277 14.037C8.54746 14.037 8.55215 14.0375 8.55684 14.0375H8.85579C8.85579 14.1167 8.88704 14.1927 8.94329 14.2485L9.24173 14.5474C9.30944 14.6151 9.40579 14.6464 9.50059 14.6308C9.59538 14.6157 9.67715 14.5552 9.71986 14.4698L10.0188 13.8724C10.061 13.788 10.061 13.6891 10.0188 13.6052L9.82194 13.212L10.2152 13.4084C10.2568 13.4292 10.3027 13.4401 10.349 13.4401H11.2449C11.41 13.4401 11.5433 13.3063 11.5433 13.1412V12.2823L11.8251 11.1552L12.1094 10.587C12.1303 10.5453 12.1407 10.4995 12.1407 10.4531V10.2453L12.6881 9.4245C12.7209 9.37555 12.7381 9.31773 12.7381 9.25888V8.95992C12.7381 8.79534 12.6042 8.66148 12.4396 8.66148H12.0657L11.9016 8.10263C11.8896 8.06096 11.8688 8.02294 11.8407 7.99065L11.5662 7.67502C11.5271 7.63023 11.4756 7.59794 11.4183 7.58284L11.2162 7.52867L11.1516 7.03336C11.149 7.01461 11.1449 6.99586 11.1386 6.97763L10.8574 6.13492C10.835 6.06721 10.7891 6.00992 10.7282 5.97346L10.2292 5.6745L10.0412 5.46044L9.92454 5.14638C9.91204 5.112 9.89381 5.08075 9.86986 5.05367L9.61517 4.76252C9.60006 4.74534 9.5834 4.73023 9.56465 4.71669L9.32663 4.5443L9.0959 4.17138C9.08132 4.14794 9.06361 4.12711 9.04329 4.10836L8.66308 3.75888C8.64382 3.74169 8.62246 3.72659 8.59954 3.71461L8.13861 3.47398L8.12402 3.40836C8.11621 3.3719 8.10163 3.337 8.08079 3.30575L7.82819 2.92659L7.79694 2.78023C7.79017 2.74898 7.77871 2.7193 7.76256 2.69221L7.32142 1.93961C7.26777 1.84794 7.16934 1.79221 7.06361 1.79221H6.54277L6.28079 1.64898L6.16777 1.42294V1.35471L6.25788 1.24273L6.52454 1.18909C6.66413 1.16096 6.76465 1.03857 6.76465 0.895858V0.59742C6.76465 0.432316 6.63131 0.298462 6.46621 0.298462H0.492773ZM0.791732 0.895858H5.76986L5.636 1.06305C5.59381 1.11565 5.57038 1.1818 5.57038 1.2495V1.49325C5.57038 1.53961 5.58079 1.58544 5.60163 1.62711L5.78861 2.00002C5.81569 2.05471 5.85892 2.0995 5.91256 2.12867L6.3235 2.35263C6.36725 2.37659 6.41621 2.38909 6.46621 2.38961H6.89277L7.2235 2.95263L7.25684 3.10784C7.26465 3.14482 7.27923 3.17971 7.29954 3.21044L7.55215 3.58909L7.58392 3.73648C7.60267 3.82294 7.65892 3.8969 7.73704 3.93805L8.28808 4.22555L8.60944 4.52034L8.84954 4.90888C8.87038 4.94221 8.89746 4.97086 8.92923 4.99377L9.18756 5.18127L9.38392 5.40523L9.50059 5.71982C9.51361 5.75367 9.53236 5.78492 9.55631 5.81252L9.811 6.10367C9.83131 6.12711 9.85527 6.14742 9.88236 6.16357L10.3261 6.42919L10.5631 7.13961L10.6501 7.80367C10.6652 7.92398 10.7516 8.02294 10.8688 8.05419L11.1756 8.13596L11.3469 8.33284L11.5558 9.04377C11.5933 9.17138 11.7094 9.25836 11.8423 9.25888H12.0808L11.5938 9.98909C11.561 10.038 11.5433 10.0959 11.5433 10.1547V10.3828L11.2766 10.9172C11.2667 10.9365 11.2589 10.9573 11.2537 10.9782L10.9548 12.1729C10.949 12.1969 10.9464 12.2209 10.9464 12.2453V12.8427H10.4193L9.88496 12.5755C9.77038 12.5183 9.63131 12.5407 9.54017 12.6313L9.24173 12.9302C9.15059 13.0209 9.12819 13.1599 9.18548 13.275L9.28288 13.4698C9.24277 13.4505 9.19902 13.4401 9.15423 13.4401H8.56413L2.41413 13.1474L2.28496 13.0177V12.2453C2.28496 12.1662 2.25319 12.0901 2.19746 12.0344L2.01829 11.8552L2.27611 10.8245C2.29329 10.7552 2.28548 10.6823 2.25319 10.6183L2.00215 10.1157L2.09486 9.68909L2.52038 9.14377C2.56152 9.09117 2.5834 9.02659 2.5834 8.95992V8.66148C2.58392 8.54742 2.51882 8.44325 2.41673 8.39325L2.28496 8.32867V8.06409C2.28496 7.99586 2.26152 7.92919 2.21829 7.87607L1.85892 7.43232L1.44746 5.01252L1.16465 2.81669C1.16309 2.80315 1.16048 2.78909 1.15684 2.77555L0.791732 1.45315V0.895858Z" fill="#1F2024"/>
                                </svg>
                                <span>Lot Area:</span>
                            </div>
                            <div class="text-sm font-bold">
                                70 sqm
                            </div>
                            <div class="flex gap-2 text-sm items-center">
                                <svg class="size-4" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.03141 0.125C1.25884 0.125 0.625163 0.758681 0.625163 1.53125V4.1875C0.625163 4.19184 0.625163 4.19672 0.625163 4.20106C0.26926 4.45714 0.00016276 4.8125 0.00016276 5.28125V8.51259C-0.00526259 8.54622 -0.00526259 8.5804 0.00016276 8.6135V9.8125C-0.00146485 9.92535 0.0576714 10.0301 0.155328 10.087C0.252441 10.1434 0.372884 10.1434 0.469998 10.087C0.567654 10.0301 0.62679 9.92535 0.625163 9.8125V8.875H13.7502V9.8125C13.7485 9.92535 13.8077 10.0301 13.9053 10.087C14.0024 10.1434 14.1229 10.1434 14.22 10.087C14.3177 10.0301 14.3768 9.92535 14.3752 9.8125V8.61241C14.3806 8.57878 14.3806 8.5446 14.3752 8.5115V5.28125C14.3752 4.8125 14.1061 4.45714 13.7502 4.20106C13.7502 4.19672 13.7502 4.19184 13.7502 4.1875V1.53125C13.7502 0.758681 13.1165 0.125 12.3439 0.125H8.28141C7.80018 0.125 7.44157 0.410373 7.18766 0.781467C6.93376 0.410373 6.57514 0.125 6.09391 0.125H2.03141ZM2.03141 0.75H6.09391C6.52848 0.75 6.87516 1.09668 6.87516 1.53125V3.875H1.40641C1.34565 3.875 1.30876 3.93197 1.25016 3.93956V1.53125C1.25016 1.09668 1.59684 0.75 2.03141 0.75ZM8.28141 0.75H12.3439C12.7785 0.75 13.1252 1.09668 13.1252 1.53125V3.93956C13.0666 3.93197 13.0297 3.875 12.9689 3.875H7.50016V1.53125C7.50016 1.09668 7.84684 0.75 8.28141 0.75ZM1.40641 4.5H7.13775C7.14534 4.50163 7.15294 4.50271 7.16054 4.5038C7.18712 4.50597 7.21316 4.50434 7.23866 4.5H12.9689C13.4035 4.5 13.7502 4.84668 13.7502 5.28125V8.25H0.625163V5.28125C0.625163 4.84668 0.971842 4.5 1.40641 4.5Z" fill="#1F2024"/>
                                </svg>
                                <span>Features:</span>
                            </div>
                            <div class="text-sm font-bold">
                                3 Bedrooms <br />
                                1 Toilet and Bath <br />
                                1 Carport plus provision <br />
                            </div>
                        </div>
                    </div>
                </div>
                <InputTextVoucherCode 
                    v-model="voucher_code"
                    label="Voucher Code"
                    placeholder="ex. H98K28"
                    helper-message="If you are a seller's assistant, please enter the seller voucher code. If not, kindly click the Book Now button below."
                    :max="8"
                />
                <div class="mt-5"></div>
                <PrimaryButton @click="submitEvent">
                    Book Now
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>