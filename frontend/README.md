# Mini Order System - Frontend

Repositori ini berisi _source code_ bagian Frontend (Single Page Application) dari proyek **Mini Order System**.

## Fitur Utama & Teknologi

- **Core Framework:** [Vue 3](https://vuejs.org/) (Composition API & `<script setup>`) ditenagai oleh [Vite](https://vitejs.dev/) untuk proses _build_ yang sangat cepat.
- **Styling:** [Tailwind CSS v4](https://tailwindcss.com/) terintegrasi secara _native_ sebagai plugin Vite, memberikan utilitas CSS modern tanpa konfigurasi berat.
- **State Management:** [Pinia](https://pinia.vuejs.org/) untuk mengelola _state_ global secara reaktif (contoh: status Autentikasi dan Data Order).
- **HTTP Client & Interceptors:** [Axios](https://axios-http.com/) dikonfigurasi dengan _Request/Response Interceptors_.
  - Otomatis menyisipkan `Bearer Token` pada setiap _request_.
  - Menangkap _error_ `401 Unauthorized` dan secara otomatis mencoba melakukan _Refresh Token_ tanpa mengganggu pengalaman pengguna.
- **Validasi Form (Client-Side):** [Zod](https://zod.dev/) digunakan untuk memastikan skema data tervalidasi dengan ketat sebelum dikirim ke API, memberikan _feedback_ instan kepada pengguna.
- **Routing & Keamanan:** [Vue Router](https://router.vuejs.org/) dilengkapi dengan _Navigation Guards_ (`meta: { requiresAuth: true }`) untuk mencegah akses ke halaman _dashboard_ tanpa token yang valid.

---

## Panduan Instalasi & Setup

Buka terminal Anda, arahkan ke folder `frontend`, dan jalankan perintah berikut secara berurutan:

```bash
git clone <URL_REPOSITORY_GIT>
cd Mini-order-system/frontend
npm install
npm run dev
```

Silahkan akses halaman website pada link yang dihasilkan oleh perintah 'npm run dev'
