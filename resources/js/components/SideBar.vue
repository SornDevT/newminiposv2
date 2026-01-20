<template>
  <ul class="menu bg-base-100 text-base-content min-h-full w-80 p-4 border-r border-base-300 gap-1">
    <li class="menu-title text-primary text-lg font-bold mb-4 flex flex-row items-center gap-2">
      <div class="p-2 bg-primary/10 rounded-lg">
        <ShoppingCartIcon class="w-6 h-6" />
      </div>
      <span>POS System</span>
    </li>

    <template v-for="(item, index) in menuItems" :key="index">
      <div v-if="item.divider" class="divider my-2"></div>

      <li v-if="item.children">
        <details :open="isChildActive(item)">
          <summary 
            :class="[
              'flex items-center gap-3 py-3 font-medium transition-colors',
              isChildActive(item) ? 'text-primary bg-primary/5' : ''
            ]"
          >
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.name }}
          </summary>
          <ul class="mt-1 ml-4 border-l-2 border-base-200 pl-2">
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
            route.path === item.path ? 'active bg-primary text-primary-content font-semibold' : ''
          ]"
        >
          <component :is="item.icon" class="w-5 h-5" />
          <span>{{ item.name }}</span>
        </router-link>
      </li>
    </template>
  </ul>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue' // ເພີ່ມ computed ເພື່ອໃຫ້ reactive
import { 
  HomeIcon, CubeIcon, ArrowDownTrayIcon, 
  ShoppingCartIcon, ChartBarIcon, Cog6ToothIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()

const menuItems = [
  { name: 'ໜ້າຫຼັກ', path: '/', icon: HomeIcon },
  { name: 'ສິນຄ້າ', path: '/products', icon: CubeIcon },
  { name: 'ນຳເຂົ້າ', path: '/stock-import', icon: ArrowDownTrayIcon },
  { name: 'ຂາຍ', path: '/pos', icon: ShoppingCartIcon },
  { name: 'ລາຍງານ', path: '/reports', icon: ChartBarIcon },
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

/**
 * ກວດສອບວ່າເຮົາຢູ່ພາຍໃຕ້ Path ຂອງ Sub-menu ນັ້ນໆຫຼືບໍ່
 */
const isChildActive = (item) => {
  if (!item.children) return false
  // ໃຊ້ .startsWith ເພື່ອໃຫ້ຄຸມທັງໝົດທີ່ຂຶ້ນຕົ້ນດ້ວຍ path ຂອງກຸ່ມນັ້ນ
  return item.children.some(child => route.path === child.path)
}
</script>