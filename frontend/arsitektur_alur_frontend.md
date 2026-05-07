# Arsitektur Frontend Solevia - Panduan & Alur Kerja

Dokumen ini memberikan penjelasan mendetail mengenai arsitektur *frontend* e-commerce Solevia yang dibangun menggunakan **Vue 3 (Composition API)**, **Pinia** (State Management), **Vue Router**, **Axios**, dan **Tailwind CSS**.

---

## 1. Penjelasan & Keterkaitan Antar Folder (`src/`)

Setiap folder di dalam aplikasi ini dibuat modular (terpisah) berdasarkan tanggung jawabnya masing-masing.

*   **`/assets`**: Menyimpan aset statis seperti CSS global (`main.css` untuk inisialisasi Tailwind).
*   **`/components`**: Menyimpan elemen-elemen UI kecil yang bisa digunakan berulang-ulang di berbagai halaman. Contoh: `Navbar.vue`, `Footer.vue`, `ProductCard.vue`. 
    *   *Keterkaitan:* Diimpor dan dirangkai oleh file di folder `/layouts` atau `/pages`.
*   **`/layouts`**: Menyimpan kerangka (cangkang) halaman. Contoh: `DefaultLayout.vue` memiliki Navbar di atas dan Footer di bawah, lalu ada `<RouterView>` di tengahnya.
    *   *Keterkaitan:* Menjadi pembungkus untuk seluruh komponen di folder `/pages` melalui instruksi di `router/index.ts`.
*   **`/pages`**: Menyimpan keseluruhan tampilan untuk spesifik satu URL. Contoh: Halaman *Home*, *Cart*, *Profile*, dll. Halaman ini adalah kumpulan dari berbagai *components*.
*   **`/router`**: Mengatur lalu lintas dan pemetaan URL. Mengatakan kepada aplikasi: "Jika URL-nya `/cart`, tolong tampilkan file `CartView.vue` di dalam `DefaultLayout.vue`".
*   **`/services`**: Tempat komunikasi dengan dunia luar (Backend). Berisi konfigurasi inti, misalnya `api.ts` yang mengatur aturan pengiriman data menggunakan *Axios*.
*   **`/stores`**: Menggunakan Pinia sebagai *Memory Card* global. Di sini data disimpan sementara agar tidak hilang dan bisa dipakai oleh banyak halaman secara bersamaan tanpa harus meminta ulang ke *Backend*.
*   **`/types`**: File *TypeScript Interfaces* (`index.ts`). Ini adalah "buku kamus" yang mendaftar format pasti untuk data (seperti Produk harus punya ID, Nama, Harga). Menjaga agar kita tidak *typo* (salah ketik) saat *coding*.

---

## 2. Alur Navigasi Web (Bagaimana SPA Bekerja)

Aplikasi Solevia adalah SPA (*Single Page Application*). Artinya, saat *user* berpindah halaman, browser tidak benar-benar me-reload halaman web dari awal.

1.  **Entry Point**: Semuanya dimulai dari `main.ts`. File ini merakit alat-alat (Pinia, Router) dan memasangnya ke `App.vue`.
2.  **Akar (Root)**: `App.vue` hanya berisi satu elemen penting, yaitu `<RouterView />` (sebuah lubang portal dinamis).
3.  **Router Beraksi**: Jika kamu mengetik `http://localhost:5173/profile`, `router/index.ts` akan membaca `/profile`, kemudian menyuntikkan `ProfileView.vue` ke dalam lubang `<RouterView />`. Halaman langsung berubah dalam sekejap tanpa *loading/blank* putih.

---

## 3. Alur Komunikasi Data (Frontend ⇄ API Backend)

Ini adalah alur paling penting. Bagaimana data diambil dari *database* Laravel hingga muncul di layar komputer pelanggan.

Mari kita ambil contoh **Halaman Cart (Keranjang Belanja)**:

> **Urutan Kejadian (Step-by-Step Cycle):**

### Langkah 1: Memicu Pemintaan (Di dalam `CartView.vue`)
Saat pengguna mengklik halaman keranjang, file `CartView.vue` muncul di layar. Di dalam kodenya, fungsi bawaan Vue yaitu `onMounted()` mendeteksi bahwa halaman baru saja dibuka.
Halaman lalu berteriak: *"Hai Pinia (`cartStore`), tolong ambilkan data keranjangnya!"* dengan memanggil `cartStore.fetchCart()`.

### Langkah 2: Pinia Memproses (`stores/cart.ts`)
Store Pinia menerima perintah tersebut. Ia mulai bekerja:
*   Pinia mengubah variabel `loading.value = true` (yang otomatis membuat animasi berputar/ *spinner* muncul di layar pengguna).
*   Pinia memanggil *Axios* lewat perintah `await api.get('/cart')`.

### Langkah 3: Agen Pengirim / Axios Interceptors (`services/api.ts`)
Sebelum permintaan itu dikirimkan ke *Backend*, file `api.ts` bertindak sebagai agen keamanan di perbatasan.
*   **Interceptor** akan memeriksa kantong memori browser (LocalStorage).
*   *"Apakah pengguna ini punya Token rahasia (Login)?"*
*   Jika ada, ia akan menempelkan token itu di amplop permintaan (`Authorization: Bearer <token>`).
*   Kemudian Axios mengirim paket ini ke *Backend* di `http://localhost:8000/api/cart`.

### Langkah 4: Backend (Laravel) Merespon
Backend Laravel memverifikasi Token tersebut, membaca isi *database*, dan mengirimkan kumpulan data berformat JSON kembali ke *Frontend*.

### Langkah 5: Pinia Menyimpan Data (`stores/cart.ts`)
Axios menerima jawaban dari *Backend* dan mengopernya kembali ke Pinia.
*   Pinia mengambil data JSON tersebut dan menyimpannya ke dalam memori global `cart.value = data`.
*   Pinia memberi sinyal bahwa pekerjaan selesai dengan `loading.value = false`.

### Langkah 6: Reaktivitas Otomatis Vue (`CartView.vue` & HTML)
Karena arsitektur Vue bersifat **Reaktif**, `CartView.vue` yang sejak tadi "mengawasi" variabel `cartStore.cart` dan `cartStore.loading` akan menyadari adanya perubahan secara otomatis.
*   Karena `loading` menjadi `false`, animasi *spinner* otomatis hilang dari layar.
*   Karena `cartStore.cart` sekarang sudah berisi data keranjang, Vue langsung menggambar (Me-render) foto sepatu, nama, dan harganya ke layar HTML pengguna sepersekian milidetik kemudian.

> Semua proses ini, dari Langkah 1 hingga Langkah 6, biasanya terjadi hanya dalam waktu **0.1 - 0.5 detik** tanpa ada satu pun halaman web yang di-*reload*. Itulah kehebatan kombinasi Vue + Pinia + Axios!
