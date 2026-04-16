<script setup>
import { ref, onMounted } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import dashboardService from "@/services/dashboardService";

const summary = ref({ total_orders: 0, total_products: 0, total_users: 0 });
const isLoading = ref(true);
const error = ref("");

const fetchSummary = async () => {
  try {
    isLoading.value = true;
    const { data } = await dashboardService.getSummary();
    summary.value = data;
  } catch (err) {
    error.value = "Gagal mengambil data dashboard.";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchSummary();
});
</script>

<template>
  <AdminLayout>
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-slate-100">Ringkasan Sistem</h2>
    </div>

    <div v-if="isLoading" class="text-blue-400 animate-pulse font-medium">
      Memuat data...
    </div>
    <div
      v-else-if="error"
      class="bg-rose-500/10 text-rose-400 border border-rose-500/20 p-4 rounded-lg"
    >
      {{ error }}
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="bg-slate-800 rounded-xl border border-slate-700 p-6 flex items-center space-x-5 hover:border-slate-600 transition"
      >
        <div
          class="p-3.5 bg-blue-500/10 text-blue-400 rounded-xl border border-blue-500/20"
        >
          <svg
            class="w-7 h-7"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
            ></path>
          </svg>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-400">Total Order</p>
          <p class="text-3xl font-bold text-slate-100 mt-0.5">
            {{ summary.total_orders }}
          </p>
        </div>
      </div>

      <div
        class="bg-slate-800 rounded-xl border border-slate-700 p-6 flex items-center space-x-5 hover:border-slate-600 transition"
      >
        <div
          class="p-3.5 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20"
        >
          <svg
            class="w-7 h-7"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
            ></path>
          </svg>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-400">Total Produk</p>
          <p class="text-3xl font-bold text-slate-100 mt-0.5">
            {{ summary.total_products }}
          </p>
        </div>
      </div>

      <div
        class="bg-slate-800 rounded-xl border border-slate-700 p-6 flex items-center space-x-5 hover:border-slate-600 transition"
      >
        <div
          class="p-3.5 bg-purple-500/10 text-purple-400 rounded-xl border border-purple-500/20"
        >
          <svg
            class="w-7 h-7"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
            ></path>
          </svg>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-400">Total User</p>
          <p class="text-3xl font-bold text-slate-100 mt-0.5">
            {{ summary.total_users }}
          </p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
