<script setup>
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  const result = await Swal.fire({
    title: "Keluar dari Sistem?",
    text: "Sesi Anda akan diakhiri dan Anda harus login kembali.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, Logout",
    cancelButtonText: "Batal",
    buttonsStyling: false,
    reverseButtons: true,
    background: "#1e293b",
    color: "#f1f5f9",
    customClass: {
      popup: "border border-slate-700 rounded-2xl shadow-2xl",
      title: "text-xl font-bold",
      htmlContainer: "text-slate-400 text-sm",
      confirmButton:
        "bg-rose-600 hover:bg-rose-500 text-white px-5 py-2.5 rounded-lg font-semibold ml-3 transition outline-none",
      cancelButton:
        "bg-slate-700 hover:bg-slate-600 text-slate-200 px-5 py-2.5 rounded-lg font-semibold transition outline-none",
    },
  });

  if (result.isConfirmed) {
    await authStore.logout();
  }
  //Router push sudah ditangani forceLogout di pinia
};
</script>

<template>
  <div class="min-h-screen bg-slate-900 flex flex-col font-sans">
    <header
      class="bg-slate-950 border-b border-slate-800 text-slate-100 sticky top-0 z-50"
    >
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center"
      >
        <h1
          class="text-xl font-bold tracking-wider bg-clip-text text-transparent bg-linear-to-r from-blue-400 to-emerald-400"
        >
          Mini Order System
        </h1>

        <nav class="hidden md:flex space-x-6 text-sm font-medium">
          <RouterLink
            to="/dashboard"
            class="text-slate-400 hover:text-blue-400 transition"
            active-class="text-blue-400 font-semibold"
            >Dashboard</RouterLink
          >
          <RouterLink
            to="/products"
            class="text-slate-400 hover:text-blue-400 transition"
            active-class="text-blue-400 font-semibold"
            >Produk</RouterLink
          >
          <RouterLink
            to="/orders"
            class="text-slate-400 hover:text-blue-400 transition"
            active-class="text-blue-400 font-semibold"
            >Order</RouterLink
          >
        </nav>

        <div class="flex items-center space-x-4">
          <span class="text-sm text-slate-400 hidden sm:inline-block">
            Halo,
            <span class="text-slate-200 font-medium">{{
              authStore.user?.name || "Admin"
            }}</span>
          </span>
          <button
            @click="handleLogout"
            class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 border border-rose-500/30 px-4 py-1.5 rounded-md text-sm font-semibold transition"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>
