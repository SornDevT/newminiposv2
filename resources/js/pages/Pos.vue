<template>
  <div class="card bg-base-100 shadow-xl h-[calc(100vh-2rem)]">
    <div class="card-body p-4 flex flex-row gap-4 h-full overflow-hidden">
      
      <div class="w-2/3 flex flex-col h-full">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-primary">POS - ຂາຍໜ້າຮ້ານ</h2>
            <div class="badge badge-outline">{{ currentDate }}</div>
        </div>
        
        <div class="flex gap-2 mb-4 bg-base-200 p-2 rounded-lg">
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="ຄົ້ນຫາຊື່ສິນຄ້າ..." 
            class="input input-sm input-bordered w-full" 
          />
          <select v-model="selectedCategory" class="select select-sm select-bordered w-48">
            <option value="all">ທຸກໝວດໝູ່</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div class="overflow-y-auto flex-1 pr-2 pb-2">
            <div v-if="loading" class="text-center p-10">ກຳລັງໂຫລດຂໍ້ມູນ...</div>
            <div v-else-if="filteredProducts.length === 0" class="text-center p-10">ບໍ່ພົບສິນຄ້າ</div>
            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <div 
              v-for="product in filteredProducts" 
              :key="product.id" 
              class="card card-compact bg-base-100 shadow border border-base-200 hover:shadow-lg hover:border-primary cursor-pointer transition-all active:scale-95 group"
              @click="addToCart(product)"
            >
              <figure class="h-28 bg-gray-100 relative">
                 <img :src="getProductImage(product)" :alt="product.name" class="object-cover h-full w-full" />
                 
                 <div v-if="getProductQty(product.id) > 0" class="absolute inset-0 bg-black/10 flex items-center justify-center backdrop-blur-[1px]">
                    <span class="badge badge-secondary badge-lg text-lg font-bold shadow-xl scale-125">
                        {{ getProductQty(product.id) }}
                    </span>
                 </div>

                 <div class="absolute bottom-0 right-0 bg-primary text-white text-xs px-2 py-1 rounded-tl-lg shadow-sm">
                    {{ formatPrice(product.price) }}
                 </div>
              </figure>

              <div class="card-body p-3 text-center">
                <h3 class="font-bold text-sm line-clamp-1">{{ product.name }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="w-1/3 bg-base-200 rounded-box flex flex-col h-full shadow-inner border border-base-300">
        <div class="p-4 bg-base-300 rounded-t-box flex justify-between items-center">
            <h3 class="font-bold text-lg">ລາຍການສັ່ງຊື້</h3>
            <div class="badge badge-primary badge-lg">{{ cartTotalQty }} ລາຍການ</div>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-2">
            <div v-if="cart.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 opacity-50">
                <p>ຍັງບໍ່ມີສິນຄ້າໃນກະຕ່າ</p>
            </div>

            <div v-for="(item, index) in cart" :key="index" class="bg-base-100 p-3 rounded-lg shadow-sm flex flex-col gap-2 relative group">
                <button @click="removeFromCart(index)" class="btn btn-xs btn-circle btn-error absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                <div class="flex justify-between items-start">
                    <span class="font-bold text-sm w-2/3">{{ item.name }}</span>
                    <span class="font-semibold text-sm">{{ formatPrice(item.price * item.qty) }}</span>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <div class="text-xs text-gray-500">{{ formatPrice(item.price) }} / 1</div>
                    <div class="join border border-base-300 rounded-lg">
                        <button class="join-item btn btn-xs btn-ghost px-2" @click.stop="decreaseQty(item)">-</button>
                        <span class="join-item bg-base-100 px-3 text-sm flex items-center font-bold">{{ item.qty }}</span>
                        <button class="join-item btn btn-xs btn-ghost px-2" @click.stop="increaseQty(item)">+</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white rounded-b-box shadow-[0_-5px_10px_rgba(0,0,0,0.05)] z-10">
            <div class="flex justify-between items-center mb-4 text-2xl font-bold text-primary">
                <span>ລວມທັງໝົດ:</span>
                <span>{{ formatPrice(totalPrice) }}</span>
            </div>
            <button @click="openPaymentModal" class="btn btn-primary w-full btn-lg shadow-lg text-white" :disabled="cart.length === 0">
                ຊຳລະເງິນ (Pay)
            </button>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <dialog class="modal" :class="{'modal-open': showPaymentModal}">
      <div class="modal-box">
        <h3 class="font-bold text-lg">ຊຳລະເງິນ</h3>
        <div class="py-4 space-y-4">
            <div class="text-center">
                <p class="text-gray-500">ຍອດລວມທີ່ຕ້ອງຊຳລະ</p>
                <p class="text-4xl font-bold text-primary">{{ formatPrice(totalPrice) }}</p>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">ຮັບເງິນສົດ (LAK)</span>
                </label>
                <input 
                    type="number" 
                    v-model.number="cashReceived"
                    placeholder="0" 
                    class="input input-bordered input-lg text-center text-2xl font-bold"
                    @keyup.enter="confirmPayment"
                />
            </div>
            <div v-if="cashReceived > 0" class="text-center">
                <p class="text-gray-500">ເງິນທອນ</p>
                <p class="text-3xl font-bold" :class="change >= 0 ? 'text-success' : 'text-error'">
                    {{ formatPrice(change) }}
                </p>
            </div>
        </div>
        <div class="modal-action">
            <button class="btn" @click="closePaymentModal">ຍົກເລີກ</button>
            <button 
                class="btn btn-primary" 
                @click="confirmPayment"
                :disabled="change < 0 || cart.length === 0"
            >
                ຢືນຢັນການຊຳລະ
            </button>
        </div>
      </div>
    </dialog>

    <!-- Confirmation Modal -->
    <dialog class="modal" :class="{'modal-open': showConfirmModal}">
      <div class="modal-box">
        <h3 class="font-bold text-lg">ຢືນຢັນການລຶບ</h3>
        <p class="py-4">ທ່ານຕ້ອງການລຶບລາຍການນີ້ອອກຈາກກະຕ່າແທ້ບໍ?</p>
        <div class="modal-action">
            <button class="btn" @click="closeModal">ຍົກເລີກ</button>
            <button class="btn btn-error" @click="confirmRemove">ລຶບອອກ</button>
        </div>
      </div>
    </dialog>

    <!-- Success Modal -->
    <dialog class="modal" :class="{'modal-open': showSuccessModal}">
        <div class="modal-box text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-success mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="font-bold text-2xl mt-4">ການຂາຍສຳເລັດ!</h3>
            <div class="py-4">
                <p class="text-gray-500">ເງິນທອນ</p>
                <p class="text-4xl font-bold text-success">{{ formatPrice(lastSaleInfo?.change_due || 0) }}</p>
            </div>
            <div class="modal-action justify-center">
                <button class="btn btn-primary btn-wide" @click="closeSuccessModal">ຂາຍໃໝ່</button>
            </div>
        </div>
    </dialog>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../api/axios'; // '@/api/axios' is often an alias, but let's use relative path for clarity

// Refs for data and UI state
const products = ref([]);
const categories = ref([]);
const cart = ref([]);
const searchQuery = ref('');
const selectedCategory = ref('all');
const loading = ref(true); // Loading state
const currentDate = new Date().toLocaleDateString('lo-LA');

// States for confirmation modal
const showConfirmModal = ref(false);
const itemToRemoveIndex = ref(null);

// States for Payment Modal
const showPaymentModal = ref(false);
const cashReceived = ref(0);

// States for Success Modal
const showSuccessModal = ref(false);
const lastSaleInfo = ref(null);


// Fetching data on component mount
onMounted(async () => {
    await fetchProducts();
    await fetchCategories();
    loading.value = false; // Set loading to false after data is fetched
});

const fetchProducts = async () => {
    try {
        const response = await api.get('/products', { params: { page: -1 } }); // Request all products
        products.value = response.data; // Now response.data should be the array of products
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};

const fetchCategories = async () => {
    try {
        const response = await api.get('/categories', { params: { page: -1 } });
        categories.value = response.data;
    } catch (error)        {
        console.error('Error fetching categories:', error);
    }
};

// Computed property for filtering products
const filteredProducts = computed(() => {
    let filtered = products.value;

    // Filter by selected category
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(p => p.category_id == selectedCategory.value);
    }

    // Filter by search query
    if (searchQuery.value) {
        filtered = filtered.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
    }

    return filtered;
});

// Computed properties for cart
const totalPrice = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.qty), 0);
});

