<template>
    <div class="card bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ລູກຄ້າ</h1>
                <div class="flex gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <MagnifyingGlassIcon class="w-5 h-5" />
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" v-model="searchQuery" @input="fetchCustomers()" />
                    </label>
                    <button class="btn btn-dash btn-info" @click="showAddEditCustomerModal()">
                        <PlusIcon class="w-5 h-5" /> ເພີ່ມລູກຄ້າໃໝ່
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="table border-1 border-gray-300">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ຊື່ລູກຄ້າ</th>
                            <th>ເບີໂທ</th>
                            <th>ທີ່ຢູ່</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="5" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                        </tr>
                        <tr v-else-if="!customers || customers.length === 0">
                            <td colspan="5" class="text-center">ບໍ່ມີຂໍ້ມູນລູກຄ້າ</td>
                        </tr>
                        <tr v-else v-for="(item, index) in customers" :key="item.id">
                            <th>{{ (currentPage - 1) * 10 + index + 1 }}</th>
                            <td>{{ item.name }}</td>
                            <td>{{ item.phone }}</td>
                            <td>{{ item.address }}</td>
                            <td class="gap-2 flex">
                                <button class="btn btn-info p-2" @click="editCustomer(item)">
                                    <PencilSquareIcon class="w-4 h-4" />
                                </button>
                                <button class="btn btn-error p-2" @click="deleteCustomer(item.id)">
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :current-page="currentPage" :last-page="lastPage" @page-change="fetchCustomers" />
        </div>

        <!-- Modal -->
        <dialog id="AddEditCustomer" class="modal" ref="addeditCustomerModal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">ແບບຟອມ ລູກຄ້າ</h3>
                <form v-if="customerForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveCustomer">
                    <div>
                        <label class="label">
                            <span class="label-text">ຊື່ລູກຄ້າ</span>
                        </label>
                        <input type="text" placeholder="ຊື່ລູກຄ້າ" class="input input-bordered w-full" v-model="customerForm.name" />
                        <label v-if="errors.name && errors.name.length" class="label text-error">{{ errors.name[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ເບີໂທ</span>
                        </label>
                        <input type="text" placeholder="ເບີໂທ" class="input input-bordered w-full" v-model="customerForm.phone" />
                         <label v-if="errors.phone && errors.phone.length" class="label text-error">{{ errors.phone[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ທີ່ຢູ່</span>
                        </label>
                        <textarea class="textarea textarea-bordered w-full" placeholder="ທີ່ຢູ່" v-model="customerForm.address"></textarea>
                        <label v-if="errors.address && errors.address.length" class="label text-error">{{ errors.address[0] }}</label>
                    </div>
                     <div class="modal-action">
                        <button type="submit" class="btn btn-primary me-2" :disabled="loading">
                            <span v-if="loading" class="loading loading-spinner"></span>
                            ບັນທຶກ
                        </button>
                        <button type="button" class="btn" @click="closeCustomerModal">ຍົກເລີກ</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
</template>

<script setup>
import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref, onMounted } from 'vue';
import axios from '@/api/axios';
import { useDialogStore } from '../stores/dialog';
import Pagination from '../components/Pagination.vue';

const addeditCustomerModal = ref(null);
const customers = ref([]);
const dialogStore = useDialogStore();

const customerForm = ref({
    id: null,
    name: '',
    phone: '',
    address: ''
});

const isEditMode = ref(false);
const errors = ref({});
const loading = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const searchQuery = ref('');

const fetchCustomers = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get('/customers', {
            params: { 
                page,
                search: searchQuery.value
            }
        });
        customers.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Error fetching customers:', error);
        dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນລູກຄ້າ' });
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    customerForm.value = {
        id: null,
        name: '',
        phone: '',
        address: ''
    };
    isEditMode.value = false;
    errors.value = {};
};

const showAddEditCustomerModal = (customer = null) => {
    resetForm();
    if (customer) {
        isEditMode.value = true;
        customerForm.value = { ...customer };
    }
    addeditCustomerModal.value.showModal();
};

const closeCustomerModal = () => {
    addeditCustomerModal.value.close();
    resetForm();
};

const saveCustomer = async () => {
    if (!customerForm.value) return;
    loading.value = true;
    errors.value = {};

    try {
        let response;
        if (isEditMode.value) {
            response = await axios.put(`/customers/${customerForm.value.id}`, customerForm.value);
        } else {
            response = await axios.post('/customers', customerForm.value);
        }
        
        closeCustomerModal();
        fetchCustomers(currentPage.value);
        dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກຂໍ້ມູນລູກຄ້າສຳເລັດ!' });

    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Error saving customer:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນລູກຄ້າ' });
        }
    } finally {
        loading.value = false;
    }
};

const editCustomer = (customer) => {
    showAddEditCustomerModal(customer);
};

const deleteCustomer = async (id) => {
    const confirmed = await dialogStore.confirm({
        title: 'ຢືນຢັນการລຶບ',
        message: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຂໍ້ມູນລູກຄ້ານີ້?'
    });
    if (confirmed) {
        loading.value = true;
        try {
            await axios.delete(`/customers/${id}`);
            fetchCustomers(currentPage.value);
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບຂໍ້ມູນລູກຄ້າສຳເລັດ!' });
        } catch (error) {
            console.error('Error deleting customer:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນລູກຄ້າ' });
        } finally {
            loading.value = false;
        }
    }
};

onMounted(() => {
    fetchCustomers();
});

</script>

<style scoped>
/* Scoped styles for Customer Management page */
</style>
