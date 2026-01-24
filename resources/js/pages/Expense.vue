<template>
    <div class="card  bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ລາຍການ ລາຍຈ່າຍ</h1>
                <div class="flex gap-2">
                    <label class="input">
                        <MagnifyingGlassIcon class="w-5 h-5"/>
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" />
                    </label>
                    <button class="btn btn-dash btn-info" @click="addeditExpense()"><PlusIcon class="w-5 h-5"/> ເພີ່ມລາຍຈ່າຍໃໝ່</button>
                </div>
            </div>

            <div class="overflow-x-auto ">
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
                    <tr v-for="item in 10" :key="item">
                        <th>1</th>
                        <td>ຄ່າໄຟຟ້າ</td>
                        <td>500,000</td>
                        <td>2024-01-24</td>
                        <td>Admin</td>
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
        <dialog id="AddEditExpense" class="modal" ref="addeditExpenseModal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">ແບບຟອມ ລາຍຈ່າຍ</h3>
                <form class="grid grid-cols-1 gap-4 mt-4">
                    <label>ລາຍລະອຽດ:</label>
                    <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດ"></textarea>
                    <div>
                        <label class="label">
                            <span class="label-text">ຈຳນວນເງິນ</span>
                        </label>
                        <input type="number" placeholder="ຈຳນວນເງິນ" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ວັນທີ່ຈ່າຍ</span>
                        </label>
                        <input type="date" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ຜູ້ບັນທຶກ</span>
                        </label>
                        <input type="text" placeholder="ຜູ້ບັນທຶກ" class="input input-bordered w-full" />
                    </div>
                </form>

                <div class="modal-action">
                    <div method="dialog">
                        <button class="btn me-2 btn-primary">ບັນທຶກ</button>
                        <button class="btn" @click="closeExpenseModal">ຍົກເລີກ</button>
                    </div>
                </div>
            </div>
        </dialog>
    </div>
</template>

<script setup>
import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const addeditExpenseModal = ref(null);

const addeditExpense = () => {
    addeditExpenseModal.value.showModal();
}

const closeExpenseModal = () => {
    addeditExpenseModal.value.close();
}
</script>

<style scoped>
/* Scoped styles for Expense Management page */
</style>
