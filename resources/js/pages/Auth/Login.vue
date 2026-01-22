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
                                <span class="label-text font-semibold">ອີເມວ ຫຼື ຊື່ຜູ້ໃຊ້</span>
                            </label>
                            <input 
                                type="text" 
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
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="ກະລຸນາປ້ອນລະຫັດຜ່ານ"
                                v-model="form.password"
                                class="input input-bordered w-full"
                                required
                            />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex justify-between items-center">
                            <label class="label cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    v-model="form.remember"
                                    class="checkbox checkbox-sm checkbox-primary"
                                />
                                <span class="label-text ml-2">ຈື່ໄວ້</span>
                            </label>
                            <a href="#" class="link link-primary text-sm">ລືມລະຫັດຜ່ານ?</a>
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
                        <p class="text-center text-sm text-gray-600 mb-3">ໃຊ້ຂໍ້ມູນທົດສອບ:</p>
                        <div class="bg-base-200 p-3 rounded text-sm">
                            <p><strong>ຊື່ຜູ້ໃຊ້:</strong> admin</p>
                            <p><strong>ລະຫັດຜ່ານ:</strong> password</p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="alert alert-error mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ errorMessage }}</span>
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage" class="alert alert-success mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ successMessage }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-base-100">
                <p class="text-sm">© 2026 MiniPOS. ສະงວນສິດ.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';

    const form = ref({
        email: '',
        password: '',
        remember: false
    });

    const showPassword = ref(false);
    const isLoading = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');

    const handleLogin = async () => {
        errorMessage.value = '';
        successMessage.value = '';

        // Validation
        if (!form.value.email || !form.value.password) {
            errorMessage.value = 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ';
            return;
        }

        isLoading.value = true;

        try {
            // Demo login (replace with actual API call)
            if (form.value.email === 'admin' && form.value.password === 'password') {
                successMessage.value = 'ເຂົ້າສູ່ລະບົບສະເລັດ!';
                
                // Simulate API delay
                setTimeout(() => {
                    // Redirect to dashboard
                    console.log('Redirecting to dashboard...');
                    // router.push('/dashboard');
                }, 1500);
            } else {
                errorMessage.value = 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ';
            }
        } catch (error) {
            errorMessage.value = 'ເກີດຄວາມຜິດພາດ ກະລຸນາລອງໃໝ່';
            console.error('Login error:', error);
        } finally {
            isLoading.value = false;
        }
    };
</script>

<style lang="scss" scoped>
    
</style>