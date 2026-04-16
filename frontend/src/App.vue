<script setup>
import { RouterView } from "vue-router";
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
onMounted(async () => {
  //untuk menangani kasus ketika user sudah login tapi refresh halaman, maka kita perlu fetch data user untuk memastikan state aplikasi tetap konsisten
  if (authStore.accessToken) {
    await authStore.fetchUser();
  }
});
</script>

<template>
  <RouterView />
</template>

<style lang="postcss">
body {
  @apply bg-slate-900 text-slate-200 antialiased;
}
</style>
