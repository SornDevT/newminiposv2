<template>
<div class="navbar bg-base-100 shadow-sm sticky top-0 z-30">
    <div class="flex-none lg:hidden">
      <label for="my-drawer" class="btn btn-square btn-ghost">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      </label>
    </div>
    
    <div class="flex-1">
      <a class="btn btn-ghost text-xl">MiniPOS</a>
    </div>
    
    <div class="flex-none gap-2">
      <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
          <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
            <span>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
        </div>
        <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
          <li class="menu-title">
            <span>{{ userName }}</span>
          </li>
          <li><a>Profile</a></li>
          <li><a @click="handleLogout">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const userName = computed(() => authStore.authUser?.name || 'Guest');

const handleLogout = async () => {
  await authStore.logout();
  router.push('/auth/login');
};
</script>