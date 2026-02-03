<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4" id="reportContent">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold">ລາຍງານ</h1>
                <div class="flex gap-2">
                    <button 
                        @click="exportReport" 
                        class="btn btn-success"
                        :disabled="loading || reportData.length === 0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17H1a1 1 0 011-1h16a1 1 0 011 1h-2a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2v-4H3v4zM5 5a1 1 0 011-1h8a1 1 0 011 1v2a1 1 0 01-1 1H6a1 1 0 01-1-1V5zm7 0h1v2h-1V5zM7 5h1v2H7V5z" clip-rule="evenodd" />
                        </svg>
                        ສົ່ງອອກເປັນ Excel
                    </button>
                    <button 
                        @click="exportPdfReport" 
                        class="btn btn-error"
                        :disabled="loading || reportData.length === 0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.5 7.75c-.28 0-.5.22-.5.5v7.5c0 .28-.22.5-.5.5h-11c-.28 0-.5-.22-.5-.5v-7.5c0-.28-.22-.5-.5-.5H3.5c-.28 0-.5.22-.5.5v8.5c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8.25c0-.28-.22-.5-.5-.5h-2.5zm-8.5-4h3c.28 0 .5-.22-.5V2.5c0-.28-.22-.5-.5-.5H11.5c-.28 0-.5.22-.5.5v.75c0 .28.22.5.5.5zM7.5 2.5c0-.28-.22-.5-.5-.5H4.5c-.28 0-.5.22-.5.5v.75c0 .28.22.5.5.5h3c.28 0 .5-.22.5-.5V2.5zM12.75 6.5c-.28 0-.5.22-.5.5v4c0 .28.22.5.5.5h4c.28 0 .5-.22.5-.5v-4c0-.28-.22-.5-.5-.5h-4zm-.25 5.5v-3.5h3.5v3.5h-3.5zM12.75 14.25c-.28 0-.5.22-.5.5v4c0 .28.22.5.5.5h4c.28 0 .5-.22.5-.5v-4c0-.28-.22-.5-.5-.5h-4zm-.25 5.5v-3.5h3.5v3.5h-3.5z" />
                        </svg>
                        ສົ່ງອອກ PDF
                    </button>
                </div>
            </div>
            
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
                        :disabled="loading"
                    >
                        <span v-if="loading" class="loading loading-spinner"></span>
                        {{ loading ? 'ກຳລັງສ້າງ...' : 'ສ້າງລາຍງານ' }}
                    </button>
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
                            <tr v-if="loading">
                                <td colspan="5" class="text-center py-8">
                                    <span class="loading loading-lg"></span>
                                </td>
                            </tr>
                            <tr v-else-if="reportData.length === 0">
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
                                        :class="item.type === 'income' ? 'badge-success' : 'badge-error'"
                                        class="badge"
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
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const startDate = ref('');
const endDate = ref('');
const reportData = ref([]);
const totalIncome = ref(0);
const totalExpense = ref(0);
const loading = ref(false);

const setDefaultDateRange = () => {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    // Format to YYYY-MM-DD
    startDate.value = firstDayOfMonth.toISOString().split('T')[0];
    endDate.value = today.toISOString().split('T')[0];
};

onMounted(() => {
    setDefaultDateRange();
    generateReport();
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

const generateReport = async () => {
    if (!startDate.value || !endDate.value) {
        alert('ກະລຸນາເລືອກວັນທີ່ເລີ່ມຕົ້ນ ແລະ ວັນທີ່ສິ້ນສຸດ');
        return;
    }
    
    loading.value = true;
    try {
        const response = await api.get('/reports', {
            params: {
                start_date: startDate.value,
                end_date: endDate.value,
            }
        });
        
        const data = response.data;
        reportData.value = data.report_data;
        totalIncome.value = data.total_income;
        totalExpense.value = data.total_expense;

    } catch (error) {
        console.error('Error generating report:', error);
        alert('ບໍ່ສາມາດສ້າງລາຍງານໄດ້. ກະລຸນາລອງໃໝ່.');
    } finally {
        loading.value = false;
    }
};

const exportReport = async () => {
    if (!startDate.value || !endDate.value) {
        alert('ກະລຸນາເລືອກວັນທີ່ເລີ່ມຕົ້ນ ແລະ ວັນທີ່ສິ້ນສຸດ');
        return;
    }
    
    try {
        const response = await api.get('/reports/export', {
            params: {
                start_date: startDate.value,
                end_date: endDate.value,
            },
            responseType: 'blob' // Important for downloading files
        });

        // Create a blob URL and download the file
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `report_${startDate.value}_to_${endDate.value}.xlsx`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error exporting report:', error);
        alert('ບໍ່ສາມາດສົ່ງອອກລາຍງານໄດ້. ກະລຸນາລອງໃໝ່.');
    }
};
const exportPdfReport = async () => {
    if (!startDate.value || !endDate.value) {
        alert('ກະລຸນາເລືອກວັນທີ່ເລີ່ມຕົ້ນ ແລະ ວັນທີ່ສິ້ນສຸດ');
        return;
    }
    
    try {
        const response = await api.get('/reports/export/pdf', { // New endpoint for PDF
            params: {
                start_date: startDate.value,
                end_date: endDate.value,
            },
            responseType: 'blob' // Important for downloading files
        });

        // Create a blob URL and download the file
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `report_${startDate.value}_to_${endDate.value}.pdf`); // .pdf extension
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error exporting PDF report:', error);
        alert('ບໍ່ສາມາດສົ່ງອອກລາຍງານ PDF ໄດ້. ກະລຸນາລອງໃໝ່.');
    }
};
</script>

<style lang="scss" scoped>

</style>