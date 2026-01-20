# SIM-LOG BKPSDM 📦

**Sistem Informasi Manajemen Logistik & Pemeliharaan Aset** berbasis web.  
Project ini dikembangkan sebagai bagian dari **Program Magang di BKPSDM Kabupaten Boyolali**.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## 📖 Tentang Project

**SIM-LOG BKPSDM** adalah transformasi digital untuk pengelolaan sumber daya logistik instansi. Tidak hanya mencatat keluar-masuk barang habis pakai (ATK), sistem ini juga dirancang untuk mencatat riwayat pemeliharaan aset (Servis Kendaraan/Elektronik) secara terpadu.

Sistem ini menerapkan **"Smart Calculation"**, dimana sistem secara cerdas memisahkan perhitungan antara **Nilai Aset Fisik (Stok Gudang)** dan **Beban Operasional (Jasa Servis)** untuk menjaga akurasi laporan keuangan/inventaris.

## ✨ Fitur Unggulan

### 1. Manajemen Logistik (Inventory)
* **Stok Masuk (Restock):** Input belanja barang dari supplier untuk menambah stok gudang.
* **Permintaan Barang:** Bidang/Divisi dapat mengajukan permintaan barang secara digital.
* **Validasi Stok Real-time:** Sistem otomatis menolak permintaan jika stok fisik tidak mencukupi.

### 2. Pencatatan Pemeliharaan (Maintenance) 🛠️
* **Riwayat Servis:** Mencatat pengeluaran jasa (Servis Mobil, Ganti Oli, Service AC, dll).
* **Monitoring KM:** Mencatat Kilometer (KM) kendaraan saat servis untuk pemantauan berkala.
* **Non-Asset Expense:** Biaya servis tercatat sebagai pengeluaran (expense) tanpa mengurangi nilai aset stok gudang.

### 3. Sistem Approval Berjenjang
* **User Bidang:** Mengajukan permintaan -> Status *Pending*.
* **Admin:** Meninjau pengajuan -> Klik *Approve* atau *Reject*.
* **Otomatisasi:** Stok hanya berkurang jika status telah disetujui (Approved) oleh Admin.

### 4. Pelaporan Cerdas (Smart Reporting) 🖨️
* **Laporan Gabungan PDF:** Menyajikan tabel Mutasi Barang dan Tabel Pemeliharaan secara terpisah dalam satu dokumen.
* **Ringkasan Eksekutif:** Menampilkan perhitungan **Total Aset Akhir** (Harta) vs **Total Anggaran Terpakai** (Pengeluaran) secara otomatis.
* **Export Excel:** Rekapitulasi data untuk kebutuhan administrasi lebih lanjut.

## 👥 Hak Akses (Role)

| Role | Deskripsi |
| :--- | :--- |
| **Admin** | Akses penuh (Kelola Master Barang, Input Belanja, Input Pemeliharaan, Acc/Tolak Pengajuan, Cetak Laporan). |
| **Bidang** | Mengajukan permintaan barang, melihat riwayat pengajuan sendiri, dan memantau status persetujuan. |

## 🛠️ Teknologi yang Digunakan

* **Framework:** Laravel 10/11
* **Bahasa:** PHP 8.1+
* **Database:** MySQL
* **Frontend:** Blade Templates, Bootstrap 5 (Mazer Admin Template)
* **Library Tambahan:**
    * `barryvdh/laravel-dompdf` (Cetak PDF Laporan)
    * `maatwebsite/excel` (Export Data Excel)
    * `simple-datatables` (Tabel interaktif & Searchable)

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
    DB_DATABASE=db_atk  <-- Pastikan database sudah dibuat di phpMyAdmin
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generate Key & Migrasi Database**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
    *(Gunakan `--seed` untuk mengisi data dummy User Admin & Bidang)*

5.  **Jalankan Project**
    ```bash
    php artisan serve
    ```
    Buka browser dan akses: `http://localhost:8000`

---

## 👨‍💻 Author

**Andhika Alvin Adhzikra** Mahasiswa Magang - BKPSDM Kabupaten Boyolali  
GitHub: [@ndikkkk](https://github.com/ndikkkk)
