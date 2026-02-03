<script setup>
import { ref, onMounted, watch } from 'vue';
import {
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    MagnifyingGlassIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';
import axios from '../api/axios';
import Pagination from '../components/Pagination.vue';
import { useDialogStore } from '../stores/dialog';

// Reactive State
const products = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const dialogStore = useDialogStore();

const addeditpro = ref(null); // Modal reference
const isEditMode = ref(false);
const productForm = ref(null);
const errors = ref({});

const categories = ref([]);
const units = ref([]);

const imagePreviewUrl = ref(null);
const imageFile = ref(null);

const getFullImageUrl = (imagePath) => {
    if (!imagePath) {
        return 'https://placehold.co/400?text=No+Image';
    }
    if (imagePath.startsWith('http') || imagePath.startsWith('data:')) {
        return imagePath;
    }
    if (imagePath.startsWith('/storage/')) {
        return `${window.location.origin}${imagePath}`;
    }
    return `${window.location.origin}/storage/${imagePath}`;
};

// Methods
const fetchProducts = async (page = -1, search = '') => {
    loading.value = true;
    console.log(page, search);
    try {
        const response = await axios.get('/products', {
            params: {
                page,
                search
            }
        });
        products.value = response.data;
        // Debugging line
        // products.value.forEach(p => console.log('Product ID:', p.id, 'Image Path:', p.image));
        // currentPage.value = response.data.current_page;
        // lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Error fetching products:', error);
        dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນສິນຄ້າ' });
    } finally {
        loading.value = false;
    }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/categories', {
            params: { page: -1 }
        });
        categories.value = response.data; // This will be the array of all categories
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const fetchUnits = async () => {
    try {
        const response = await axios.get('/units', {
            params: { page: -1 }
        });
        units.value = response.data; // This will be the array of all units
    } catch (error) {
        console.error('Error fetching units:', error);
    }
};

const addeditproduct = (product = null) => {
    errors.value = {};
    if (product) {
        isEditMode.value = true;
        productForm.value = { ...product };
        imagePreviewUrl.value = getFullImageUrl(product.image);
        imageFile.value = null;
    } else {
        isEditMode.value = false;
        productForm.value = {
            name: '',
            category_id: '',
            unit_id: '',
            barcode: '',
            price: 0,
            cost_price: 0,
            stock_quantity: 0,
            description: '',
            status: 'active',
        };
        removeImage();
    }
    addeditpro.value.showModal();
};

const closemodal = () => {
    addeditpro.value.close();
    productForm.value = null;
    isEditMode.value = false;
    errors.value = {};
    removeImage();
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        imageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviewUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    imagePreviewUrl.value = null;
    imageFile.value = null;
    // If you have a file input, you might need to reset it
    // This is tricky, a common way is to re-create the input or reset the form it's in.
    // For this case, we'll just clear the file variable.
};

const saveProduct = async () => {
    if (!productForm.value) return;
    loading.value = true;
    errors.value = {};

    const formData = new FormData();
    for (const key in productForm.value) {
        if (productForm.value[key] !== null) {
            formData.append(key, productForm.value[key]);
        }
    }

    if (imageFile.value) {
        formData.append('image', imageFile.value);
    }

    try {
        let response;
        if (isEditMode.value) {
            // Laravel needs _method to be 'PUT' for FormData requests
            formData.append('_method', 'PUT');
            response = await axios.post(`/products/${productForm.value.id}`, formData);
        } else {
            response = await axios.post('/products', formData);
        }
        
        console.log('Save success:', response.data);
        closemodal();
        fetchProducts(currentPage.value, searchQuery.value);
        dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກສິນຄ້າສຳເລັດ!' });

    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Error saving product:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກສິນຄ້າ' });
        }
    } finally {
        loading.value = false;
    }
};

const deleteProduct = async (id) => {
    if (confirm('ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບສິນค้านี้?')) {
        loading.value = true;
        try {
            await axios.delete(`/products/${id}`);
            fetchProducts(currentPage.value, searchQuery.value);
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບສິນຄ້າສຳເລັດ!' });
        } catch (error) {
            console.error('Error deleting product:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບສິນຄ້າ' });
        } finally {
            loading.value = false;
        }
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        fetchProducts(page, searchQuery.value);
    }
};

