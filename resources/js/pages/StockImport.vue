<template>
    <div class="card bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ນຳເຂົ້າ ສິນຄ້າ</h1>
                <div class="flex gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <MagnifyingGlassIcon class="w-5 h-5" />
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" v-model="searchQuery" @input="fetchStockImports()" />
                    </label>
                    <button class="btn btn-dash btn-info" @click="showAddEditImportModal()">
                        <PlusIcon class="w-5 h-5" /> ນຳເຂົ້າສິນຄ້າ
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="table border-1 border-gray-300">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ວັນທີນຳເຂົ້າ</th>
                            <th>ຊື່ສິນຄ້າ</th>
                            <th>ຈຳນວນ</th>
                            <th>ລາຄາຕົ້ນທຶນ/ໜ່ວຍ</th>
                            <th>ລາຄາຂາຍໃໝ່/ໜ່ວຍ</th>
                            <th>ເປັນເງິນ</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="8" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                        </tr>
                        <tr v-else-if="!stockImports || stockImports.length === 0">
                            <td colspan="8" class="text-center">ບໍ່ມີຂໍ້ມູນການນຳເຂົ້າສິນຄ້າ</td>
                        </tr>
                        <tr v-else v-for="(item, index) in stockImports" :key="item.id">
                            <th>{{ (currentPage - 1) * 10 + index + 1 }}</th>
                            <td>{{ formatDate(item.import_date) }}</td>
                            <td>{{ item.product?.name || 'N/A' }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.cost_price_per_unit.toLocaleString() }}</td>
                            <td>{{ item.new_selling_price ? item.new_selling_price.toLocaleString() : '-' }}</td>
                            <td>{{ (item.quantity * item.cost_price_per_unit).toLocaleString() }}</td>
                            <td class="gap-2 flex">
                                <button class="btn btn-info p-2" @click="showAddEditImportModal(item)">
                                    <PencilSquareIcon class="w-4 h-4" />
                                </button>
                                <button class="btn btn-error p-2" @click="deleteImport(item.id)">
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :current-page="currentPage" :last-page="lastPage" @page-change="fetchStockImports" />
        </div>

        <!-- Modal -->
        <dialog id="ImportEditProduct" class="modal" ref="importeditpro">
            <div class="modal-box">
                <h3 class="text-lg font-bold">{{ isEditMode ? 'ແກ້ໄຂການນຳເຂົ້າສິນຄ້າ' : 'ນຳເຂົ້າສິນຄ້າໃໝ່' }}</h3>

                <form v-if="importForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveImport">
                    <div>
                        <label class="label">
                            <span class="label-text">ເລືອກສິນຄ້າ</span>
                        </label>
                        <select class="select select-bordered w-full" v-model="importForm.product_id">
                            <option disabled value="">ເລືອກສິນຄ້າ</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                        <label v-if="errors.product_id && errors.product_id.length" class="label text-error">{{ errors.product_id[0] }}</label>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">ເລືອກຜູ້ສະໜອງ</span>
                        </label>
                        <select class="select select-bordered w-full" v-model="importForm.supplier_id">
                            <option disabled value="">ເລືອກຜູ້ສະໜອງ</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select>
                        <label v-if="errors.supplier_id && errors.supplier_id.length" class="label text-error">{{ errors.supplier_id[0] }}</label>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">ຈຳນວນ</span>
                        </label>
                        <input type="number" placeholder="ຈຳນວນ" class="input input-bordered w-full" v-model="importForm.quantity" />
                        <label v-if="errors.quantity && errors.quantity.length" class="label text-error">{{ errors.quantity[0] }}</label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">
                                <span class="label-text">ລາຄາຕົ້ນທຶນຕໍ່ໜ່ວຍ</span>
                            </label>
                            <input type="number" placeholder="ລາຄາຕົ້ນທຶນຕໍ່ໜ່ວຍ" class="input input-bordered w-full" v-model="importForm.cost_price_per_unit" />
                            <label v-if="errors.cost_price_per_unit && errors.cost_price_per_unit.length" class="label text-error">{{ errors.cost_price_per_unit[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ລາຄາຂາຍໃໝ່ (ບໍ່ບັງຄັບ)</span>
                            </label>
                            <input type="number" placeholder="ລາຄາຂາຍໃໝ່" class="input input-bordered w-full" v-model="importForm.new_selling_price" />
                            <label v-if="errors.new_selling_price && errors.new_selling_price.length" class="label text-error">{{ errors.new_selling_price[0] }}</label>
                        </div>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ລາຍລະອຽດ</span>
                        </label>
                        <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດການນຳເຂົ້າ" v-model="importForm.description"></textarea>
                        <label v-if="errors.description && errors.description.length" class="label text-error">{{ errors.description[0] }}</label>
                    </div>

                    <div class="modal-action">
                        <button type="submit" class="btn me-2 btn-primary" :disabled="loading">
                            <span v-if="loading" class="loading loading-spinner"></span>
                            ບັນທຶກ
                        </button>
                        <button type="button" class="btn" @click="closeModal">ຍົກເລີກ</button>
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
const stockImports = ref([]);
const products = ref([]); // For product dropdown in the modal
const suppliers = ref([]); // For supplier dropdown in the modal
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const dialogStore = useDialogStore();

const importeditpro = ref(null); // Modal reference
const isEditMode = ref(false);
const importForm = ref(null);
const errors = ref({});

// Methods
const fetchStockImports = async (page = 1, search = '') => {
    loading.value = true;
    try {
        const response = await axios.get('/stock-imports', {
            params: {
                page,
                search
            }
        });
        stockImports.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Error fetching stock imports:', error);
        dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນການນຳເຂົ້າສິນຄ້າ' });
    } finally {
        loading.value = false;
    }
};

const fetchProducts = async () => {
    try {
        const response = await axios.get('/products', {
            params: { page: -1 } // Fetch all products for the dropdown
        });
        products.value = response.data.data;
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};

const fetchSuppliers = async () => {
    try {
        const response = await axios.get('/suppliers', {
            params: { page: -1 } // Fetch all suppliers for the dropdown
        });
        suppliers.value = response.data.data;
    } catch (error) {
        console.error('Error fetching suppliers:', error);
    }
};

const resetForm = () => {
    importForm.value = {
        id: null,
        product_id: '',
        supplier_id: '', // Add supplier_id
        quantity: 0,
        cost_price_per_unit: 0,
        new_selling_price: null,
        description: '',
    };
    isEditMode.value = false;
    errors.value = {};
};

const showAddEditImportModal = (importItem = null) => {
    resetForm();
    if (importItem) {
        isEditMode.value = true;
        // Ensure numbers are correctly parsed if they come as strings
        importForm.value = {
            ...importItem,
            quantity: Number(importItem.quantity),
            cost_price_per_unit: Number(importItem.cost_price_per_unit),
            new_selling_price: importItem.new_selling_price ? Number(importItem.new_selling_price) : null,
            supplier_id: importItem.supplier_id || '', // Ensure supplier_id is set for editing
        };
    }
    importeditpro.value.showModal();
};

const closeModal = () => {
    importeditpro.value.close();
    resetForm();
};

const saveImport = async () => {
    if (!importForm.value) return;
    loading.value = true;
    errors.value = {};

    try {
        let response;
        if (isEditMode.value) {
            response = await axios.put(`/stock-imports/${importForm.value.id}`, importForm.value);
        } else {
            response = await axios.post('/stock-imports', importForm.value);
        }

        console.log('Save stock import success:', response.data);
        closeModal();
        fetchStockImports(currentPage.value, searchQuery.value);
        dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກການນຳເຂົ້າສິນຄ້າສຳເລັດ!' });

    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Error saving stock import:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກການນຳເຂົ້າສິນຄ້າ' });
        }
    } finally {
        loading.value = false;
    }
};

const deleteImport = async (id) => {
    const confirmed = await dialogStore.confirm({
        title: 'ຢືນຢັນການລຶບ',
        message: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບລາຍການນຳເຂົ້າສິນຄ້ານີ້?'
    });
    if (confirmed) {
        loading.value = true;
        try {
            await axios.delete(`/stock-imports/${id}`);
            fetchStockImports(currentPage.value, searchQuery.value);
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບລາຍການນຳເຂົ້າສິນຄ້າສຳເລັດ!' });
        } catch (error) {
            console.error('Error deleting stock import:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບລາຍການນຳເຂົ້າສິນຄ້າ' });
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
        fetchStockImports(1, newQuery);
    }, 500); // Debounce search input
});

// Lifecycle Hooks
onMounted(() => {
    fetchStockImports();
    fetchProducts(); // Fetch products for the dropdown
    fetchSuppliers(); // Fetch suppliers for the dropdown
});
</script>

<style lang="scss" scoped>
/* Scoped styles for Stock Import Management page */
</style>