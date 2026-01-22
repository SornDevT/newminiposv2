<template>
    <div class="card  bg-base-100 card-xs shadow-sm">
    <div class="card-body p-4">
        <div class=" flex justify-between">
            <h1 class="text-xl">ນຳເຂົ້າ ສິນຄ້າ</h1>
            <div class=" flex gap-2">
             
                <label class="input">
                    <MagnifyingGlassIcon class="w-5 h-5"/>
                    <input type="text" class="grow" placeholder="ຄົ້ນຫາ" />
                </label>

                <button class="btn btn-dash btn-info" @click="importeditproduct()"><PlusIcon class="w-5 h-5"/> ນຳເຂົ້າສິນຄ້າ</button>

            </div>

        </div>

        <div class="overflow-x-auto ">
            <table class="table border-1 border-gray-300 ">
                <!-- head -->
                <thead>
                <tr>
                    <th>#</th>
                    <th>ວັນທີ່ນຳເຂົ້າ</th>
                    <th>ຊື່ສິນຄ້າ</th>
                    <th>ຈຳນວນ</th>
                    <th>ລາຄາຕົ້ນທຶນ</th>
                    <th>ເປັນເງິນ</th>
                    <th>ຜູ້ນຳເຂົ້າ</th>
                    <th>ຈັດການ</th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="item in 10" :key="item">
                    <th>1</th>
                    <td>12/12/2025</td>
                    <td>ເບຍລາວ</td>
                    <td>10</td>
                    <td>10,000</td>
                    <td>1,000,000</td>
                    <td>Admin</td>
                    <td class=" gap-2 flex ">
                    <button class="btn btn-info p-2"><PencilSquareIcon class="w-4 h-4"/></button>
                    <button class="btn btn-error p-2"><TrashIcon class="w-4 h-4"/></button>
                    </td>
                </tr>
                
                </tbody>
            </table>
            </div>
        
  
      </div>

      <!-- Modal -->
        <dialog id="ImportEditProduct" class="modal" ref="importeditpro" >
            <div class="modal-box">
                <h3 class="text-lg font-bold">ແບບຟອມ</h3>
                
                <form class="grid grid-cols-1 gap-4 mt-4">

                    
                     <div>
                        <label class="label">
                            <span class="label-text">ເລືອກສິນຄ້າ</span>
                        </label>    
                        <select class="select select-bordered w-full">
                            <option disabled selected>ເລືອກ</option>
                            <option>ເບຍ</option>
                            <option>ເຄື່ອງດື່ມ</option>
                            <option>ອາຫານ</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">ຈຳນວນ</span>
                        </label>
                        <input type="text" placeholder="ຈຳນວນ" class="input input-bordered w-full" />
                    </div>

                   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                   
                    <div>
                        <label class="label">
                            <span class="label-text">ລາຄາຕົ້ນທຶນຕໍ່ໜ່ວຍ</span>
                        </label>
                        <input type="number" placeholder="ລາຄາຕົ້ນທຶນຕໍ່ໜ່ວຍ" class="input input-bordered w-full" />   
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ລາຄາຂາຍໃໝ່</span>
                        </label>
                        <input type="number" placeholder="ລາຄາຂາຍໃໝ່" class="input input-bordered w-full" />   
                    </div>
                      </div>
                      <label>ລາຍລະອຽດ:</label>
                        <textarea class="textarea textarea-bordered w-full" placeholder="ລາຍລະອຽດສິນຄ້າ"></textarea>
                </form>


                <div class="modal-action">
                <div method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn me-2 btn-primary">ບັນທຶກ</button>
                    <button class="btn" @click="closemodal">ຍົກເລີກ</button>
                </div>
                </div>
            </div>
            </dialog>
    </div>
</template>

<script setup>

    import {  MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
    import { ref } from 'vue';

    const importeditpro = ref(null);


    // function ----------------------------

    const importeditproduct = () => {
        importeditpro.value.showModal();
    }

    const closemodal = () => {
        importeditpro.value.close();
    }

</script>

<style lang="scss" scoped>

</style>