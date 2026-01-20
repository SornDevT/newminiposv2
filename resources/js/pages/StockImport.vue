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

                <button class="btn btn-dash btn-info" @click="addeditproduct()"><PlusIcon class="w-5 h-5"/> ເພີ່ມສິນຄ້າໃໝ່</button>

            </div>

        </div>

        <div class="overflow-x-auto ">
            <table class="table border-1 border-gray-300 ">
                <!-- head -->
                <thead>
                <tr>
                    <th>#</th>
                    <th>ຮູບ</th>
                    <th>ຊື່ສິນຄ້າ</th>
                    <th>ໝວດໝູ່</th>
                    <th>ຈຳນວນ</th>
                    <th>ລາຄາຂາຍ</th>
                    <th>ຈັດການ</th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="item in 10" :key="item">
                    <th>1</th>
                    <td></td>
                    <td>ເບຍລາວ</td>
                    <td>ເບຍ</td>
                    <td>10</td>
                    <td>100,000</td>
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
        <dialog id="AddEditProduct" class="modal" ref="addeditpro" >
            <div class="modal-box">
                <h3 class="text-lg font-bold">ແບບຟອມ</h3>
                
                <form class="grid grid-cols-1 gap-4 mt-4">

                    <div class="flex justify-center">
                        <div class="avatar">
                            <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                <img src="https://placeimg.com/192/192/people" />
                            </div>
                        </div>
                    </div>

                   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text">ຊື່ສິນຄ້າ</span>
                        </label>
                        <input type="text" placeholder="ຊື່ສິນຄ້າ" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ໝວດໝູ່</span>
                        </label>    
                        <select class="select select-bordered w-full">
                            <option disabled selected>ເລືອກໝວດໝູ່</option>
                            <option>ເບຍ</option>
                            <option>ເຄື່ອງດື່ມ</option>
                            <option>ອາຫານ</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ຫົວໜ່ວຍ</span>
                        </label>    
                        <select class="select select-bordered w-full">
                            <option disabled selected>ເລືອກຫົວໜ່ວຍ</option>
                            <option>ແກ້ວ</option>
                            <option>ຂອງແພງ</option>
                            <option>ລູກ</option>
                        </select>
                    </div>  
                    <div>
                        <label class="label">
                            <span class="label-text">ລາຄາຂາຍ</span>
                        </label>
                        <input type="number" placeholder="ລາຄາຂາຍ" class="input input-bordered w-full" />   
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

    const addeditpro = ref(null);


    // function ----------------------------

    const addeditproduct = () => {
        addeditpro.value.showModal();
    }

    const closemodal = () => {
        addeditpro.value.close();
    }

</script>

<style lang="scss" scoped>

</style>