const cartTotalQty = computed(() => {
    return cart.value.reduce((total, item) => total + item.qty, 0);
});

// Computed property for payment
const change = computed(() => {
    return (cashReceived.value || 0) - totalPrice.value;
});

// Get product quantity in cart
const getProductQty = (productId) => {
    const item = cart.value.find(i => i.id === productId);
    return item ? item.qty : 0;
};

// Formatting and cart management functions
const formatPrice = (value) => {
  return new Intl.NumberFormat('lo-LA', { style: 'currency', currency: 'LAK' }).format(value);
};

const getProductImage = (product) => {
    return product.image || 'https://placehold.co/200x200/e2e8f0/black?text=No+Image';
}

const addToCart = (product) => {
    if (product.stock_quantity <= 0) {
        alert('ສິນຄ້າໝົດແລ້ວ!');
        return;
    }
    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        if (existingItem.qty >= product.stock_quantity) {
            alert('ຈຳນວນສິນຄ້າໃນສະຕ໋ອກບໍ່ພຽງພໍ!');
            return;
        }
        existingItem.qty++;
    } else {
        cart.value.push({ ...product, qty: 1 });
    }
};

const increaseQty = (item) => {
    const productInStock = products.value.find(p => p.id === item.id);
    if (item.qty >= productInStock.stock_quantity) {
        alert('ຈຳນວນສິນຄ້າໃນສະຕ໋ອກບໍ່ພຽງພໍ!');
        return;
    }
    item.qty++;
};

