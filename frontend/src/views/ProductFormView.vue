<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import productService from "@/services/productService";
import AdminLayout from "@/layouts/AdminLayout.vue";
import Swal from "sweetalert2";
import { z } from "zod";

const router = useRouter();
const route = useRoute();

// Deteksi apakah ini mode EDIT berdasarkan adanya ID di URL
const isEditMode = computed(() => !!route.params.id);
const productId = route.params.id;

const form = reactive({
  name: "",
  price: 0,
  stock: 0,
});

const errors = ref({});
const apiError = ref("");
const isLoading = ref(false);
const isFetching = ref(false);

const productSchema = z.object({
  name: z.string().min(1, "Nama produk wajib diisi"),
  price: z.number().min(0, "Harga tidak boleh minus"),
  stock: z
    .number()
    .min(0, "Stok tidak boleh minus")
    .int("Stok harus bilangan bulat"),
});

// Jika mode edit, ambil data lama
onMounted(async () => {
  if (isEditMode.value) {
    try {
      isFetching.value = true;
      const { data } = await productService.getDetail(productId);
      form.name = data.name;
      form.price = Math.floor(data.price);
      form.stock = data.stock;
    } catch (error) {
      apiError.value = "Gagal memuat data produk";
    } finally {
      isFetching.value = false;
    }
  }
});

const handleSubmit = async () => {
  errors.value = {};
  apiError.value = "";

  const result = productSchema.safeParse(form);
  if (!result.success) {
    result.error.issues.forEach((issue) => {
      errors.value[issue.path[0]] = issue.message;
    });
    return;
  }

  try {
    isLoading.value = true;
    if (isEditMode.value) {
      await productService.updateProduct(productId, form);
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Produk berhasil diperbarui!",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: "#1e293b",
        color: "#f1f5f9",
        customClass: {
          popup: "border border-slate-700 rounded-xl shadow-2xl",
        },
      });
    } else {
      await productService.createProduct(form);
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Produk berhasil ditambahkan!",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: "#1e293b",
        color: "#f1f5f9",
        customClass: {
          popup: "border border-slate-700 rounded-xl shadow-2xl",
        },
      });
    }
    router.push("/products");
  } catch (error) {
    apiError.value =
      error.response?.data?.message || "Terjadi kesalahan saat menyimpan.";
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <AdminLayout>
    <div class="max-w-2xl mx-auto">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-slate-100">
            {{ isEditMode ? "Edit Produk" : "Tambah Produk Baru" }}
          </h2>
        </div>
        <button
          @click="router.push('/products')"
          class="text-slate-400 hover:text-slate-200 text-sm font-medium transition flex items-center"
        >
          &larr; Kembali
        </button>
      </div>

      <div
        class="bg-slate-800 rounded-xl shadow-sm border border-slate-700 p-6"
      >
        <div v-if="isFetching" class="text-blue-400 animate-pulse font-medium">
          Memuat data produk...
        </div>

        <div v-else>
          <div
            v-if="apiError"
            class="bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-lg mb-6 text-sm"
          >
            {{ apiError }}
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5"
                >Nama Produk</label
              >
              <input
                v-model="form.name"
                type="text"
                placeholder="Masukkan nama produk..."
                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
              />
              <p v-if="errors.name" class="text-rose-400 text-xs mt-1.5">
                {{ errors.name }}
              </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5"
                  >Harga (Rp)</label
                >
                <input
                  v-model.number="form.price"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                />
                <p v-if="errors.price" class="text-rose-400 text-xs mt-1.5">
                  {{ errors.price }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5"
                  >Stok</label
                >
                <input
                  v-model.number="form.stock"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                />
                <p v-if="errors.stock" class="text-rose-400 text-xs mt-1.5">
                  {{ errors.stock }}
                </p>
              </div>
            </div>

            <div class="pt-5 mt-6 border-t border-slate-700 flex justify-end">
              <button
                type="submit"
                :disabled="isLoading"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isLoading ? "Menyimpan..." : "Simpan Produk" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
