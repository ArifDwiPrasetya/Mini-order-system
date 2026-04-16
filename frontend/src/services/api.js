import axios from "axios";
import { useAuthStore } from "@/stores/auth";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// Request Interceptor otomatis menyisipkan Bearer Token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("access_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error),
);

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (
      error.response?.status === 401 &&
      !originalRequest._retry &&
      originalRequest.url !== "/auth/refresh-token"
    ) {
      originalRequest._retry = true;

      try {
        const authStore = useAuthStore();
        await authStore.refreshToken();

        originalRequest.headers.Authorization = `Bearer ${localStorage.getItem("access_token")}`;
        return api(originalRequest);
      } catch (e) {
        const authStore = useAuthStore();
        authStore.forceLogout();
        return Promise.reject(e);
      }
    }

    return Promise.reject(error);
  },
);

export default api;
