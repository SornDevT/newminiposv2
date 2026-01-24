<template>
    <dialog ref="dialogRef" class="modal">
        <div class="modal-box p-0 overflow-hidden">
            <!-- Header -->
            <div :class="headerClasses">
                <h3 class="font-bold text-lg text-white">{{ dialogStore.title }}</h3>
                <button
                    v-if="dialogStore.type !== 'confirm'"
                    @click="dialogStore.hide()"
                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 text-white"
                >
                    ✕
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 text-center text-gray-700">
                <div v-if="dialogStore.type === 'info'" class="text-blue-500 mb-4">
                    <InformationCircleIcon class="w-12 h-12 mx-auto" />
                </div>
                <div v-else-if="dialogStore.type === 'warning'" class="text-orange-500 mb-4">
                    <ExclamationTriangleIcon class="w-12 h-12 mx-auto" />
                </div>
                <div v-else-if="dialogStore.type === 'success'" class="text-green-500 mb-4">
                    <CheckCircleIcon class="w-12 h-12 mx-auto" />
                </div>
                <div v-else-if="dialogStore.type === 'confirm'" class="text-yellow-500 mb-4">
                    <QuestionMarkCircleIcon class="w-12 h-12 mx-auto" />
                </div>

                <p class="py-4">{{ dialogStore.message }}</p>
            </div>

            <!-- Footer -->
            <div class="modal-action justify-center border-t border-gray-200 pt-4 pb-4">
                <button
                    v-if="dialogStore.type === 'confirm'"
                    @click="dialogStore.confirmCancel()"
                    class="btn btn-ghost"
                >
                    ຍົກເລີກ
                </button>
                <button
                    :class="buttonClasses"
                    @click="handleAccept()"
                >
                    {{ dialogStore.type === 'confirm' ? 'ຕົກລົງ' : 'ເຂົ້າໃຈແລ້ວ' }}
                </button>
            </div>
        </div>
    </dialog>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useDialogStore } from '../stores/dialog';
import {
    InformationCircleIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    QuestionMarkCircleIcon
} from '@heroicons/vue/24/outline';

const dialogStore = useDialogStore();
const dialogRef = ref(null);

watch(() => dialogStore.isVisible, (newVal) => {
    if (newVal) {
        dialogRef.value.showModal();
    } else {
        dialogRef.value.close();
    }
});

const headerClasses = computed(() => {
    let base = 'px-6 py-4 text-white flex justify-between items-center';
    switch (dialogStore.type) {
        case 'info': return base + ' bg-blue-500';
        case 'warning': return base + ' bg-orange-500';
        case 'success': return base + ' bg-green-500';
        case 'confirm': return base + ' bg-yellow-500';
        default: return base + ' bg-gray-500';
    }
});

const buttonClasses = computed(() => {
    let base = 'btn';
    switch (dialogStore.type) {
        case 'info': return base + ' btn-info';
        case 'warning': return base + ' btn-warning';
        case 'success': return base + ' btn-success';
        case 'confirm': return base + ' btn-primary';
        default: return base + ' btn-neutral';
    }
});

const handleAccept = () => {
    if (dialogStore.type === 'confirm') {
        dialogStore.confirmAccept();
    } else {
        dialogStore.hide();
    }
};
</script>

<style scoped>
/* You can add custom styles here if needed */
</style>
