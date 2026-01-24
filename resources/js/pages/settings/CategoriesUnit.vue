<template>
    <div class="card bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ໝວດໝູ່ ແລະ ຫົວໜ່ວຍ</h1>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                <!-- Categories Card -->
                <div class="card bg-white shadow">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title text-lg">ໝວດໝູ່</h2>
                            <button class="btn btn-sm btn-info" @click="showAddEditCategoryModal()"><PlusIcon class="w-4 h-4"/> ເພີ່ມໃໝ່</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table border-1 border-gray-300">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ຊື່ໝວດໝູ່</th>
                                        <th>ລາຍລະອຽດ</th>
                                        <th>ຈັດການ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="categoryLoading">
                                        <td colspan="4" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                                    </tr>
                                    <tr v-else-if="!categories || categories.length === 0">
                                        <td colspan="4" class="text-center">ບໍ່ມີຂໍ້ມູນໝວດໝູ່</td>
                                    </tr>
                                    <tr v-else v-for="item in categories" :key="item.id">
                                        <th>{{ item.id }}</th>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.description }}</td>
                                        <td class="gap-2 flex">
                                            <button class="btn btn-info p-2" @click="editCategory(item)"><PencilSquareIcon class="w-4 h-4"/></button>
                                            <button class="btn btn-error p-2" @click="deleteCategory(item.id)"><TrashIcon class="w-4 h-4"/></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :current-page="categoryCurrentPage" :last-page="categoryLastPage" @page-change="fetchCategories" />
                    </div>
                </div>

                <!-- Units Card -->
                <div class="card bg-white shadow">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title text-lg">ຫົວໜ່ວຍ</h2>
                            <button class="btn btn-sm btn-info" @click="showAddEditUnitModal()"><PlusIcon class="w-4 h-4"/> ເພີ່ມໃໝ່</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table border-1 border-gray-300">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ຊື່ຫົວໜ່ວຍ</th>
                                        <th>ອັກສອນຍໍ</th>
                                        <th>ຈັດການ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="unitLoading">
                                        <td colspan="4" class="text-center">ກຳລັງໂຫຼດຂໍ້ມູນ...</td>
                                    </tr>
                                    <tr v-else-if="!units || units.length === 0">
                                        <td colspan="4" class="text-center">ບໍ່ມີຂໍ້ມູນຫົວໜ່ວຍ</td>
                                    </tr>
                                    <tr v-else v-for="item in units" :key="item.id">
                                        <th>{{ item.id }}</th>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.abbreviation }}</td>
                                        <td class="gap-2 flex">
                                            <button class="btn btn-info p-2" @click="editUnit(item)"><PencilSquareIcon class="w-4 h-4"/></button>
                                            <button class="btn btn-error p-2" @click="deleteUnit(item.id)"><TrashIcon class="w-4 h-4"/></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :current-page="unitCurrentPage" :last-page="unitLastPage" @page-change="fetchUnits" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <dialog id="AddEditCategory" class="modal" ref="categoryModal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">ແບບຟອມໝວດໝູ່</h3>
            <form v-if="categoryForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveCategory">
                <div>
                    <label class="label">
                        <span class="label-text">ຊື່ໝວດໝູ່</span>
                    </label>
                    <input type="text" placeholder="ກະລຸນາປ້ອນຊື່ໝວດໝູ່" class="input input-bordered w-full" v-model="categoryForm.name" />
                    <label v-if="categoryErrors.name && categoryErrors.name.length" class="label text-error">{{ categoryErrors.name[0] }}</label>
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">ລາຍລະອຽດ</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດໝວດໝູ່" v-model="categoryForm.description"></textarea>
                    <label v-if="categoryErrors.description && categoryErrors.description.length" class="label text-error">{{ categoryErrors.description[0] }}</label>
                </div>

                <div class="modal-action">
                    <button type="submit" class="btn btn-primary me-2" :disabled="categoryLoading">
                        <span v-if="categoryLoading" class="loading loading-spinner"></span>
                        ບັນທຶກ
                    </button>
                    <button type="button" class="btn" @click="closeCategoryModal">ຍົກເລີກ</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Unit Modal -->
    <dialog id="AddEditUnit" class="modal" ref="unitModal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">ແບບຟອມຫົວໜ່ວຍ</h3>
            <form v-if="unitForm" class="grid grid-cols-1 gap-4 mt-4" @submit.prevent="saveUnit">
                <div>
                    <label class="label">
                        <span class="label-text">ຊື່ຫົວໜ່ວຍ</span>
                    </label>
                    <input type="text" placeholder="ກະລຸນາປ້ອນຊື່ຫົວໜ່ວຍ" class="input input-bordered w-full" v-model="unitForm.name" />
                    <label v-if="unitErrors.name && unitErrors.name.length" class="label text-error">{{ unitErrors.name[0] }}</label>
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">ອັກສອນຍໍ</span>
                    </label>
                    <input type="text" placeholder="ເຊ່ນ: ແກ້ວ" class="input input-bordered w-full" v-model="unitForm.abbreviation" />
                    <label v-if="unitErrors.abbreviation && unitErrors.abbreviation.length" class="label text-error">{{ unitErrors.abbreviation[0] }}</label>
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">ລາຍລະອຽດ</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດຫົວໜ່ວຍ" v-model="unitForm.description"></textarea>
                    <label v-if="unitErrors.description && unitErrors.description.length" class="label text-error">{{ unitErrors.description[0] }}</label>
                </div>

                <div class="modal-action">
                    <button type="submit" class="btn btn-primary me-2" :disabled="unitLoading">
                        <span v-if="unitLoading" class="loading loading-spinner"></span>
                        ບັນທຶກ
                    </button>
                    <button type="button" class="btn" @click="closeUnitModal">ຍົກເລີກ</button>
                </div>
            </form>
        </div>
    </dialog>
