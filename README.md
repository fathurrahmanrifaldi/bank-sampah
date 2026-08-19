#  BASARA (Bank Sampah RW 042) — Sistem Informasi Pengelolaan Bank Sampah

Aplikasi sistem informasi berbasis web yang dirancang untuk mendigitalkan dan mengotomatiskan seluruh alur operasional Bank Sampah di RW 042 Kelurahan Bahagia, Kecamatan Babelan, Kabupaten Bekasi. Sistem ini mentransformasi pembukuan konvensional manual menjadi sistem digital yang terintegrasi, transparan, akurat, dan aman.

---

##  Ringkasan Proyek

Pengelolaan bank sampah secara manual sering kali menghadapi risiko kehilangan arsip fisik, pencatatan transaksi yang lambat, serta minimnya transparansi saldo bagi nasabah/warga. **BASARA** hadir sebagai solusi *end-to-end* yang memfasilitasi:
- **Pengelolaan Data Terpusat:** Manajemen data nasabah, pengelola/admin, dan inventaris kategori sampah.
- **Transaksi Cepat & Otomatis:** Pencatatan setoran sampah multi-kategori secara dinamis dengan kalkulasi saldo dan total setoran secara *real-time*.
- **Transparansi Keuangan:** Nasabah dapat memantau riwayat transaksi, mutasi saldo, serta mengajukan penarikan dana tabungan secara mandiri.
- **Sistem Pendukung Keputusan (SPK):** Penentuan nasabah terbaik secara objektif dan terukur menggunakan metode *Simple Additive Weighting* (SAW).
- **Visualisasi & Pelaporan:** Monitoring operasional dan rekapitulasi data keuangan/transaksi dalam bentuk tabel serta grafik interaktif.

---

##  Fitur Utama

###  Super Admin
- Dashboard analitik dan ringkasan eksekutif sistem
- Manajemen akun pengelola/admin (tambah, edit, dan aktivasi status)
- Monitoring transaksi, laporan operasional, dan laporan keuangan komprehensif

###  Admin / Petugas Operasional
- Verifikasi dan persetujuan pendaftaran akun nasabah baru
- Pengelolaan master data nasabah dan kategori/harga sampah
- Input transaksi setoran sampah (*multi-row dynamic form*)
- Pencatatan transaksi penjualan sampah ke mitra pengepul beserta pembaruan stok otomatis
- Pemrosesan dan verifikasi pengajuan penarikan dana nasabah
- Eksekusi kalkulasi penilaian nasabah terbaik berbasis metode SAW

###  Nasabah / Warga
- Dashboard personal: informasi total tabungan saldo dan total akumulasi berat sampah
- Pengajuan penarikan saldo tabungan secara mandiri
- Akses transparan riwayat transaksi setoran dan penarikan dana
- Manajemen data profil akun

---

##  Tech Stack

- **Backend Framework:** PHP (Laravel)
- **Database:** MySQL
- **Frontend / UI:** HTML5, CSS3, JavaScript, Bootstrap
- **Authentication:** Laravel Auth & Google OAuth
- **Data Visualization:** Chart.js
- **Design & Modeling:** Figma, Draw.io (UML & ERD)

---

##  Keamanan & Integritas Data

- **Multi-Role Access Control (RBAC):** Pembatasan hak akses ketat berbasis peran (*Super Admin*, *Admin*, *Nasabah*) via custom middleware.
- **Database Transactions:** Penerapan `DB::transaction()` pada modul transaksional multi-tabel untuk mencegah data korup dan menjamin konsistensi saldo (*data integrity*).
- **Form Validation & Gatekeeper:** Validasi masukan berlapis di sisi klien dan server untuk menghindari data duplikat serta nilai anomali.

---

##  Hasil Pengujian & Implementasi

- **Black-Box Testing:** 45 skenario uji fungsional berhasil diselesaikan dengan tingkat kelulusan **100%**.
- **User Acceptance Testing (UAT):** Diuji langsung bersama pengurus RW dan perwakilan warga dengan tingkat penerimaan **100%** (25 skenario diterima) dan skor kepuasan rata-rata **4.5 / 5.0 (Sangat Baik)**.
