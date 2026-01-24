<template>
    <div class="min-h-screen bg-gradient-to-br from-primary to-primary-focus flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="card bg-base-100 shadow-2xl">
                <div class="card-body p-8">
                    <!-- Logo/Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-primary mb-2">MiniPOS</h1>
                        <p class="text-gray-600">ລະບົບຈັດການຂາຍສົ່ວນໜ້ອຍ</p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="handleLogin" class="space-y-4">
                        <!-- Email/Username -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold">ອີເມວ</span>
                            </label>
                            <input 
                                type="email" 
                                placeholder="user@example.com"
                                v-model="form.email"
                                class="input input-bordered w-full"
                                required
                            />
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold">ລະຫັດຜ່ານ</span>
                            </label>
                            <input 
                                type="password"
                                placeholder="ກະລຸນາປ້ອນລະຫັດຜ່ານ"
                                v-model="form.password"
                                class="input input-bordered w-full"
                                required
                            />
                        </div>

                        <!-- Error Message -->
                        <div v-if="errorMessage" class="text-error text-sm mt-2">
                           {{ errorMessage }}
                        </div>


                        <!-- Login Button -->
                        <button 
                            type="submit"
                            class="btn btn-primary w-full"
                            :disabled="isLoading"
                        >
                            <span v-if="!isLoading">ເຂົ້າສູ່ລະບົບ</span>
                            <span v-else class="loading loading-spinner loading-sm"></span>
                        </button>
                    </form>

                     <!-- Divider -->
                     <div class="divider">ຫຼື</div>

                    <!-- Demo Login -->
                    <div class="space-y-2">
                        <p class="text-center text-sm text-gray-600 mb-3">ບໍ່ມີບັນຊີ? <a href="#" class="link link-primary">ລົງທະບຽນ</a></p>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-base-100">
                <p class="text-sm">© 2026 MiniPOS. ສະງວນສິດ.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { useRouter } from 'vue-router'
    import { useAuthStore } from '../../stores/auth';

    const router = useRouter()
    const authStore = useAuthStore()

    const form = ref({
        email: 'admin@example.com',
        password: 'password',
    });

    const isLoading = ref(false);
    const errorMessage = ref('');

    const handleLogin = async () => {
        errorMessage.value = '';
        isLoading.value = true;
        try {
            await authStore.login(form.value)
            router.push('/')
        } catch (error) {
            errorMessage.value = 'ອີເມວ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ';
            console.error('Login error:', error);
        } finally {
            isLoading.value = false;
        }
    };
</script>