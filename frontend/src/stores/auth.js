import { defineStore } from "pinia";
import authService from "@/services/authService";
import router from "@/router";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    accessToken: localStorage.getItem("access_token") || null,
    refreshTokenValue: localStorage.getItem("refresh_token") || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.accessToken,
  },

  actions: {
    async login(credentials) {
      const { data } = await authService.login(credentials);
      this.setTokens(data.access_token, data.refresh_token);
      await this.fetchUser();
    },

    async fetchUser() {
      const { data } = await authService.me();
      console.log("Data user :", data);
      this.user = data;
    },

    async logout() {
      try {
        await authService.logout(); // Panggil API logout untuk revoke token di backend
      } catch (e) {
        console.error("Logout error:", e);
      } finally {
        this.forceLogout();
      }
    },

    async refreshToken() {
      if (!this.refreshTokenValue) throw new Error("Tidak ada refresh token");

      const { data } = await authService.refreshToken(this.refreshTokenValue);
      this.setTokens(data.access_token, data.refresh_token);
    },

    setTokens(access, refresh) {
      this.accessToken = access;
      this.refreshTokenValue = refresh;
      localStorage.setItem("access_token", access);
      localStorage.setItem("refresh_token", refresh);
    },

    forceLogout() {
      this.user = null;
      this.accessToken = null;
      this.refreshTokenValue = null;
      localStorage.removeItem("access_token");
      localStorage.removeItem("refresh_token");
      router.push("/login");
    },
  },
});
