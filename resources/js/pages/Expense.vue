<template>
    <div class="card  bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ລາຍຈ່າຍ</h1>
                <div class="flex gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <MagnifyingGlassIcon class="w-5 h-5"/>
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" v-model="searchQuery" @input="fetchExpenses()" />
                    </label>
                    <button class="btn btn-dash btn-info" @click="showAddEditExpenseModal()"><PlusIcon class="w-5 h-5"/> ເພີ່ມລາຍຈ່າຍໃໝ່</button>
                </div>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="table border-1 border-gray-300 ">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ລາຍລະອຽດ</th>
                        <th>ຈຳນວນເງິນ</th>
                        <th>ວັນທີ່ຈ່າຍ</th>
                        <th>ຜູ້ບັນທຶກ</th>
                        <th>ຈັດການ</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="loading">
                        <td colspan="6" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                    </tr>
                    <tr v-else-if="!expenses || expenses.length === 0">
                        <td colspan="6" class="text-center">ບໍ່ມີຂໍ້ມູນລາຍຈ່າຍ</td>
                    </tr>
                    <tr v-else v-for="(expense, index) in expenses" :key="expense.id">
                        <th>{{ (currentPage - 1) * 10 + index + 1 }}</th>
                        <td>{{ expense.description }}</td>
                        <td>{{ expense.amount.toLocaleString() }}</td>
                        <td>{{ formatDate(expense.expense_date) }}</td>
                        <td>{{ expense.user?.name || 'N/A' }}</td>
                        <td class="gap-2 flex">
                            <button class="btn btn-info p-2" @click="showAddEditExpenseModal(expense)"><PencilSquareIcon class="w-4 h-4"/></button>
                            <button class="btn btn-error p-2" @click="deleteExpense(expense.id)"><TrashIcon class="w-4 h-4"/></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :current-page="currentPage" :last-page="lastPage" @page-change="fetchExpenses" />
        </div>

        <!-- Modal -->
        <dialog id="AddEditExpense" class="modal" ref="addeditExpenseModal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">{{ isEditMode ? 'ແກ້ໄຂລາຍຈ່າຍ' : 'ເພີ່ມລາຍຈ່າຍໃໝ່' }}</h3>
                <form v-if="expenseForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveExpense">
                    <div>
                        <label class="label">
                            <span class="label-text">ລາຍລະອຽດ</span>
                        </label>
                        <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດ" v-model="expenseForm.description"></textarea>
                        <label v-if="errors.description && errors.description.length" class="label text-error">{{ errors.description[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ຈຳນວນເງິນ</span>
                        </label>
                        <input type="number" placeholder="ຈຳນວນເງິນ" class="input input-bordered w-full" v-model="expenseForm.amount" />
                        <label v-if="errors.amount && errors.amount.length" class="label text-error">{{ errors.amount[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ວັນທີ່ຈ່າຍ</span>
                        </label>
                        <input type="date" class="input input-bordered w-full" v-model="expenseForm.expense_date" />
                        <label v-if="errors.expense_date && errors.expense_date.length" class="label text-error">{{ errors.expense_date[0] }}</label>
                    </div>
                    
                    <div class="modal-action">
                        <button type="submit" class="btn me-2 btn-primary" :disabled="loading">
                            <span v-if="loading" class="loading loading-spinner"></span>
                            ບັນທຶກ
                        </button>
                        <button type="button" class="btn" @click="closeExpenseModal">ຍົກເລີກ</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import axios from '@/api/axios';
import Pagination from '../components/Pagination.vue';
import { useDialogStore } from '../stores/dialog';

// Reactive State
const expenses = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const dialogStore = useDialogStore();

const addeditExpenseModal = ref(null); // Modal reference
const isEditMode = ref(false);
const expenseForm = ref(null);
const errors = ref({});

// Methods
const fetchExpenses = async (page = 1, search = '') => {
    loading.value = true;
    try {
        const response = await axios.get('/expenses', {
            params: {
                page,
                search
            }
        });
        expenses.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Error fetching expenses:', error);
        dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນລາຍຈ່າຍ' });
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    // Get current date in YYYY-MM-DD format
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const day = String(today.getDate()).padStart(2, '0');
    const formattedDate = `${year}-${month}-${day}`;

    expenseForm.value = {
        id: null,
        description: '',
        amount: 0,
        expense_date: formattedDate,
    };
    isEditMode.value = false;
    errors.value = {};
};

const showAddEditExpenseModal = (expense = null) => {
    resetForm();
    if (expense) {
        isEditMode.value = true;
        expenseForm.value = {
            ...expense,
            amount: Number(expense.amount),
            // Ensure date is in YYYY-MM-DD format for input type="date"
            expense_date: expense.expense_date.split('T')[0]
        };
    }
    addeditExpenseModal.value.showModal();
};

const closeExpenseModal = () => {
    addeditExpenseModal.value.close();
    resetForm();
};

const saveExpense = async () => {
    if (!expenseForm.value) return;
    loading.value = true;
    errors.value = {};

    try {
        let response;
        if (isEditMode.value) {
            response = await axios.put(`/expenses/${expenseForm.value.id}`, expenseForm.value);
        } else {
            response = await axios.post('/expenses', expenseForm.value);
        }

        console.log('Save expense success:', response.data);
        closeExpenseModal();
        fetchExpenses(currentPage.value, searchQuery.value);
        dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກລາຍຈ່າຍສຳເລັດ!' });

    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Error saving expense:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກລາຍຈ່າຍ' });
        }
    } finally {
        loading.value = false;
    }
};

const deleteExpense = async (id) => {
    const confirmed = await dialogStore.confirm({
        title: 'ຢືນຢັນການລຶບ',
        message: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບລາຍຈ່າຍນີ້?'
    });
    if (confirmed) {
        loading.value = true;
        try {
            await axios.delete(`/expenses/${id}`);
            fetchExpenses(currentPage.value, searchQuery.value);
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບລາຍຈ່າຍສຳເລັດ!' });
        } catch (error) {
            console.error('Error deleting expense:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບລາຍຈ່າຍ' });
        } finally {
            loading.value = false;
        }
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('lo-LA'); // Format for Lao locale
};

// Watch for search query changes
let searchTimeout = null;
watch(searchQuery, (newQuery) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchExpenses(1, newQuery);
    }, 500); // Debounce search input
});

// Lifecycle Hooks
onMounted(() => {
    fetchExpenses();
});
</script>

<style scoped>
/* Scoped styles for Expense Management page */
</style>
