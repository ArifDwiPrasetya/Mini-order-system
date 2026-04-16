import api from "./api";
import axios from "axios"; // Import axios bawaan untuk bypass interceptor

export default {
  login(credentials) {
    return api.post("/auth/login", credentials);
  },

  logout() {
    return api.post("/auth/logout");
  },

  me() {
    return api.get("/auth/me");
  },

  refreshToken(refreshTokenValue) {
    // Kita gunakan axios murni (bukan instance 'api') agar tidak terkena response interceptor yang bisa menyebabkan infinite loop jika refresh tokennya juga sudah tidak valid.
    return axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/auth/refresh-token`,
      {
        refresh_token: refreshTokenValue,
      },
      {
        headers: { Accept: "application/json" },
      },
    );
  },
};
