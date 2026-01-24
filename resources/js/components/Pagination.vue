<template>
    <div class="flex justify-center mt-4" v-if="lastPage > 1">
        <div class="join">
            <button class="join-item btn" @click="goToPage(1)" :disabled="currentPage === 1">«</button>
            <button class="join-item btn" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">‹</button>

            <button
                v-for="page in pages"
                :key="page"
                :class="['join-item', 'btn', { 'btn-active': page === currentPage }]"
                @click="goToPage(page)"
            >
                {{ page }}
            </button>

            <button class="join-item btn" @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage">›</button>
            <button class="join-item btn" @click="goToPage(lastPage)" :disabled="currentPage === lastPage">»</button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    lastPage: {
        type: Number,
        required: true
    },
    range: {
        type: Number,
        default: 2 // Number of page links to show on either side of the current page
    }
});

const emit = defineEmits(['page-change']);

const goToPage = (page) => {
    if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
        emit('page-change', page);
    }
};

const pages = computed(() => {
    const pagesArray = [];
    let start = props.currentPage - props.range;
    let end = props.currentPage + props.range;

    if (start < 1) {
        end += (1 - start);
        start = 1;
    }

    if (end > props.lastPage) {
        start -= (end - props.lastPage);
        end = props.lastPage;
    }

    if (start < 1) { // Ensure start is not less than 1 after adjustment
        start = 1;
    }

    for (let i = start; i <= end; i++) {
        pagesArray.push(i);
    }
    return pagesArray;
});
</script>

<style scoped>
/* Add any specific styles for pagination if needed */
</style>