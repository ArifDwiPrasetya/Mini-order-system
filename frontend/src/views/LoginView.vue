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

    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: "Selamat Datang !!!",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: "#1e293b",
      color: "#f1f5f9",
      customClass: {
        popup: "border border-slate-700 rounded-xl shadow-2xl",
      },
    });

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
  <div
    class="min-h-screen bg-slate-950 flex items-center justify-center p-4 relative overflow-hidden font-sans"
  >
    <div class="absolute inset-0 z-0 opacity-10">
      <div
        class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-linear-to-br from-blue-600 to-purple-800 blur-3xl"
      ></div>
      <div
        class="absolute top-1/2 left-2/3 w-80 h-80 rounded-full bg-linear-to-tr from-emerald-600 to-slate-900 blur-2xl"
      ></div>

      <svg
        class="absolute inset-0 w-full h-full"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <pattern
            id="pattern-circles"
            x="0"
            y="0"
            width="40"
            height="40"
            patternUnits="userSpaceOnUse"
          >
            <circle cx="2" cy="2" r="1" fill="#334155" />
          </pattern>
        </defs>
        <rect width="100%" h="100%" fill="url(#pattern-circles)" />
      </svg>
    </div>

    <div
      class="z-10 w-full max-w-md bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700/50 overflow-hidden transform transition-all duration-300 hover:shadow-blue-900/20 hover:border-slate-600/50"
    >
      <div class="bg-slate-900/60 p-6 text-center border-b border-slate-700/50">
        <h2
          class="text-3xl font-extrabold tracking-tighter bg-clip-text text-transparent bg-linear-to-r from-blue-400 to-emerald-400"
        >
          Mini Order System
        </h2>
        <p class="text-slate-400 mt-2 text-sm font-medium">Silahkan Login</p>
      </div>

      <div class="p-8">
        <div
          v-if="apiError"
          class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-2"
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
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            ></path>
          </svg>
          {{ apiError }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5"
              >Email</label
            >
            <div class="relative">
              <input
                v-model="form.email"
                type="email"
                placeholder="admin@order.com"
                class="w-full pl-11 pr-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-150"
                :class="{ 'border-rose-500 focus:ring-rose-500': errors.email }"
              />
              <svg
                class="w-5 h-5 text-slate-600 absolute left-3.5 top-3"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"
                ></path>
              </svg>
            </div>
            <p v-if="errors.email" class="text-rose-400 text-xs mt-1.5 ml-1">
              {{ errors.email }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5"
              >Password</label
            >
            <div class="relative">
              <input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                class="w-full pl-11 pr-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-150"
                :class="{
                  'border-rose-500 focus:ring-rose-500': errors.password,
                }"
              />
              <svg
                class="w-5 h-5 text-slate-600 absolute left-3.5 top-3"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                ></path>
              </svg>
            </div>
            <p v-if="errors.password" class="text-rose-400 text-xs mt-1.5 ml-1">
              {{ errors.password }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="w-full mt-2 bg-linear-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-semibold py-3 px-4 rounded-lg shadow-lg shadow-blue-900/30 transition-all duration-150 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg
              v-if="isLoading"
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
            <span v-if="isLoading">Mengautentikasi...</span>
            <span v-else>Login</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
