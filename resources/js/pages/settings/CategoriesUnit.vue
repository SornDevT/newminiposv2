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
                                    <tr v-for="item in categories" :key="item.id">
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
                                    <tr v-for="item in units" :key="item.id">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <dialog id="AddEditCategory" class="modal" ref="categoryModal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">ແບບຟອມໝວດໝູ່</h3>
            <form class="grid grid-cols-1 gap-4 mt-4">
                <div>
                    <label class="label">
                        <span class="label-text">ຊື່ໝວດໝູ່</span>
                    </label>
                    <input type="text" placeholder="ກະລຸນາປ້ອນຊື່ໝວດໝູ່" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">ລາຍລະອຽດ</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດໝວດໝູ່"></textarea>
                </div>
            </form>

            <div class="modal-action">
                <div method="dialog">
                    <button class="btn btn-primary me-2">ບັນທຶກ</button>
                    <button class="btn" @click="closeCategoryModal">ຍົກເລີກ</button>
                </div>
            </div>
        </div>
    </dialog>

    <!-- Unit Modal -->
    <dialog id="AddEditUnit" class="modal" ref="unitModal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">ແບບຟອມຫົວໜ່ວຍ</h3>
            <form class="grid grid-cols-1 gap-4 mt-4">
                <div>
                    <label class="label">
                        <span class="label-text">ຊື່ຫົວໜ່ວຍ</span>
                    </label>
                    <input type="text" placeholder="ກະລຸນາປ້ອນຊື່ຫົວໜ່ວຍ" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">ອັກສອນຍໍ</span>
                    </label>
                    <input type="text" placeholder="ເຊ່ນ: ແກ້ວ" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">ລາຍລະອຽດ</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດຫົວໜ່ວຍ"></textarea>
                </div>
            </form>

            <div class="modal-action">
                <div method="dialog">
                    <button class="btn btn-primary me-2">ບັນທຶກ</button>
                    <button class="btn" @click="closeUnitModal">ຍົກເລີກ</button>
                </div>
            </div>
        </div>
    </dialog>
</template>

<script setup>
    import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
    import { ref } from 'vue';

    const categoryModal = ref(null);
    const unitModal = ref(null);
    const categories = ref([
        { id: 1, name: 'ເບຍ', description: 'ຫ້ອງແລະເຄື່ອງດື່ມແບບໂຟມ' },
        { id: 2, name: 'ເຄື່ອງດື່ມ', description: 'ຫ້ອງນ້ໍາ ກາເຟ ຊາ' },
        { id: 3, name: 'ອາຫານ', description: 'ອາຫານທົ່ວໄປ' },
    ]);
    const units = ref([
        { id: 1, name: 'ແກ້ວ', abbreviation: 'ແກ້ວ', description: 'ໜ່ວຍປະເພດແກ້ວ' },
        { id: 2, name: 'ຂວດ', abbreviation: 'ຂວດ', description: 'ໜ່ວຍປະເພດຂວດ' },
        { id: 3, name: 'ລູກ', abbreviation: 'ລູກ', description: 'ໜ່ວຍປະເພດລູກ' },
    ]);

    // Category Functions
    const showAddEditCategoryModal = () => {
        categoryModal.value.showModal();
    };

    const closeCategoryModal = () => {
        categoryModal.value.close();
    };

    const editCategory = (item) => {
        console.log('Edit category:', item);
        showAddEditCategoryModal();
    };

    const deleteCategory = (id) => {
        if (confirm('ທ່ານແນ່ໃຈບໍ?')) {
            console.log('Delete category:', id);
            categories.value = categories.value.filter(c => c.id !== id);
        }
    };

    // Unit Functions
    const showAddEditUnitModal = () => {
        unitModal.value.showModal();
    };

    const closeUnitModal = () => {
        unitModal.value.close();
    };

    const editUnit = (item) => {
        console.log('Edit unit:', item);
        showAddEditUnitModal();
    };

    const deleteUnit = (id) => {
        if (confirm('ທ່ານແນ່ໃຈບໍ?')) {
            console.log('Delete unit:', id);
            units.value = units.value.filter(u => u.id !== id);
        }
    };
</script>

<style lang="scss" scoped>

</style>