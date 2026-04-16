<script setup>
import { ref, onMounted, watch } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import productService from "@/services/productService";
import Swal from "sweetalert2";

const products = ref([]);
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 10,
});
const isLoading = ref(false);
const error = ref("");
const search = ref("");

const fetchProducts = async (page = 1) => {
  try {
    isLoading.value = true;
    error.value = "";
    const { data } = await productService.getProducts({
      search: search.value,
      page: page,
      per_page: pagination.value.per_page,
    });
    products.value = data.data;
    console.log(products.value);
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      total: data.total,
      per_page: data.per_page,
    };
  } catch (err) {
    error.value = "Gagal mengambil data produk.";
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

onMounted(() => {
  fetchProducts();
});

const handleDelete = async (id, name) => {
  const result = await Swal.fire({
    title: "Apakah Anda yakin?",
    text: `Produk "${name}" akan dihapus`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    background: "#1e293b",
    color: "#f1f5f9",
    confirmButtonText: "Ya, hapus!",
    cancelButtonText: "Batal",
  });

  if (result.isConfirmed) {
    try {
      isLoading.value = true;
      await productService.deleteProduct(id);
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Produk berhasil dihapus!",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: "#1e293b",
        color: "#f1f5f9",
        customClass: {
          popup: "border border-slate-700 rounded-xl shadow-2xl",
        },
      });

      fetchProducts(pagination.value.current_page);
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Gagal menghapus produk",
        text: err.response?.data?.message || "Terjadi kesalahan.",
        background: "#1e293b",
        color: "#f1f5f9",
        customClass: {
          popup: "border border-slate-700 rounded-xl shadow-2xl",
        },
      });
    } finally {
      isLoading.value = false;
    }
  }
};

let searchTimeout;
watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchProducts(1);
  }, 500);
});
</script>

<template>
  <AdminLayout>
    <div
      class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
    >
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Daftar Produk</h2>
        <p class="text-slate-400 mt-1 text-sm">
          Kelola dan lihat persediaan produk.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row items-center gap-4">
        <div class="relative w-full sm:w-auto">
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama produk..."
            class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
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
          to="/products/create"
          class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition whitespace-nowrap text-center"
        >
          + Tambah Produk
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
              <th class="p-4 font-medium">ID Produk</th>
              <th class="p-4 font-medium">Nama Produk</th>
              <th class="p-4 font-medium">Harga</th>
              <th class="p-4 font-medium">Stok</th>
              <th class="p-4 font-medium text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/50">
            <tr v-if="isLoading" v-for="i in 5" :key="i" class="animate-pulse">
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-8"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-3/4"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-1/2"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-12"></div>
              </td>
              <td class="p-4">
                <div class="h-4 bg-slate-700 rounded w-24 ml-auto"></div>
              </td>
            </tr>

            <tr v-else-if="products.length === 0">
              <td colspan="5" class="p-8 text-center text-slate-500">
                Tidak ada produk ditemukan.
              </td>
            </tr>

            <tr
              v-else
              v-for="product in products"
              :key="product.id"
              class="hover:bg-slate-700/30 transition"
            >
              <td class="p-4 font-medium text-slate-400">
                &nbsp;{{ product.id }}
              </td>
              <td class="p-4 font-medium text-slate-100">{{ product.name }}</td>
              <td class="p-4 text-emerald-400">
                {{ formatPrice(product.price) }}
              </td>
              <td class="p-4">
                <span
                  class="px-3 py-1 text-xs font-semibold rounded-md border"
                  :class="
                    product.stock > 10
                      ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                      : 'bg-rose-500/10 text-rose-400 border-rose-500/20'
                  "
                >
                  {{ product.stock }}
                </span>
              </td>

              <td class="p-4 text-right space-x-2 whitespace-nowrap">
                <RouterLink
                  :to="`/products/${product.id}/edit`"
                  class="inline-block text-blue-400 hover:text-blue-300 font-medium text-sm px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 rounded-md transition"
                >
                  Edit
                </RouterLink>
                <button
                  @click="handleDelete(product.id, product.name)"
                  class="inline-block text-rose-400 hover:text-rose-300 font-medium text-sm px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 rounded-md transition"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        class="bg-slate-900/50 border-t border-slate-700 p-4 flex flex-col sm:flex-row items-center justify-between gap-4"
      >
        <span class="text-sm text-slate-400">
          Menampilkan
          <span class="font-bold text-slate-200">{{ products.length }}</span>
          dari
          <span class="font-bold text-slate-200">{{ pagination.total }}</span>
          produk
        </span>

        <div class="flex space-x-2">
          <button
            @click="fetchProducts(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1 || isLoading"
            class="px-4 py-2 border border-slate-600 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
          >
            Sebelumnya
          </button>
          <button
            @click="fetchProducts(pagination.current_page + 1)"
            :disabled="
              pagination.current_page === pagination.last_page || isLoading
            "
            class="px-4 py-2 border border-slate-600 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
          >
            Selanjutnya
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