// Watch for search query changes
let searchTimeout = null;
watch(searchQuery, (newQuery) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchProducts(1, newQuery);
    }, 500); // Debounce search input
});

// Lifecycle Hooks
onMounted(() => {
    fetchProducts();
    fetchCategories();
    fetchUnits();
});
</script>

<template>
    <div class="card bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ສິນຄ້າ</h1>
                <div class="flex gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <MagnifyingGlassIcon class="w-5 h-5"/>
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" v-model="searchQuery" />
                    </label>
                    <button class="btn btn-info" @click="openImportModal"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375H12a1.125 1.125 0 0 1-1.125-1.125V1.5M19.5 14.25H12m7.5 0 3 3m-3-3 3-3m-8.25 6.75H12" />
</svg>

 ເພີ່ມຈາກ Excel</button>
                    <button class="btn btn-dash btn-info" @click="addeditproduct()"><PlusIcon class="w-5 h-5"/> ເພີ່ມສິນຄ້າໃໝ່</button>
                </div>
            </div>

            <div class="overflow-x-auto ">
                <table class="table border-1 border-gray-300 ">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ຮູບ</th>
                        <th>ຊື່ສິນຄ້າ</th>
                        <th>ໝວດໝູ່</th>
                        <th>ຫົວໜ່ວຍ</th>
                        <th>ຈຳນວນ</th>
                        <th>ລາຄາຂາຍ</th>
                        <th>ຈັດການ</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="loading">
                        <td colspan="8" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                    </tr>
                    <tr v-else-if="!products || products.length === 0">
                        <td colspan="8" class="text-center">ບໍ່ມີຂໍ້ມູນສິນຄ້າ</td>
                    </tr>
                    <tr v-else v-for="(product, index) in products.filter(p => p)" :key="index">
                        <th>{{ (currentPage - 1) * 10 + index + 1 }}</th>
                        <td>
                            <div v-if="product" class="avatar">
                                <div class="w-12 h-12 rounded-full">
                                    <img :src="getFullImageUrl(product?.image)" :alt="product?.name" />
                                </div>
                            </div>
                        </td>
                        <td>{{ product.name }}</td>
                        <td>{{ product.category?.name || 'N/A' }}</td>
                        <td>{{ product.unit?.name || 'N/A' }}</td>
                        <td>{{ product.stock_quantity }}</td>
                        <td>{{ product.price.toLocaleString() }}</td>
                        <td class="gap-2 flex">
                            <button class="btn btn-info p-2" @click="addeditproduct(product)"><PencilSquareIcon class="w-4 h-4"/></button>
                            <button class="btn btn-error p-2" @click="deleteProduct(product.id)"><TrashIcon class="w-4 h-4"/></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :current-page="currentPage" :last-page="lastPage" @page-change="goToPage" />

        </div>

        <!-- Modal -->
        <dialog id="AddEditProduct" class="modal" ref="addeditpro">
            <div class="modal-box">
                <h3 class="text-lg font-bold">{{ isEditMode ? 'ແກ້ໄຂສິນຄ້າ' : 'ເພີ່ມສິນຄ້າໃໝ່' }}</h3>

                <form v-if="productForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveProduct">
                    <div class="flex justify-center flex-col items-center">
                        <div class="avatar relative">
                            <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                <img :src="imagePreviewUrl || 'https://placehold.co/400?text=No+Image'" alt="Product Image" />
                            </div>
                            <button v-if="imagePreviewUrl" @click="removeImage" class="absolute top-0 right-0 btn btn-circle btn-xs btn-error text-white">
                                <XCircleIcon class="w-4 h-4" />
                            </button>
                        </div>
                        <input type="file" @change="handleImageUpload" class="file-input file-input-bordered w-full max-w-xs mt-2" accept="image/*" />
                        <label v-if="errors.image && errors.image.length" class="label text-error">{{ errors.image[0] }}</label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">
                                <span class="label-text">ຊື່ສິນຄ້າ</span>
                            </label>
                            <input type="text" placeholder="ຊື່ສິນຄ້າ" class="input input-bordered w-full" v-model="productForm.name" />
                            <label v-if="errors.name && errors.name.length" class="label text-error">{{ errors.name[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ໝວດໝູ່</span>
                            </label>
                            <select class="select select-bordered w-full" v-model="productForm.category_id">
                                <option disabled value="">ເລືອກໝວດໝູ່</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                            <label v-if="errors.category_id && errors.category_id.length" class="label text-error">{{ errors.category_id[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ຫົວໜ່ວຍ</span>
                            </label>
                            <select class="select select-bordered w-full" v-model="productForm.unit_id">
                                <option disabled value="">ເລືອກຫົວໜ່ວຍ</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                            <label v-if="errors.unit_id && errors.unit_id.length" class="label text-error">{{ errors.unit_id[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ບາໂຄດ</span>
                            </label>
                            <input type="text" placeholder="ບາໂຄດ" class="input input-bordered w-full" v-model="productForm.barcode" />
                            <label v-if="errors.barcode && errors.barcode.length" class="label text-error">{{ errors.barcode[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ລາຄາຂາຍ</span>
                            </label>
                            <input type="number" placeholder="ລາຄາຂາຍ" class="input input-bordered w-full" v-model="productForm.price" />
                            <label v-if="errors.price && errors.price.length" class="label text-error">{{ errors.price[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ລາຄາຕົ້ນທຶນ</span>
                            </label>
                            <input type="number" placeholder="ລາຄາຕົ້ນທຶນ" class="input input-bordered w-full" v-model="productForm.cost_price" />
                            <label v-if="errors.cost_price && errors.cost_price.length" class="label text-error">{{ errors.cost_price[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ຈຳນວນໃນສະຕັອກ</span>
                            </label>
                            <input type="number" placeholder="ຈຳນວນໃນສະຕັອກ" class="input input-bordered w-full" v-model="productForm.stock_quantity" />
                            <label v-if="errors.stock_quantity && errors.stock_quantity.length" class="label text-error">{{ errors.stock_quantity[0] }}</label>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">ສະຖານະ</span>
                            </label>
                            <select class="select select-bordered w-full" v-model="productForm.status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <label v-if="errors.status && errors.status.length" class="label text-error">{{ errors.status[0] }}</label>
                        </div>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ລາຍລະອຽດ</span>
                        </label>
                        <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດສິນຄ້າ" v-model="productForm.description"></textarea>
                        <label v-if="errors.description && errors.description.length" class="label text-error">{{ errors.description[0] }}</label>
                    </div>

                    <div class="modal-action">
                        <button type="submit" class="btn me-2 btn-primary" :disabled="loading">
                            <span v-if="loading" class="loading loading-spinner"></span>
                            ບັນທຶກ
                        </button>
                        <button type="button" class="btn" @click="closemodal">ຍົກເລີກ</button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Import Product Modal -->
        <dialog id="ImportProduct" class="modal" ref="importModal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">ນຳເຂົ້າສິນຄ້າຈາກ Excel</h3>
                <form class="mt-4" @submit.prevent="importProducts">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">ເລືອກໄຟລ໌ Excel</span>
                        </label>
                        <input type="file" class="file-input file-input-bordered w-full" accept=".xlsx, .xls" @change="handleImportFileChange" />
                        <label v-if="importErrors.file && importErrors.file.length" class="label text-error">{{ importErrors.file[0] }}</label>
                        <div v-if="importErrors.failures && importErrors.failures.length" class="mt-4 text-error">
                            <p>ການນຳເຂົ້າສຳເລັດດ້ວຍບາງຂໍ້ຜິດພາດ:</p>
                            <ul>
                                <li v-for="(failure, index) in importErrors.failures" :key="index">
                                    Row {{ failure.row }}: {{ failure.errors.join(', ') }} (Values: {{ JSON.stringify(failure.values) }})
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn me-2 btn-primary" :disabled="importLoading || !fileToImport">
                            <span v-if="importLoading" class="loading loading-spinner"></span>
                            ນຳເຂົ້າ
                        </button>
                        <button type="button" class="btn" @click="closeImportModal">ຍົກເລີກ</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
</template>