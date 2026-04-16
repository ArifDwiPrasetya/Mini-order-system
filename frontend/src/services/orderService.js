import api from "./api";

export default {
  getOrders(params) {
    return api.get("/orders", { params });
  },

  getDetail(id) {
    return api.get(`/orders/${id}`);
  },

  createOrder(payload) {
    return api.post("/orders", payload);
  },
};
