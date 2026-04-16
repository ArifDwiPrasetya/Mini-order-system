<script setup>
import { ref, onMounted, watch } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import { useOrderStore } from "@/stores/order";

const orderStore = useOrderStore();
const isLoading = ref(false);
const error = ref("");
const search = ref("");

const fetchOrders = async (page = 1) => {
  try {
    isLoading.value = true;
    error.value = "";
    await orderStore.fetchOrders({
      search: search.value,
      page: page,
      per_page: orderStore.pagination.per_page,
    });
  } catch (err) {
    error.value = "Gagal mengambil data order.";
  } finally {
    isLoading.value = false;
  }
};

const formatPrice = (price) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(price);
const formatDate = (dateString) =>
  new Date(dateString).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });

onMounted(() => {
  fetchOrders();
});

let searchTimeout;
watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchOrders(1);
  }, 500);
});
</script>

<template>
  <AdminLayout>
    <div
      class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
    >
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Daftar Order</h2>
        <p class="text-slate-400 mt-1 text-sm">Riwayat transaksi pemesanan.</p>
      </div>

      <div class="flex items-center gap-4">
        <div class="relative">
          <input
            v-model="search"
            type="text"
            placeholder="Cari ID / Nama User..."
            class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 outline-none transition"
          />
          <svg
            class="w-5 h-5 text-slate-500 absolute left-3 top-3"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            ></path>
          </svg>
        </div>
        <RouterLink
          to="/orders/create"
          class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-900/20 transition whitespace-nowrap"
        >
          + Buat Order
        </RouterLink>
      </div>
    </div>

    <div
      v-if="error"
      class="bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-lg mb-6 text-sm"
    >
      {{ error }}
    </div>

    <div
      class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-sm"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-sm tracking-wide"
            >
              <th class="p-4 font-medium">Order ID</th>
              <th class="p-4 font-medium">Dibuat Oleh</th>
              <th class="p-4 font-medium">Tanggal</th>
              <th class="p-4 font-medium">Total Harga</th>
              <th class="p-4 font-medium text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/50">
            <tr v-if="isLoading" v-for="i in 3" :key="i" class="animate-pulse">
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-12"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-32"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-24"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-24"></div>
              </td>
              <td class="p-4 text-center">
                <div class="h-8 bg-slate-700 rounded-lg w-24 mx-auto"></div>
              </td>
            </tr>

            <tr v-else-if="orderStore.orders.length === 0">
              <td colspan="5" class="p-8 text-center text-slate-500">
                Belum ada order.
              </td>
            </tr>

            <tr
              v-else
              v-for="order in orderStore.orders"
              :key="order.id"
              class="hover:bg-slate-700/30 transition"
            >
              <td class="p-4 font-bold text-slate-300">&nbsp;{{ order.id }}</td>
              <td class="p-4 font-medium text-slate-100">
                {{ order.user?.name || "Sistem" }}
              </td>
              <td class="p-4 text-slate-400 text-sm">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="p-4 text-emerald-400 font-semibold">
                {{ formatPrice(order.total_price) }}
              </td>
              <td class="p-4 text-center">
                <RouterLink
                  :to="`/orders/${order.id}`"
                  class="inline-block text-blue-400 hover:text-blue-300 font-medium text-sm bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 px-4 py-1.5 rounded-md transition"
                >
                  Detail
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        class="bg-slate-900/50 border-t border-slate-700 p-4 flex flex-col sm:flex-row items-center justify-between gap-4"
      >
        <span class="text-sm text-slate-400"
          >Total
          <span class="font-bold text-slate-200">{{
            orderStore.pagination.total
          }}</span>
          order</span
        >
        <div class="flex space-x-2">
          <button
            @click="fetchOrders(orderStore.pagination.current_page - 1)"
            :disabled="orderStore.pagination.current_page === 1 || isLoading"
            class="px-4 py-2 border border-slate-600 rounded-lg text-sm text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
          >
            &laquo; Sebelumnya
          </button>
          <button
            @click="fetchOrders(orderStore.pagination.current_page + 1)"
            :disabled="
              orderStore.pagination.current_page ===
                orderStore.pagination.last_page || isLoading
            "
            class="px-4 py-2 border border-slate-600 rounded-lg text-sm text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
          >
            Selanjutnya &raquo;
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