</template>

<script setup>
    import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
    import { ref, onMounted } from 'vue';
    import axios from '../../api/axios';
    import { useDialogStore } from '../../stores/dialog';
    import Pagination from '../../components/Pagination.vue';

    const categoryModal = ref(null);
    const unitModal = ref(null);

    const categories = ref([]);
    const units = ref([]);

    const dialogStore = useDialogStore();

    // Category State
    const categoryForm = ref({
        id: null,
        name: '',
        description: '',
    });
    const isCategoryEditMode = ref(false);
    const categoryErrors = ref({});
    const categoryLoading = ref(false);
    const categoryCurrentPage = ref(1);
    const categoryLastPage = ref(1);

    // Unit State
    const unitForm = ref({
        id: null,
        name: '',
        abbreviation: '',
        description: '',
    });
    const isUnitEditMode = ref(false);
    const unitErrors = ref({});
    const unitLoading = ref(false);
    const unitCurrentPage = ref(1);
    const unitLastPage = ref(1);

    // Category Functions
    const fetchCategories = async (page = 1) => {
        categoryLoading.value = true;
        try {
            const response = await axios.get('/categories', {
                params: { page }
            });
            categories.value = response.data.data;
            categoryCurrentPage.value = response.data.current_page;
            categoryLastPage.value = response.data.last_page;
        } catch (error) {
            console.error('Error fetching categories:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນໝວດໝູ່' });
        } finally {
            categoryLoading.value = false;
        }
    };

    const resetCategoryForm = () => {
        categoryForm.value = {
            id: null,
            name: '',
            description: '',
        };
        isCategoryEditMode.value = false;
        categoryErrors.value = {};
    };

    const showAddEditCategoryModal = (category = null) => {
        resetCategoryForm();
        if (category) {
            isCategoryEditMode.value = true;
            categoryForm.value = { ...category };
        }
        categoryModal.value.showModal();
    };

    const closeCategoryModal = () => {
        categoryModal.value.close();
        resetCategoryForm();
    };

    const saveCategory = async () => {
        if (!categoryForm.value) return;
        categoryLoading.value = true;
        categoryErrors.value = {};

        try {
            let response;
            if (isCategoryEditMode.value) {
                response = await axios.put(`/categories/${categoryForm.value.id}`, categoryForm.value);
            } else {
                response = await axios.post('/categories', categoryForm.value);
            }

            console.log('Category save success:', response.data);
            closeCategoryModal();
            fetchCategories();
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກໝວດໝູ່ສຳເລັດ!' });

        } catch (error) {
            if (error.response && error.response.status === 422) {
                categoryErrors.value = error.response.data.errors;
            } else {
                console.error('Error saving category:', error);
                dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກໝວດໝູ່' });
            }
        } finally {
            categoryLoading.value = false;
        }
    };

    const editCategory = (category) => {
        showAddEditCategoryModal(category);
    };

    const deleteCategory = async (id) => {
        const confirmed = await dialogStore.confirm({
            title: 'ຢືນຢັນການລຶບ',
            message: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບໝວດໝູ່ນີ້?'
        });
        if (confirmed) {
            categoryLoading.value = true;
            try {
                await axios.delete(`/categories/${id}`);
                fetchCategories(categoryCurrentPage.value);
                dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບໝວດໝູ່ສຳເລັດ!' });
            } catch (error) {
                console.error('Error deleting category:', error);
                dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບໝວດໝູ່' });
            } finally {
                categoryLoading.value = false;
            }
        }
    };

    // Unit Functions
    const fetchUnits = async (page = 1) => {
        unitLoading.value = true;
        try {
            const response = await axios.get('/units', {
                params: { page }
            });
            units.value = response.data.data;
            unitCurrentPage.value = response.data.current_page;
            unitLastPage.value = response.data.last_page;
        } catch (error) {
            console.error('Error fetching units:', error);
            dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນຫົວໜ່ວຍ' });
        } finally {
            unitLoading.value = false;
        }
    };

    const resetUnitForm = () => {
        unitForm.value = {
            id: null,
            name: '',
            abbreviation: '',
            description: '',
        };
        isUnitEditMode.value = false;
        unitErrors.value = {};
    };

    const showAddEditUnitModal = (unit = null) => {
        resetUnitForm();
        if (unit) {
            isUnitEditMode.value = true;
            unitForm.value = { ...unit };
        }
        unitModal.value.showModal();
    };

    const closeUnitModal = () => {
        unitModal.value.close();
        resetUnitForm();
    };

    const saveUnit = async () => {
        if (!unitForm.value) return;
        unitLoading.value = true;
        unitErrors.value = {};

        try {
            let response;
            if (isUnitEditMode.value) {
                response = await axios.put(`/units/${unitForm.value.id}`, unitForm.value);
            } else {
                response = await axios.post('/units', unitForm.value);
            }

            console.log('Unit save success:', response.data);
            closeUnitModal();
            fetchUnits();
            dialogStore.success({ title: 'ສຳເລັດ', message: 'ບັນທຶກຫົວໜ່ວຍສຳເລັດ!' });

        } catch (error) {
            if (error.response && error.response.status === 422) {
                unitErrors.value = error.response.data.errors;
            } else {
                console.error('Error saving unit:', error);
                dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຫົວໜ່ວຍ' });
            }
        } finally {
            unitLoading.value = false;
        }
    };

    const editUnit = (unit) => {
        showAddEditUnitModal(unit);
    };

    const deleteUnit = async (id) => {
        const confirmed = await dialogStore.confirm({
            title: 'ຢືນຢັນການລຶບ',
            message: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຫົວໜ່ວຍນີ້?'
        });
        if (confirmed) {
            unitLoading.value = true;
            try {
                await axios.delete(`/units/${id}`);
                fetchUnits(unitCurrentPage.value);
                dialogStore.success({ title: 'ສຳເລັດ', message: 'ລຶບຫົວໜ່ວຍສຳເລັດ!' });
            } catch (error) {
                console.error('Error deleting unit:', error);
                dialogStore.warning({ title: 'ຂໍ້ຜິດພາດ', message: 'ເກີດຂໍ້ຜິດພາດໃນການລຶບຫົວໜ່ວຍ' });
            } finally {
                unitLoading.value = false;
            }
        }
    };
    onMounted(() => {
        fetchCategories();
        fetchUnits();
    });
</script>

<style lang="scss" scoped>

</style>