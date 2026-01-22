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
          <select class="select select-sm select-bordered w-48">
            <option disabled selected>ທຸກໝວດໝູ່</option>
            <option>ເຄື່ອງດື່ມ</option>
            <option>ອາຫານ</option>
          </select>
        </div>

        <div class="overflow-y-auto flex-1 pr-2 pb-2">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <div 
              v-for="product in filteredProducts" 
              :key="product.id" 
              class="card card-compact bg-base-100 shadow border border-base-200 hover:shadow-lg hover:border-primary cursor-pointer transition-all active:scale-95 group"
              @click="addToCart(product)"
            >
              <figure class="h-28 bg-gray-100 relative">
                 <img :src="product.image" :alt="product.name" class="object-cover h-full w-full" />
                 
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
            <button class="btn btn-primary w-full btn-lg shadow-lg text-white" :disabled="cart.length === 0">
                ຊຳລະເງິນ (Pay)
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const products = ref([
  { id: 1, name: 'ເບຍລາວ (ນ້ອຍ)', price: 12000, image: 'https://placehold.co/200x200/fcd34d/black?text=BeerLao' },
  { id: 2, name: 'ເບຍລາວ (ໃຫຍ່)', price: 15000, image: 'https://placehold.co/200x200/fcd34d/black?text=Big+Beer' },
  { id: 3, name: 'ນ້ຳດື່ມ ຫົວເສືອ', price: 5000, image: 'https://placehold.co/200x200/3b82f6/white?text=Water' },
  { id: 4, name: 'ເປັບຊີ', price: 8000, image: 'https://placehold.co/200x200/2563eb/white?text=Pepsi' },
  { id: 5, name: 'ຕຳໝາກຫຸ່ງ', price: 25000, image: 'https://placehold.co/200x200/ef4444/white?text=Papaya' },
  { id: 6, name: 'ເຂົ້າໜຽວ', price: 5000, image: 'https://placehold.co/200x200/f3f4f6/black?text=Rice' },
  { id: 7, name: 'ປີ້ງໄກ່', price: 45000, image: 'https://placehold.co/200x200/d97706/white?text=Chicken' },
  { id: 8, name: 'ເບຍອາມາຊອນ', price: 20000, image: 'https://placehold.co/200x200/10b981/white?text=Amz+Beer' },
]);

const cart = ref([]);
const searchQuery = ref('');
const currentDate = new Date().toLocaleDateString('lo-LA');

const filteredProducts = computed(() => {
    return products.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const totalPrice = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.qty), 0);
});

const cartTotalQty = computed(() => {
    return cart.value.reduce((total, item) => total + item.qty, 0);
});

// ✨✨✨ ຟັງຊັນໃໝ່ສຳລັບສະແດງຈຳນວນໃນ Card ✨✨✨
const getProductQty = (productId) => {
    const item = cart.value.find(i => i.id === productId);
    return item ? item.qty : 0;
};

const formatPrice = (value) => {
  return new Intl.NumberFormat('lo-LA', { style: 'currency', currency: 'LAK' }).format(value);
};

const addToCart = (product) => {
    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.value.push({ ...product, qty: 1 });
    }
};

const increaseQty = (item) => {
    item.qty++;
};

const decreaseQty = (item) => {
    if (item.qty > 1) {
        item.qty--;
    } else {
        cart.value = cart.value.filter(c => c.id !== item.id);
    }
};

const removeFromCart = (index) => {
    cart.value.splice(index, 1);
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