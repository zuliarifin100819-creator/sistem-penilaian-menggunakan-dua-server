# Sistem Informasi Data Mahasiswa Berbasis Jaringan (Dua Server, Satu Database)
Proyek Sistem Jaringan Komputer - Kelompok 4

```
CLIENT (browser) --Wi-Fi/LAN--> SERVER 1 (kelola mahasiswa) ----\
                 --Wi-Fi/LAN--> SERVER 2 (olah nilai)  ----------+--> 1 DATABASE: db_kampus
                                                                      (tabel mahasiswa + tabel nilai, di laptop Server 1)
```

| Folder    | Fungsi                                                    | Tabel yang dipakai       |
|-----------|-----------------------------------------------------------|--------------------------|
| `server1` | Tambah / lihat / ubah / hapus data mahasiswa              | `mahasiswa`              |
| `server2` | Baca mahasiswa, input nilai, hitung nilai akhir & status  | `mahasiswa` (baca), `nilai` |

Server 2 terhubung ke database di laptop Server 1 lewat jaringan (MySQL port 3306). Data mahasiswa yang diinput di Server 1 langsung terlihat di Server 2.

## A. Cara menjalankan (2 laptop + 1 client)

1. Hubungkan Laptop 1, Laptop 2, dan client ke **Wi-Fi/hotspot yang sama**.
2. Cek IP tiap laptop: `ipconfig` -> *IPv4 Address*. Contoh: Laptop 1 = `192.168.43.10`, Laptop 2 = `192.168.43.20`.
3. **Laptop 1 (Server 1 + database)**
   - Install XAMPP, start **Apache** dan **MySQL**.
   - Salin folder `server1` ke `C:\xampp\htdocs\server1`.
   - Buka `http://localhost/phpmyadmin` -> tab *Import* -> pilih `db.sql` (di folder utama). Ini membuat database `db_kampus` dan akun `kelompok4` untuk akses dari laptop lain.
   - Edit `server1/config.php`: ubah `SERVER2_URL` menjadi `http://192.168.43.20/server2`.
4. **Laptop 2 (Server 2)**
   - Install XAMPP, start **Apache** (MySQL tidak perlu).
   - Salin folder `server2` ke `C:\xampp\htdocs\server2`.
   - Edit `server2/config.php`:
     ```php
     const DB_HOST = '192.168.43.10';   // IP Laptop 1
     const DB_USER = 'kelompok4';
     const DB_PASS = 'kelompok4';
     const SERVER1_URL = 'http://192.168.43.10/server1';
     ```
5. **Client**: buka browser ->
   - `http://192.168.43.10/server1/`
   - `http://192.168.43.20/server2/`

> Jika gagal terhubung: di Laptop 1 izinkan **port 80 dan 3306** di Windows Firewall; di Laptop 2 izinkan port 80.

## B. Alternatif: demo di 1 laptop saja
Import `db.sql`, lalu dari folder proyek jalankan:
```
php -S 0.0.0.0:8081 -t server1
php -S 0.0.0.0:8082 -t server2
```
Konfigurasi bawaan (`localhost`, user `root`) sudah sesuai. Buka `http://localhost:8081` dan `http://localhost:8082`.

## C. Alur penggunaan
1. Server 1: tambah / ubah / hapus data mahasiswa.
2. Server 2: data mahasiswa otomatis muncul -> klik **Input Nilai** -> isi tugas, UTS, UAS -> nilai akhir & status dihitung otomatis dan disimpan di tabel `nilai`.

Aturan nilai (ubah di `server2/config.php`): nilai akhir = rata-rata tugas, UTS, UAS (contoh 80, 75, 85 -> 80); `LULUS` jika >= `BATAS_LULUS` (default 60).

Jika mahasiswa dihapus di Server 1, nilainya ikut terhapus otomatis (relasi `nim`).

## D. Checklist pengujian
| No | Pengujian                      | Hasil yang diharapkan                              |
|----|--------------------------------|----------------------------------------------------|
| 1  | Client buka Server 1           | Halaman Data Mahasiswa tampil                      |
| 2  | Client buka Server 2           | Halaman Data Nilai tampil                          |
| 3  | Tambah & lihat data (Server 1) | Data tersimpan dan tampil                          |
| 4  | Ubah & hapus data (Server 1)   | Perubahan tersimpan                                |
| 5  | Data muncul di Server 2        | Mahasiswa baru langsung tampil di Server 2         |
| 6  | Input & hitung nilai           | 80, 75, 85 -> nilai akhir 80                       |
| 7  | Penentuan status               | 40, 50, 45 -> TIDAK LULUS                          |
| 8  | Penyimpanan hasil              | Tabel `nilai` di `db_kampus` berisi hasilnya       |

## E. Masalah umum
- **Server 2 error koneksi database** -> IP `DB_HOST` salah, MySQL Laptop 1 belum jalan, `db.sql` belum di-import, atau port 3306 diblokir firewall.
- **Client tidak bisa buka server** -> beda jaringan atau port 80 diblokir firewall.
