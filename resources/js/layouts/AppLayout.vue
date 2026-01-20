<template>
 <div class="drawer lg:drawer-open min-h-screen bg-base-200">
    <input id="my-drawer" type="checkbox" class="drawer-toggle" />
    
    <div class="drawer-content flex flex-col">
      
      <Header />

      <main class="flex-1 p-4 lg:p-6">
        <div class="max-w-7xl mx-auto w-full">
          <router-view />
        </div>
      </main>

      <Footer />
    </div>

    <div class="drawer-side z-40">
      <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
      <SideBar />
    </div>
  </div>
</template>

<script setup>

    import Header from '../components/Header.vue';
    import SideBar from '../components/SideBar.vue';
    import Footer from '../components/Footer.vue';


</script>

<style lang="scss" scoped>

</style>