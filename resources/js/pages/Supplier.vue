<template>
    <div class="card  bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ຜູ້ສະໜອງ</h1>
                <div class="flex gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <MagnifyingGlassIcon class="w-5 h-5"/>
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" v-model="searchQuery" />
                    </label>
                    <button class="btn btn-dash btn-info" @click="addeditSupplier()"><PlusIcon class="w-5 h-5"/> ເພີ່ມຜູ້ສະໜອງໃໝ່</button>
                </div>
            </div>

            <div class="overflow-x-auto ">
                <table class="table border-1 border-gray-300 ">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ຊື່ຜູ້ສະໜອງ</th>
                        <th>ຜູ້ຕິດຕໍ່</th>
                        <th>ເບີໂທ</th>
                        <th>ທີ່ຢູ່</th>
                        <th>ຈັດການ</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="loading">
                        <td colspan="6" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                    </tr>
                    <tr v-else-if="!suppliers || suppliers.length === 0">
                        <td colspan="6" class="text-center">ບໍ່ມີຂໍ້ມູນຜູ້ສະໜອງ</td>
                    </tr>
                    <tr v-else v-for="(supplier, index) in suppliers" :key="supplier.id">
                        <th>{{ (currentPage - 1) * 10 + index + 1 }}</th>
                        <td>{{ supplier.name }}</td>
                        <td>{{ supplier.contact_person }}</td>
                        <td>{{ supplier.phone }}</td>
                        <td>{{ supplier.address }}</td>
                        <td class="gap-2 flex">
                            <button class="btn btn-info p-2" @click="addeditSupplier(supplier)"><PencilSquareIcon class="w-4 h-4"/></button>
                            <button class="btn btn-error p-2" @click="deleteSupplier(supplier.id)"><TrashIcon class="w-4 h-4"/></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :current-page="currentPage" :last-page="lastPage" @page-change="goToPage" />
        </div>

        <!-- Modal -->
        <dialog id="AddEditSupplier" class="modal" ref="addeditSupplierModal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">{{ isEditMode ? 'ແກ້ໄຂຜູ້ສະໜອງ' : 'ເພີ່ມຜູ້ສະໜອງໃໝ່' }}</h3>
                <form v-if="supplierForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveSupplier">
                    <div>
                        <label class="label">
                            <span class="label-text">ຊື່ຜູ້ສະໜອງ</span>
                        </label>
                        <input type="text" placeholder="ຊື່ຜູ້ສະໜອງ" class="input input-bordered w-full" v-model="supplierForm.name" />
                        <label v-if="errors.name && errors.name.length" class="label text-error">{{ errors.name[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ຜູ້ຕິດຕໍ່</span>
                        </label>
                        <input type="text" placeholder="ຜູ້ຕິດຕໍ່" class="input input-bordered w-full" v-model="supplierForm.contact_person" />
                        <label v-if="errors.contact_person && errors.contact_person.length" class="label text-error">{{ errors.contact_person[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ເບີໂທ</span>
                        </label>
                        <input type="text" placeholder="ເບີໂທ" class="input input-bordered w-full" v-model="supplierForm.phone" />
                        <label v-if="errors.phone && errors.phone.length" class="label text-error">{{ errors.phone[0] }}</label>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ທີ່ຢູ່</span>
                        </label>
                        <textarea class="textarea textarea-bordered w-full" placeholder="ທີ່ຢູ່" v-model="supplierForm.address"></textarea>
                        <label v-if="errors.address && errors.address.length" class="label text-error">{{ errors.address[0] }}</label>
                    </div>

                    <div class="modal-action">
                        <button type="submit" class="btn me-2 btn-primary" :disabled="loading">
                            <span v-if="loading" class="loading loading-spinner"></span>
                            ບັນທຶກ
                        </button>
                        <button type="button" class="btn" @click="closeSupplierModal">ຍົກເລີກ</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import {
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    MagnifyingGlassIcon
} from '@heroicons/vue/24/outline';
import axios from '../api/axios';
import Pagination from '../components/Pagination.vue';
import { useDialogStore } from '../stores/dialog'; // Assuming dialog store is available and used for notifications

// Reactive State
const suppliers = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const dialogStore = useDialogStore();

const addeditSupplierModal = ref(null); // Modal reference
const isEditMode = ref(false);
const supplierForm = ref(null);
const errors = ref({});

// Methods
const fetchSuppliers = async (page = 1, search = '') => {
    loading.value = true;
    try {
        const response = await axios.get('/suppliers', {
            params: {
                page,
                search
            }
        });
        suppliers.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Error fetching suppliers:', error);
        dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນຜູ້ສະໜອງ' });
    } finally {
        loading.value = false;
    }
};

const addeditSupplier = (supplier = null) => {
    errors.value = {};
    if (supplier) {
        isEditMode.value = true;
        supplierForm.value = { ...supplier };
    } else {
        isEditMode.value = false;
        supplierForm.value = {
            name: '',
            contact_person: '',
            phone: '',
            address: '',
        };
    }
    addeditSupplierModal.value.showModal();
};

const closeSupplierModal = () => {
    addeditSupplierModal.value.close();
    supplierForm.value = null;
    isEditMode.value = false;
    errors.value = {};
};

const saveSupplier = async () => {
    if (!supplierForm.value) return;
    loading.value = true;
    errors.value = {};

    try {
        let response;
        if (isEditMode.value) {
            response = await axios.put(`/suppliers/${supplierForm.value.id}`, supplierForm.value);
        } else {
            response = await axios.post('/suppliers', supplierForm.value);
        }
        
        console.log('Save success:', response.data);
        closeSupplierModal();
        fetchSuppliers(currentPage.value, searchQuery.value);
        dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກຜູ້ສະໜອງສຳເລັດ!' });

    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Error saving supplier:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຜູ້ສະໜອງ' });
        }
    } finally {
        loading.value = false;
    }
};

const deleteSupplier = async (id) => {
    if (confirm('ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຜູ້ສະໜອງຄົນນີ້?')) {
        loading.value = true;
        try {
            await axios.delete(`/suppliers/${id}`);
            fetchSuppliers(currentPage.value, searchQuery.value);
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບຜູ້ສະໜອງສຳເລັດ!' });
        } catch (error) {
            console.error('Error deleting supplier:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບຜູ້ສະໜອງ' });
        } finally {
            loading.value = false;
        }
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        fetchSuppliers(page, searchQuery.value);
    }
};

// Watch for search query changes
let searchTimeout = null;
watch(searchQuery, (newQuery) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchSuppliers(1, newQuery);
    }, 500); // Debounce search input
});

// Lifecycle Hooks
onMounted(() => {
    fetchSuppliers();
});
</script>

<style scoped>
/* Scoped styles for Supplier Management page */
</style>
