# Solevia Frontend Documentation

Dokumen ini disusun untuk membantu Anda memahami struktur fundamental dari sistem frontend Solevia. Aplikasi ini dibangun menggunakan standar industri e-commerce modern dengan kinerja tinggi.

---

## 1. Konsep Dasar (Fundamental)

Aplikasi frontend Solevia dibangun dengan menggunakan arsitektur **Single Page Application (SPA)** menggunakan framework **Vue 3 (Composition API)** dan bahasa **TypeScript**.

*   **Apa itu SPA?** Dalam website tradisional, setiap kali kamu pindah halaman (misal dari *Home* ke *About*), browser akan mengunduh ulang seluruh halaman HTML dari server (layar menjadi putih sesaat/loading). Pada SPA, file HTML, CSS, dan JS utama hanya diunduh **satu kali** di awal. Saat kamu berpindah halaman, Vue secara pintar hanya mengganti komponen/bagian di layar secara instan tanpa melakukan muat ulang (*reload*). Inilah yang membuat web terasa secepat aplikasi *mobile*.
*   **TypeScript:** JavaScript dengan tipe data (strict). Ini digunakan agar ketika kita memanggil data (misalnya `product.price`), editor (IDE) langsung tahu bahwa `price` adalah angka dan memperingatkan jika ada salah ketik.

---

## 2. Struktur Folder & Fungsinya

Semua kode yang kita tulis untuk tampilan (frontend) berada di dalam folder `frontend/src/`. Berikut adalah rincian fungsionalnya:

*   **`src/assets/`**
    Tempat menaruh file statis (seperti gambar global, logo) dan CSS global. File `main.css` digunakan untuk memuat sistem utilitas Tailwind CSS.
*   **`src/components/`**
    Berisi "potongan-potongan lego" (*Reusable Components*). Ini adalah bagian antarmuka yang akan dipakai berulang kali di berbagai halaman. **Contoh:** `Navbar.vue`, `Footer.vue`, `ProductCard.vue`. Daripada menulis ulang kode kotak produk di halaman *Search*, *Men*, dan *Home*, kita cukup memanggil `<ProductCard />`.
*   **`src/layouts/`**
    Berisi kerangka luar dari halaman.
    *   `DefaultLayout.vue`: Kerangka utama (memiliki Navbar di atas dan Footer di bawah). Hampir semua halaman menggunakan layout ini.
    *   `AuthLayout.vue`: Kerangka khusus untuk halaman Login atau Register (hanya menampilkan isi, tanpa Footer/Navbar yang utuh).
*   **`src/pages/`** (atau Views)
    Ini adalah komponen besar yang bertindak sebagai "Halaman". Setiap file di sini akan memiliki alamat URL (rute) sendiri. **Contoh:** `HomeView.vue`, `SearchView.vue`, `ContactUsView.vue`.
*   **`src/router/`**
    Tempat mengatur sistem *Routing* (Peta Jalan). Di sini (file `index.ts`), kita mendaftarkan bahwa URL `/search` akan membuka halaman `SearchView.vue`. Jika rute tidak terdaftar di sini, maka URL tersebut tidak akan bisa dibuka.
*   **`src/services/`**
    Tempat menaruh fungsi-fungsi yang berhubungan dengan komunikasi pihak ketiga. File `api.ts` di sini berisi **Axios** (kurir HTTP) yang sudah disetel untuk mengarah ke API Backend Laravel.
*   **`src/stores/`**
    Tempat menaruh sistem **Pinia (State Management)**. Bayangkan ini sebagai "Otak Utama" penyimpan memori/data web. Daripada setiap halaman harus mendownload ulang daftar produk yang sama ke server, kita menyimpan datanya secara global di Store. Jika `authStore` mencatat bahwa *user* sudah login, maka seluruh komponen (mulai dari Navbar hingga Checkout) langsung tahu status tersebut tanpa harus saling lempar data.
*   **`src/types/`**
    Berisi definisi/bentuk kerangka data TypeScript. **Contoh:** File ini mendefinisikan bahwa `Product` **wajib** memiliki `id`, `name`, `price`, dan `slug`. Ini mencegah kita mengalami error misterius karena memanggil data yang tidak ada dari backend.

---

## 3. Lokasi Konfigurasi Penting

Jika Anda butuh memodifikasi pengaturan teknis, cari di sini:

1.  **URL Backend (API):** `src/services/api.ts`. Jika kamu mengupload backend ke server *hosting*, ubah `baseURL` (misal: `https://api.solevia.com`) di file ini.
2.  **Menambah Rute/Halaman Baru:** `src/router/index.ts`. Tambahkan *path* URL dan komponen yang ingin ditampilkan.
3.  **Warna & Konfigurasi Desain (Tailwind):** `tailwind.config.js` (berada di luar folder `src/`). Jika butuh warna atau ukuran font baru di luar standar Tailwind, daftarkan di sini.
4.  **Titik Awal Aplikasi:** `src/main.ts` dan `src/App.vue`. Ini adalah fondasi di mana aplikasi mulai dirakit. `main.ts` menyuntikkan Router dan Pinia ke dalam Vue, dan `App.vue` adalah bungkus paling luar untuk menampilkan rute-rute.

---

## 4. Alur Kerja Aplikasi (Workflow)

Mari pelajari siklus dari saat pengunjung mengetik alamat website hingga sepatu tampil:

1.  **Loading:** Browser membaca file `index.html` dan menjalankan `src/main.ts`.
2.  **Pencarian Rute:** Vue Router mendeteksi bahwa URL saat ini adalah `/brands`. Router lalu menyuntikkan `BrandsView.vue` ke bagian layar yang telah disediakan di dalam `DefaultLayout.vue`.
3.  **Meminta Data (OnMounted):** Begitu halaman Brands tampil di memori (siklus `onMounted`), halaman ini "memerintahkan" `productStore` untuk mengambil daftar brand.
4.  **Axios Beraksi:** Store akan menyuruh Axios untuk "mengetuk pintu" API Backend Laravel (`http://localhost:8000/api/brands`) dan menerima balasan berwujud JSON.
5.  **Reaktivitas Tampilan:** Setelah data tiba, Store menyimpannya ke dalam ingatan (State `brands.value`). Karena sifat Vue adalah *Reactive*, `BrandsView.vue` yang sudah *subscribe* (mendengarkan) Store tersebut akan segera menyadari ada perubahan, dan detik itu juga komponen tersebut menggambar kotak-kotak brand di layar komputer pengunjung.
