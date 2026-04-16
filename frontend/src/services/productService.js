import api from "./api";

export default {
  getProducts(params) {
    return api.get("/products", { params });
  },
};
