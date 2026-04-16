import { defineStore } from "pinia";
import orderService from "@/services/orderService";

export const useOrderStore = defineStore("order", {
  state: () => ({
    orders: [],
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
      per_page: 10,
    },
    currentOrder: null,
  }),

  actions: {
    async fetchOrders(params) {
      const { data } = await orderService.getOrders(params);
      this.orders = data.data;
      this.pagination = {
        current_page: data.current_page,
        last_page: data.last_page,
        total: data.total,
        per_page: data.per_page,
      };
    },

    async fetchOrderDetail(id) {
      const { data } = await orderService.getDetail(id);
      this.currentOrder = data;
      return data;
    },

    async createOrder(payload) {
      const response = await orderService.createOrder(payload);
      return response.data;
    },
  },
});
