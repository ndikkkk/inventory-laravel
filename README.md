# Sistem Informasi Inventaris Barang (Si-ATK) 📦

Sistem Informasi Manajemen Inventaris Alat Tulis Kantor (ATK) berbasis web.  
Project ini dikembangkan sebagai bagian dari **Program Magang di BKPSDM Kabupaten Boyolali**.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## 📖 Tentang Project

Aplikasi ini bertujuan untuk mendigitalkan proses pencatatan keluar-masuk barang (ATK) yang sebelumnya manual. Sistem ini memfasilitasi permintaan barang dari setiap Bidang/Divisi dengan sistem **Approval (Persetujuan)** oleh Admin, serta menyediakan laporan otomatis dalam bentuk PDF dan Excel.

## ✨ Fitur Utama

### 🔐 Multi-Level User (Role)
* **Admin:** Memiliki akses penuh (Kelola Barang, Input Stok Masuk, Acc/Tolak Pengajuan, Cetak Laporan).
* **User Bidang:** Mengajukan permintaan barang, melihat riwayat pengajuan, dan cek status (Menunggu/Disetujui/Ditolak).

### 📊 Manajemen Barang
* **Dashboard:** Ringkasan jumlah barang, kategori, dan transaksi terbaru.
* **Stok Masuk (Restock):** Input penambahan stok barang dari supplier.
* **Stok Keluar (Pengajuan):** Form permintaan barang dengan validasi stok real-time.

### ✅ Sistem Approval (Persetujuan)
* Bidang mengajukan barang -> Status **Pending (Menunggu)**.
* Admin meninjau pengajuan -> Klik **Approve (Setuju)** atau **Reject (Tolak)**.
* Stok hanya berkurang otomatis jika status **Approved**.

### 🖨️ Laporan & Ekspor
* **Cetak PDF:** Laporan Barang Masuk, Barang Keluar, Sisa Stok, dan Form Pengajuan.
* **Ekspor Excel:** Rekapitulasi data untuk administrasi lebih lanjut.

## 🛠️ Teknologi yang Digunakan

* **Framework:** Laravel 10/11
* **Bahasa:** PHP 8.1+
* **Database:** MySQL
* **Frontend:** Blade Templates, Bootstrap 5 (Mazer Admin Template)
* **Library Tambahan:**
    * `barryvdh/laravel-dompdf` (Cetak PDF)
    * `maatwebsite/excel` (Export Excel)
    * `simple-datatables` (Tabel interaktif)

## 🚀 Cara Instalasi (Localhost)

Ikuti langkah ini untuk menjalankan project di komputer Anda:

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/ndikkkk/inventory-atk.git](https://github.com/ndikkkk/inventory-atk.git)
    cd inventory-atk
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Setup Environment**
    * Duplikat file `.env.example` menjadi `.env`
    * Atur konfigurasi database di file `.env`:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE= db_atk <-- ubah ke database pribadi
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generate Key & Migrasi Database**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
    *(Gunakan `--seed` jika ada data dummy user/admin)*

5.  **Jalankan Project**
    ```bash
    php artisan serve
    ```
    Buka browser dan akses: `http://localhost:8000`

## 📸 Screenshots

*(Tambahkan screenshot aplikasi di sini agar lebih menarik, misalnya halaman Dashboard, Form Pengajuan, dan Laporan PDF)*

---

## 👨‍💻 Author

**Mahasiswa Magang** - BKPSDM Boyolali  
GitHub: [@ndikkkk](https://github.com/ndikkkk)
