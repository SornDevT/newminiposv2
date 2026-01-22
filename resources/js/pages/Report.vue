<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold mb-4">ລາຍງານ</h1>
                
                <!-- Date Range Selection -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="label">
                            <span class="label-text">ວັນທີ່ເລີ່ມຕົ້ນ</span>
                        </label>
                        <input 
                            type="date" 
                            v-model="startDate"
                            class="input input-bordered w-full" 
                        />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ວັນທີ່ສິ້ນສຸດ</span>
                        </label>
                        <input 
                            type="date" 
                            v-model="endDate"
                            class="input input-bordered w-full" 
                        />
                    </div>
                    <div class="flex items-end">
                        <button 
                            @click="generateReport" 
                            class="btn btn-primary w-full"
                        >
                            ສ້າງລາຍງານ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Income Card -->
                <div class="card bg-success/10 border border-success">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success">ລາຍຮັບ</h3>
                        <p class="text-3xl font-bold text-success">{{ formatCurrency(totalIncome) }}</p>
                        <p class="text-sm text-gray-600">ລາຍຮັບທັງຫມົດ</p>
                    </div>
                </div>

                <!-- Expense Card -->
                <div class="card bg-error/10 border border-error">
                    <div class="card-body p-4">
                        <h3 class="card-title text-error">ລາຍຈ່າຍ</h3>
                        <p class="text-3xl font-bold text-error">{{ formatCurrency(totalExpense) }}</p>
                        <p class="text-sm text-gray-600">ລາຍຈ່າຍທັງຫມົດ</p>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div>
                <h2 class="text-xl font-bold mb-4">ລາຍລະອຽດລາຍງານ</h2>
                <div class="overflow-x-auto">
                    <table class="table border border-gray-300">
                        <thead class="bg-base-200">
                            <tr>
                                <th>#</th>
                                <th>ວັນທີ່</th>
                                <th>ລາຍລະອຽດ</th>
                                <th>ປະເພດ</th>
                                <th>ຈຳນວນ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="reportData.length === 0">
                                <td colspan="5" class="text-center py-8 text-gray-400">
                                    ບໍ່ມີຂໍ້ມູນ
                                </td>
                            </tr>
                            <tr v-for="(item, index) in reportData" :key="index">
                                <th>{{ index + 1 }}</th>
                                <td>{{ formatDate(item.date) }}</td>
                                <td>{{ item.description }}</td>
                                <td>
                                    <span 
                                        :class="item.type === 'income' ? 'badge badge-success' : 'badge badge-error'"
                                    >
                                        {{ item.type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ' }}
                                    </span>
                                </td>
                                <td :class="item.type === 'income' ? 'text-success font-bold' : 'text-error font-bold'">
                                    {{ formatCurrency(item.amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const startDate = ref('');
const endDate = ref('');
const reportData = ref([]);

// Sample data - replace with actual API call
const sampleData = [
    { date: '2026-01-20', description: 'ຂາຍເບຍ', type: 'income', amount: 50000 },
    { date: '2026-01-20', description: 'ຊື້ສິນຄ້າ', type: 'expense', amount: 30000 },
    { date: '2026-01-21', description: 'ຂາຍເຄື່ອງດື່ມ', type: 'income', amount: 35000 },
    { date: '2026-01-21', description: 'ຊ່າງແກ້ໄຂ', type: 'expense', amount: 15000 },
    { date: '2026-01-22', description: 'ຂາຍອາຫານ', type: 'income', amount: 60000 },
    { date: '2026-01-22', description: 'ໂອນບັນຊີໄຟຟ້າ', type: 'expense', amount: 25000 },
];

const totalIncome = computed(() => {
    return reportData.value
        .filter(item => item.type === 'income')
        .reduce((sum, item) => sum + item.amount, 0);
});

const totalExpense = computed(() => {
    return reportData.value
        .filter(item => item.type === 'expense')
        .reduce((sum, item) => sum + item.amount, 0);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('lo-LA', {
        style: 'currency',
        currency: 'LAK',
        minimumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('lo-LA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const generateReport = () => {
    if (!startDate.value || !endDate.value) {
        alert('ກະລຸນາເລືອກວັນທີ່ເລີ່ມຕົ້ນ ແລະ ວັນທີ່ສິ້ນສຸດ');
        return;
    }

    // Filter data based on date range
    const filtered = sampleData.filter(item => {
        const itemDate = new Date(item.date);
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        return itemDate >= start && itemDate <= end;
    });

    reportData.value = filtered;

    // In a real application, you would make an API call here
    // Example: const response = await fetch(`/api/reports?start=${startDate.value}&end=${endDate.value}`);
};
</script>

<style lang="scss" scoped>

</style>