const decreaseQty = (item) => {
    if (item.qty > 1) {
        item.qty--;
    } else {
        // If qty is 1, ask for confirmation to remove
        removeFromCart(cart.value.findIndex(c => c.id === item.id));
    }
};

const removeFromCart = (index) => {
    itemToRemoveIndex.value = index;
    showConfirmModal.value = true;
};

const confirmRemove = () => {
    if (itemToRemoveIndex.value !== null) {
        cart.value.splice(itemToRemoveIndex.value, 1);
    }
    closeModal();
};

const closeModal = () => {
    showConfirmModal.value = false;
    itemToRemoveIndex.value = null;
};

// Payment Modal functions
const openPaymentModal = () => {
    cashReceived.value = 0;
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    cashReceived.value = 0;
};

const closeSuccessModal = () => {
    showSuccessModal.value = false;
    lastSaleInfo.value = null;
};

const confirmPayment = async () => {
    if (change.value < 0 || cart.value.length === 0) {
        alert("ກະລຸນາກວດສອບຈຳນວນເງິນທີ່ໄດ້ຮັບ ຫຼື ລາຍການສິນຄ້າໃນກະຕ່າ."); // More user-friendly feedback
        console.error("Cannot process payment. Check cart and cash received.");
        return;
    }

    // Double check stock before final submission
    for (const item of cart.value) {
        const productInStock = products.value.find(p => p.id === item.id);
        if (!productInStock || item.qty > productInStock.stock_quantity) {
            alert(`ສິນຄ້າ "${item.name}" ໃນສະຕ໋ອກບໍ່ພຽງພໍ!`);
            return;
        }
    }

    try {
        const payload = {
            customer_id: null, // For now, assume no customer selection on POS. Can be added later.
            discount: 0, // No discount functionality implemented yet
            cart_items: cart.value.map(item => ({ 
                id: item.id,
                qty: item.qty, 
                price: item.price 
            })),
            payments: [{
                amount: totalPrice.value, // Pay exactly the total amount
                method: 'cash', // Assuming cash payment for now
            }],
            total_amount: totalPrice.value,
            total_received: cashReceived.value,
            change_due: change.value,
        };

        const response = await api.post('/sales', payload);
        
        // On successful payment:
        lastSaleInfo.value = response.data.sale;

        // Update stock quantity in local state
        cart.value.forEach(cartItem => {
            const product = products.value.find(p => p.id === cartItem.id);
            if (product) {
                product.stock_quantity -= cartItem.qty;
            }
        });

        cart.value = []; // Clear the cart
        closePaymentModal(); // Close the payment modal
        showSuccessModal.value = true; // Show success modal

    } catch (error) {
        console.error('Error processing payment:', error);
        // Display more specific error if possible
        if (error.response && error.response.data && error.response.data.message) {
            alert('ເກີດຂໍ້ຜິດພາດ: ' + error.response.data.message);
            if (error.response.data.errors) {
                console.error('Validation errors:', error.response.data.errors);
            }
        } else {
            alert('ເກີດຂໍ້ຜິດພາດໃນການຊຳລະເງິນ!'); 
        }
    }
};
</script>

<style lang="scss" scoped>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  border-radius: 4px;
}
</style>