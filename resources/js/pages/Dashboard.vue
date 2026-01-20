<template>
    <div class=" p-4 bg-gray-50 dark:bg-gray-900">
        <h1 class=" text-3xl font-bold text-gray-900 dark:text-white mb-6">ພາບລວມ  </h1>

        <!-- ສະຖິຕິ card -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            <div class="card  bg-base-100 card-xs shadow-sm">
            <div class="card-body p-5">
                <div class=" flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-1">ຍອດຂາຍທັງໝົດ</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₭ </p>
                        </div>
                        <div>
                          <CurrencyDollarIcon class="w-10 h-10 text-blue-600 dark:text-blue-300" />
                        </div>
                </div>
            </div>
            </div>

            <div class="card  bg-base-100 card-xs shadow-sm">
            <div class="card-body p-5">
                <div class=" flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-1">ສິນຄ້າທັງໝົດ</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₭ </p>
                        </div>
                        <div>
                          <ArchiveBoxIcon class="w-10 h-10 text-blue-600 dark:text-blue-300" />
                        </div>
                </div>
            </div>
            </div>

            <div class="card  bg-base-100 card-xs shadow-sm">
            <div class="card-body p-5">
                <div class=" flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-1">ກຳໄລ</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₭ </p>
                        </div>
                        <div>
                          <ChartBarIcon class="w-10 h-10 text-blue-600 dark:text-blue-300" />
                        </div>
                </div>
            </div>
            </div>

            <div class="card  bg-base-100 card-xs shadow-sm">
            <div class="card-body p-5">
                <div class=" flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-1">ຜູ້ໃຊ້</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₭ </p>
                        </div>
                        <div>
                          <UserPlusIcon class="w-10 h-10 text-blue-600 dark:text-blue-300" />
                        </div>
                </div>
            </div>
            </div>

            
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card  bg-base-100 card-xs shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-xl">ກິດຈະກຳລ່າສຸດ</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="table">
                            <!-- head -->
                            <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Job</th>
                                <th>Favorite Color</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th>1</th>
                                <td>Cy Ganderton</td>
                                <td>Quality Control Specialist</td>
                                <td>Blue</td>
                            </tr>
                            <!-- row 2 -->
                            <tr>
                                <th>2</th>
                                <td>Hart Hagerty</td>
                                <td>Desktop Support Technician</td>
                                <td>Purple</td>
                            </tr>
                            <!-- row 3 -->
                            <tr>
                                <th>3</th>
                                <td>Brice Swyre</td>
                                <td>Tax Accountant</td>
                                <td>Red</td>
                            </tr>
                            </tbody>
                        </table>
                        </div>

                </div>
            </div>

            <div class="card  bg-base-100 card-xs shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-xl">ການສັ່ງຊື້ດ່ວນ</h2>
                    <div class=" grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button class="btn btn-active btn-primary p-8 rounded-2xl">
                         <ShoppingCartIcon class="w-6 h-6 me-2" /> ຂາຍສິນຄ້າ</button>
                        <button class="btn btn-active btn-success p-8 rounded-2xl">
                        <ArchiveBoxIcon class="w-6 h-6 me-2" /> ຈັດການສິນຄ້າ</button>
                        <button class="btn btn-active btn-warning p-8 rounded-2xl">
                        <UserIcon class="w-6 h-6 me-2" /> ຈັດການຜູ້ໃຊ້</button>
                        <button class="btn btn-active btn-accent p-8 rounded-2xl">
                        <DocumentTextIcon class="w-6 h-6 me-2" />ລາຍງານ</button>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
</template>

<script setup>
    import { CurrencyDollarIcon, ArchiveBoxIcon, ChartBarIcon, UserPlusIcon, ShoppingCartIcon, DocumentTextIcon, UserIcon } from '@heroicons/vue/24/outline'

</script>

<style lang="scss" scoped>

</style>