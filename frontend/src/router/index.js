import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      redirect: "/dashboard",
    },
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
      meta: { guestOnly: true }, // Hanya untuk yang belum login
    },
    {
      path: "/dashboard",
      name: "dashboard",
      component: () => import("../views/DashboardView.vue"),
      meta: { requiresAuth: true }, // Wajib login
    },
    {
      path: "/products",
      name: "products",
      component: () => import("../views/ProductListView.vue"),
      meta: { requiresAuth: true },
    },
    {
      path: "/products/create",
      name: "product-create",
      component: () => import("../views/ProductFormView.vue"),
      meta: { requiresAuth: true },
    },
    {
      path: "/products/:id/edit",
      name: "product-edit",
      component: () => import("../views/ProductFormView.vue"),
      meta: { requiresAuth: true },
    },

    {
      path: "/orders",
      name: "orders",
      component: () => import("../views/OrderListView.vue"),
      meta: { requiresAuth: true },
    },
    {
      path: "/orders/create",
      name: "order-create",
      component: () => import("../views/OrderCreateView.vue"),
      meta: { requiresAuth: true },
    },
    {
      path: "/orders/:id",
      name: "order-detail",
      component: () => import("../views/OrderDetailView.vue"),
      meta: { requiresAuth: true },
    },
  ],
});

// Navigation Guard: Pengecekan sebelum pindah halaman
// Navigation Guard Baru: Lebih simpel dan tanpa 'next'
router.beforeEach(async (to, from) => {
  const authStore = useAuthStore();
  const isAuthenticated = authStore.isAuthenticated;

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Redirect ke login jika belum auth
    return { name: "login" };
  }

  if (to.meta.guestOnly && isAuthenticated) {
    // Redirect ke dashboard jika sudah auth tapi mau buka login
    return { name: "dashboard" };
  }

  // Jika tidak ada masalah, biarkan perjalanan berlanjut (default return true)
});

export default router;
