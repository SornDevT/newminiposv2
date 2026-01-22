<template>
    <div class="card bg-base-100 card-xs shadow-sm">
        <div class="card-body p-4">
            <div class="flex justify-between">
                <h1 class="text-xl">ຜູ້ໃຊ້ສໍາລັບລະບົບ</h1>
                <div class="flex gap-2">
                    <label class="input">
                        <MagnifyingGlassIcon class="w-5 h-5"/>
                        <input type="text" class="grow" placeholder="ຄົ້ນຫາ" />
                    </label>
                    <button class="btn btn-dash btn-info" @click="showAddEditModal()"><PlusIcon class="w-5 h-5"/> ເພີ່ມຜູ້ໃຊ້ໃໝ່</button>
                </div>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="table border-1 border-gray-300">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ຊື່ຜູ້ໃຊ້</th>
                            <th>ອີເມວ</th>
                            <th>ໂທລະສັບ</th>
                            <th>ບົດບາດ</th>
                            <th>ສະຖານະ</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <th>{{ user.id }}</th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-8 h-8">
                                            <img :src="user.avatar" :alt="user.name" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ user.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.phone }}</td>
                            <td>
                                <span :class="getRoleBadge(user.role)">{{ getRoleLabel(user.role) }}</span>
                            </td>
                            <td>
                                <span :class="user.status === 'active' ? 'badge badge-success' : 'badge badge-warning'">
                                    {{ user.status === 'active' ? 'ໃຊ້ງານ' : 'ປະຕິບັດ' }}
                                </span>
                            </td>
                            <td class="gap-2 flex">
                                <button class="btn btn-info p-2" @click="editUser(user)"><PencilSquareIcon class="w-4 h-4"/></button>
                                <button class="btn btn-error p-2" @click="deleteUser(user.id)"><TrashIcon class="w-4 h-4"/></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <dialog id="AddEditUser" class="modal" ref="addEditModal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">ແບບຟອມຜູ້ໃຊ້</h3>
            <form class="grid grid-cols-1 gap-4 mt-4">
                <div>
                    <label class="label">
                        <span class="label-text">ຊື່ຜູ້ໃຊ້</span>
                    </label>
                    <input type="text" placeholder="ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້" class="input input-bordered w-full" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text">ອີເມວ</span>
                        </label>
                        <input type="email" placeholder="example@mail.com" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ໂທລະສັບ</span>
                        </label>
                        <input type="tel" placeholder="+856" class="input input-bordered w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text">ລະຫັດຜ່ານ</span>
                        </label>
                        <input type="password" placeholder="ລະຫັດຜ່ານ" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ຢືນຢັນລະຫັດຜ່ານ</span>
                        </label>
                        <input type="password" placeholder="ຢືນຢັນລະຫັດຜ່ານ" class="input input-bordered w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text">ບົດບາດ</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled selected>ເລືອກບົດບາດ</option>
                            <option value="admin">ຜູ້ບໍລິຫານ</option>
                            <option value="user">ຜູ້ໃຊ້</option>
                            <option value="cashier">ຊັກແຊ</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">ສະຖານະ</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled selected>ເລືອກສະຖານະ</option>
                            <option value="active">ໃຊ້ງານ</option>
                            <option value="inactive">ປະຕິບັດ</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="modal-action">
                <div method="dialog">
                    <button class="btn btn-primary me-2">ບັນທຶກ</button>
                    <button class="btn" @click="closeModal">ຍົກເລີກ</button>
                </div>
            </div>
        </div>
    </dialog>
</template>

<script setup>
    import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
    import { ref } from 'vue';

    const addEditModal = ref(null);
    const users = ref([
        { id: 1, name: 'ສາຫວາດ ບົວບັນນະວົງ', email: 'sawaad@pos.com', phone: '+856 20 123 4567', role: 'admin', status: 'active', avatar: 'https://placeimg.com/192/192/people' },
        { id: 2, name: 'ວົງສະເພັດ ສຸກສະພັກ', email: 'vongsapet@pos.com', phone: '+856 20 987 6543', role: 'user', status: 'active', avatar: 'https://placeimg.com/192/192/people' },
        { id: 3, name: 'ອະຫວາຍ ວົງສາວັນ', email: 'ahway@pos.com', phone: '+856 20 555 1234', role: 'cashier', status: 'inactive', avatar: 'https://placeimg.com/192/192/people' },
    ]);

    const getRoleLabel = (role) => {
        const roles = {
            admin: 'ຜູ້ບໍລິຫານ',
            user: 'ຜູ້ໃຊ້',
            cashier: 'ຊັກແຊ'
        };
        return roles[role] || role;
    };

    const getRoleBadge = (role) => {
        const badges = {
            admin: 'badge badge-error',
            user: 'badge badge-info',
            cashier: 'badge badge-warning'
        };
        return badges[role] || 'badge';
    };

    const showAddEditModal = () => {
        addEditModal.value.showModal();
    };

    const closeModal = () => {
        addEditModal.value.close();
    };

    const editUser = (user) => {
        console.log('Edit:', user);
        showAddEditModal();
    };

    const deleteUser = (id) => {
        if (confirm('ທ່ານແນ່ໃຈບໍ?')) {
            console.log('Delete:', id);
            users.value = users.value.filter(u => u.id !== id);
        }
    };
</script>

<style lang="scss" scoped>

</style>