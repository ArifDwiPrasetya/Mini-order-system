<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useOrderStore } from "@/stores/order";
import AdminLayout from "@/layouts/AdminLayout.vue";

const route = useRoute();
const router = useRouter();
const orderStore = useOrderStore();

const orderId = route.params.id;
const isLoading = ref(true);
const error = ref("");

onMounted(async () => {
  try {
    await orderStore.fetchOrderDetail(orderId);
  } catch (err) {
    error.value = "Data order tidak ditemukan atau terjadi kesalahan.";
  } finally {
    isLoading.value = false;
  }
});

const formatPrice = (price) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(price);
const formatDate = (dateString) =>
  new Date(dateString).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
</script>

<template>
  <AdminLayout>
    <div class="mb-6">
      <button
        @click="router.push('/orders')"
        class="text-slate-400 hover:text-blue-400 text-sm font-medium flex items-center mb-4 transition"
      >
        &larr; Kembali ke Daftar Order
      </button>
      <h2 class="text-2xl font-bold text-slate-100">
        Detail Order #{{ orderId }}
      </h2>
    </div>

    <div v-if="isLoading" class="text-blue-400 animate-pulse font-medium">
      Memuat detail order...
    </div>
    <div
      v-else-if="error"
      class="bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-lg"
    >
      {{ error }}
    </div>

    <div v-else-if="orderStore.currentOrder" class="space-y-6">
      <div
        class="bg-slate-800 p-6 rounded-xl border border-slate-700 shadow-sm"
      >
        <h3
          class="text-lg font-bold text-slate-100 border-b border-slate-700 pb-3 mb-4"
        >
          Informasi Transaksi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-slate-400 mb-1">Dibuat Oleh</p>
            <p class="font-medium text-slate-100">
              {{ orderStore.currentOrder.user?.name }}
            </p>
          </div>
          <div>
            <p class="text-slate-400 mb-1">Tanggal & Waktu</p>
            <p class="font-medium text-slate-100">
              {{ formatDate(orderStore.currentOrder.created_at) }}
            </p>
          </div>
        </div>
      </div>

      <div
        class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-sm"
      >
        <div class="p-6 border-b border-slate-700 bg-slate-900/50">
          <h3 class="text-lg font-bold text-slate-100">Rincian Barang</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr
                class="text-slate-400 text-sm border-b border-slate-700 bg-slate-900/30"
              >
                <th class="p-4 font-medium">Produk</th>
                <th class="p-4 font-medium text-center">Harga Satuan</th>
                <th class="p-4 font-medium text-center">Qty</th>
                <th class="p-4 font-medium text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
              <tr
                v-for="item in orderStore.currentOrder.items"
                :key="item.id"
                class="hover:bg-slate-700/30 transition"
              >
                <td class="p-4">
                  <p class="font-medium text-slate-200">
                    {{ item.product?.name || "Produk Dihapus" }}
                  </p>
                </td>
                <td class="p-4 text-center text-slate-400">
                  {{ formatPrice(item.product?.price || 0) }}
                </td>
                <td class="p-4 text-center font-medium text-slate-200">
                  {{ item.qty }}
                </td>
                <td class="p-4 text-right font-bold text-slate-200">
                  {{ formatPrice(item.subtotal) }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-slate-900/80 border-t border-slate-700">
                <td
                  colspan="3"
                  class="p-5 text-right font-medium text-slate-400"
                >
                  Total Keseluruhan:
                </td>
                <td
                  class="p-5 text-right font-black text-emerald-400 text-xl tracking-tight"
                >
                  {{ formatPrice(orderStore.currentOrder.total_price) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
