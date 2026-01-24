<template>
    <div class="card  bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ຜູ້ສະໜອງ</h1>
                <div class="flex gap-2">
                    <label class="input">
                        <MagnifyingGlassIcon class="w-5 h-5"/>
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" />
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
                    <tr v-for="item in 10" :key="item">
                        <th>1</th>
                        <td>ບໍລິສັດ ກ</td>
                        <td>ທ້າວ ກ</td>
                        <td>02011112222</td>
                        <td>ນະຄອນຫຼວງວຽງຈັນ</td>
                        <td class="gap-2 flex">
                            <button class="btn btn-info p-2"><PencilSquareIcon class="w-4 h-4"/></button>
                            <button class="btn btn-error p-2"><TrashIcon class="w-4 h-4"/></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <dialog id="AddEditSupplier" class="modal" ref="addeditSupplierModal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">ແບບຟອມ ຜູ້ສະໜອງ</h3>
                <form class="grid grid-cols-1 gap-4 mt-4">
                    <div>
                        <label class="label">
                            <span class="label-text">ຊື່ຜູ້ສະໜອງ</span>
                        </label>
                        <input type="text" placeholder="ຊື່ຜູ້ສະໜອງ" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ຜູ້ຕິດຕໍ່</span>
                        </label>
                        <input type="text" placeholder="ຜູ້ຕິດຕໍ່" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ເບີໂທ</span>
                        </label>
                        <input type="text" placeholder="ເບີໂທ" class="input input-bordered w-full" />
                    </div>
                    <label>ທີ່ຢູ່:</label>
                    <textarea class="textarea textarea-bordered w-full" placeholder="ທີ່ຢູ່"></textarea>
                </form>

                <div class="modal-action">
                    <div method="dialog">
                        <button class="btn me-2 btn-primary">ບັນທຶກ</button>
                        <button class="btn" @click="closeSupplierModal">ຍົກເລີກ</button>
                    </div>
                </div>
            </div>
        </dialog>
    </div>
</template>

<script setup>
import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const addeditSupplierModal = ref(null);

const addeditSupplier = () => {
    addeditSupplierModal.value.showModal();
}

const closeSupplierModal = () => {
    addeditSupplierModal.value.close();
}
</script>

<style scoped>
/* Scoped styles for Supplier Management page */
</style>
