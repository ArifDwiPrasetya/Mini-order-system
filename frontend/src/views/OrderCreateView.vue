<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useOrderStore } from "@/stores/order";
import productService from "@/services/productService";
import AdminLayout from "@/layouts/AdminLayout.vue";
import { z } from "zod";
import Swal from "sweetalert2";

const router = useRouter();
const orderStore = useOrderStore();
const availableProducts = ref([]);
const isFetchingProducts = ref(true);

const form = reactive({ items: [{ product_id: "", qty: 1 }] });
const errors = ref({});
const apiError = ref("");
const isSubmitting = ref(false);

onMounted(async () => {
  try {
    const { data } = await productService.getProducts({ per_page: 100 });
    availableProducts.value = data.data;
    console.log(availableProducts.value);
  } catch (err) {
    apiError.value = "Gagal memuat daftar produk.";
  } finally {
    isFetchingProducts.value = false;
  }
});

const addItem = () => form.items.push({ product_id: "", qty: 1 });
const removeItem = (index) => {
  if (form.items.length > 1) form.items.splice(index, 1);
};

const orderSchema = z.object({
  items: z
    .array(
      z.object({
        product_id: z
          .union([z.number(), z.string()])
          .refine((val) => val !== "", { message: "Produk wajib dipilih" }),
        qty: z.number().min(1, "Minimal 1").int("Bulat"),
      }),
    )
    .min(1, "Minimal 1 item"),
});

const handleSubmit = async () => {
  errors.value = {};
  apiError.value = "";
  const payloadToValidate = {
    items: form.items.map((item) => ({
      product_id: item.product_id ? Number(item.product_id) : "",
      qty: Number(item.qty),
    })),
  };

  const result = orderSchema.safeParse(payloadToValidate);
  if (!result.success) {
    result.error.issues.forEach((issue) => {
      errors.value[issue.path.join(".")] = issue.message;
    });
    return;
  }

  try {
    isSubmitting.value = true;
    await orderStore.createOrder(payloadToValidate);
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: "Order berhasil dibuat!",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: "#1e293b",
      color: "#f1f5f9",
      customClass: {
        popup: "border border-slate-700 rounded-xl shadow-2xl",
      },
    });
    router.push("/orders");
  } catch (error) {
    apiError.value = error.response?.data?.message || "Terjadi kesalahan.";
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <AdminLayout>
    <div class="max-w-3xl mx-auto">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-slate-100">Buat Order Baru</h2>
          <p class="text-slate-400 mt-1 text-sm">
            Pilih produk dan tentukan kuantitas.
          </p>
        </div>
        <button
          @click="$router.push('/dashboard')"
          class="text-slate-400 hover:text-blue-400 text-sm font-medium transition"
        >
          &larr; Kembali
        </button>
      </div>

      <div
        class="bg-slate-800 rounded-xl border border-slate-700 p-6 shadow-lg"
      >
        <div
          v-if="apiError"
          class="bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-lg mb-6 text-sm"
        >
          {{ apiError }}
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="space-y-4">
            <div
              v-for="(item, index) in form.items"
              :key="index"
              class="flex flex-col sm:flex-row gap-4 p-5 border border-slate-700 bg-slate-800/50 rounded-xl relative group"
            >
              <div class="flex-1">
                <label class="block text-sm font-medium text-slate-300 mb-1.5"
                  >Produk</label
                >
                <select
                  v-model="item.product_id"
                  class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 outline-none transition disabled:opacity-50"
                  :disabled="isFetchingProducts"
                >
                  <option value="" disabled>-- Pilih Produk --</option>
                  <option
                    v-for="prod in availableProducts"
                    :key="prod.id"
                    :value="prod.id"
                    :disabled="prod.stock < 1"
                  >
                    {{ prod.name }} (Stok: {{ prod.stock }} | Rp.{{
                      prod.price
                    }})
                  </option>
                </select>
                <p
                  v-if="errors[`items.${index}.product_id`]"
                  class="text-rose-400 text-xs mt-1.5"
                >
                  {{ errors[`items.${index}.product_id`] }}
                </p>
              </div>

              <div class="w-full sm:w-32">
                <label class="block text-sm font-medium text-slate-300 mb-1.5"
                  >Kuantitas</label
                >
                <input
                  v-model.number="item.qty"
                  type="number"
                  min="1"
                  class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:ring-2 focus:ring-blue-500 outline-none transition"
                />
                <p
                  v-if="errors[`items.${index}.qty`]"
                  class="text-rose-400 text-xs mt-1.5"
                >
                  {{ errors[`items.${index}.qty`] }}
                </p>
              </div>

              <div class="flex items-end justify-end sm:pb-1">
                <button
                  v-if="form.items.length > 1"
                  type="button"
                  @click="removeItem(index)"
                  class="p-2.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition"
                  title="Hapus baris"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    ></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <div
            class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-slate-700 pt-6"
          >
            <button
              type="button"
              @click="addItem"
              class="text-sm font-medium text-blue-400 hover:text-blue-300 flex items-center transition"
            >
              <span class="mr-1.5 text-lg leading-none">+</span> Tambah Produk
              Lain
            </button>

            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full sm:w-auto bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-6 rounded-lg shadow-lg shadow-blue-900/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isSubmitting">Memproses...</span
              ><span v-else>Konfirmasi Order</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
