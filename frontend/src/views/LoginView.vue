<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { z } from "zod";
import Swal from "sweetalert2";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({ email: "", password: "" });
const errors = ref({});
const apiError = ref("");
const isLoading = ref(false);

const loginSchema = z.object({
  email: z
    .string()
    .min(1, "Email wajib diisi")
    .email("Format email tidak valid"),
  password: z.string().min(6, "Password minimal 6 karakter"),
});

const handleLogin = async () => {
  errors.value = {};
  apiError.value = "";

  const result = loginSchema.safeParse(form);
  if (!result.success) {
    result.error.issues.forEach((issue) => {
      errors.value[issue.path[0]] = issue.message;
    });
    return;
  }

  try {
    isLoading.value = true;
    await authStore.login(form);

    router.push("/dashboard");
  } catch (error) {
    apiError.value =
      error.response?.data?.message || "Terjadi kesalahan pada server.";
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-950 flex font-sans overflow-hidden">
    <div
      class="hidden lg:flex lg:w-1/2 relative items-center justify-center bg-linear-to-br from-blue-700 via-blue-800 to-indigo-950 p-12"
    >
      <div class="absolute inset-0 opacity-20">
        <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern
              id="grid"
              width="40"
              height="40"
              patternUnits="userSpaceOnUse"
            >
              <path
                d="M 40 0 L 0 0 0 40"
                fill="none"
                stroke="white"
                stroke-width="1"
              />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
      </div>

      <div class="z-10 text-center max-w-lg">
        <div
          class="inline-flex items-center justify-center w-24 h-24 mb-8 bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl animate-bounce"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-12 h-12 text-blue-300"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <circle cx="8" cy="21" r="1" />
            <circle cx="19" cy="21" r="1" />
            <path
              d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"
            />
          </svg>
        </div>

        <h1
          class="text-5xl font-black text-white tracking-tight mb-4 leading-tight"
        >
          Welcome to <span class="text-blue-300">Mini Order App</span>
        </h1>
        <p class="text-blue-100/80 text-lg leading-relaxed">
          Mini system untuk pengelolaan produk dan pesanan dengan dashboard yang
          mudah digunakan.
        </p>
      </div>

      <div
        class="absolute bottom-10 left-10 w-32 h-32 bg-blue-400/20 rounded-full blur-3xl"
      ></div>
      <div
        class="absolute top-10 right-10 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl"
      ></div>
    </div>

    <div
      class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative bg-slate-950"
    >
      <div
        class="lg:hidden absolute inset-0 z-0 opacity-30 pointer-events-none"
      >
        <div
          class="absolute top-0 left-0 w-full h-full bg-linear-to-b from-blue-600/20 to-transparent"
        ></div>
      </div>

      <div class="z-10 w-full max-w-md">
        <div class="lg:hidden flex flex-col items-center mb-8">
          <div
            class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-8 h-8 text-white"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <circle cx="8" cy="21" r="1" />
              <circle cx="19" cy="21" r="1" />
              <path
                d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"
              />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white text-center">
            Mini Order System
          </h2>
        </div>

        <div
          class="bg-slate-900/40 backdrop-blur-2xl p-8 rounded-3xl border border-slate-800 shadow-2xl"
        >
          <div class="mb-8">
            <h3
              class="text-2xl mb-2 font-bold tracking-wider bg-clip-text text-transparent bg-linear-to-r from-blue-400 to-emerald-400"
            >
              Login to Mini Order System
            </h3>
            <p class="text-slate-400 text-sm">
              Masukkan kredensial Anda untuk akses dashboard.
            </p>
          </div>

          <div
            v-if="apiError"
            class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-center gap-3 animate-head-shake"
          >
            <svg
              class="w-5 h-5 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            {{ apiError }}
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-300 ml-1"
                >Email</label
              >
              <div class="relative group">
                <div
                  class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-500 transition-colors"
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
                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"
                    />
                  </svg>
                </div>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="name@company.com"
                  class="w-full mt-2 pl-11 pr-4 py-3.5 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all duration-200"
                  :class="{ 'border-rose-500/50 bg-rose-500/5': errors.email }"
                />
              </div>
              <p v-if="errors.email" class="text-rose-400 text-xs mt-1 ml-1">
                {{ errors.email }}
              </p>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-300 ml-1"
                >Kata Sandi</label
              >
              <div class="relative group">
                <div
                  class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-500 transition-colors"
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
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                    />
                  </svg>
                </div>
                <input
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  class="w-full mt-2 pl-11 pr-4 py-3.5 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition-all duration-200"
                  :class="{
                    'border-rose-500/50 bg-rose-500/5': errors.password,
                  }"
                />
              </div>
              <p v-if="errors.password" class="text-rose-400 text-xs mt-1 ml-1">
                {{ errors.password }}
              </p>
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-full bg-blue-600 hover:bg-blue-500 active:scale-[0.98] text-white font-bold py-4 px-4 rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3 group"
            >
              <template v-if="isLoading">
                <svg
                  class="animate-spin h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                <span>Memproses...</span>
              </template>
              <template v-else>
                <span>Masuk ke Dashboard</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  stroke-width="2.5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                  />
                </svg>
              </template>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes head-shake {
  0% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-4px);
  }
  50% {
    transform: translateX(4px);
  }
  75% {
    transform: translateX(-4px);
  }
  100% {
    transform: translateX(0);
  }
}

.animate-head-shake {
  animation: head-shake 0.4s ease-in-out;
}
</style>
