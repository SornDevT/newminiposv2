<template>
  <ul 
    class="menu bg-base-100 text-base-content min-h-full p-4 border-r border-base-300 gap-1 transition-all duration-300 ease-in-out"
    :class="[isPosPage ? 'w-20 px-2' : 'w-80']"
  >
    <li class="menu-title text-primary text-lg font-bold mb-4 flex flex-row items-center gap-2 overflow-hidden whitespace-nowrap">
      <div class="p-2 bg-primary/10 rounded-lg flex-shrink-0">
        <ShoppingCartIcon class="w-6 h-6" />
      </div>
      <span v-show="!isPosPage" class="transition-opacity duration-300">POS System</span>
    </li>

    <template v-for="(item, index) in menuItems" :key="index">
      <div v-if="item.divider" class="divider my-2"></div>

      <li v-if="item.children">
        <details :open="isChildActive(item)">
          <summary 
            :class="[
              'flex items-center gap-3 py-3 font-medium transition-colors',
              isChildActive(item) ? 'text-primary bg-primary/5' : '',
              isPosPage ? 'justify-center px-0' : '' 
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            
            <span v-show="!isPosPage">{{ item.name }}</span>
          </summary>
          
          <ul v-show="!isPosPage" class="mt-1 ml-4 border-l-2 border-base-200 pl-2">
            <li v-for="child in item.children" :key="child.path">
              <router-link 
                :to="child.path"
                active-class="active bg-primary/10 text-primary font-bold"
                class="py-2 my-1"
              >
                {{ child.name }}
              </router-link>
            </li>
          </ul>
        </details>
      </li>

      <li v-else>
        <router-link 
          :to="item.path"
          :class="[
            'flex items-center gap-3 py-3 transition-all duration-200',
            route.path === item.path ? 'active bg-primary text-primary-content font-semibold' : '',
            isPosPage ? 'justify-center' : '' 
          ]"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
          
          <span v-show="!isPosPage" class="whitespace-nowrap">{{ item.name }}</span>
        </router-link>
      </li>
    </template>
  </ul>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import { 
  HomeIcon, CubeIcon, ArrowDownTrayIcon, 
  ShoppingCartIcon, ChartBarIcon, Cog6ToothIcon, UsersIcon, ClipboardDocumentListIcon, BanknotesIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()

// 2. ສ້າງ Computed Property ເພື່ອກວດສອບວ່າແມ່ນໜ້າ POS ບໍ່
const isPosPage = computed(() => {
  return route.path === '/pos'
})

const menuItems = [
  { name: 'ໜ້າຫຼັກ', path: '/', icon: HomeIcon },
  { name: 'ສິນຄ້າ', path: '/products', icon: CubeIcon },
  { name: 'ນຳເຂົ້າ', path: '/stock-import', icon: ArrowDownTrayIcon },
  { name: 'ຂາຍ', path: '/pos', icon: ShoppingCartIcon },
  { name: 'ລາຍງານ', path: '/reports', icon: ChartBarIcon },
  { name: 'ລູກຄ້າ', path: '/customers', icon: UsersIcon },
  { name: 'ຜູ້ສະໜອງ', path: '/suppliers', icon: ClipboardDocumentListIcon },
  { name: 'ລາຍຈ່າຍ', path: '/expenses', icon: BanknotesIcon },
  { 
    name: 'ຕັ້ງຄ່າ', 
    icon: Cog6ToothIcon, 
    divider: true,
    children: [
      { name: 'ໝວດໝູ່ຫົວໜ່ວຍ', path: '/settings/categories-unit' },
      { name: 'ຈັດການຜູ້ໃຊ້', path: '/settings/users' }
    ]
  },
]

const isChildActive = (item) => {
  if (!item.children) return false
  return item.children.some(child => route.path === child.path)
}
</script>

<style scoped>
/* ເຊື່ອງເຄື່ອງໝາຍສາມຫຼ່ຽມຂອງ details/summary ເວລາໜ້າຈໍນ້ອຍ */
details > summary {
  list-style: none;
}
details > summary::-webkit-details-marker {
  display: none;
}
</style>