<?php
// ====== KONFIGURASI SERVER 2 ======
// Database ada di LAPTOP SERVER 1 -> isi DB_HOST dengan IP laptop Server 1.
// (Kalau semua dijalankan di 1 laptop, biarkan 'localhost' dan pakai user root.)
const DB_HOST = 'localhost';          // contoh: '192.168.43.10'
const DB_USER = 'root';               // untuk akses dari laptop lain: 'kelompok4'
const DB_PASS = '';                   // untuk akses dari laptop lain: 'kelompok4'
const DB_NAME = 'db_kampus';

// Alamat Server 1 (hanya untuk link menu), mis. 'http://192.168.43.10/server1'
const SERVER1_URL = 'http://localhost:8081';

// Nilai akhir = rata-rata tugas, UTS, UAS (contoh proposal: 80, 75, 85 -> 80)
const BOBOT_TUGAS = 1/3;
const BOBOT_UTS   = 1/3;
const BOBOT_UAS   = 1/3;
// Lulus jika nilai akhir >= BATAS_LULUS
const BATAS_LULUS = 60;

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_TIMEOUT => 5]
        );
    }
    return $pdo;
}
function hitung_nilai_akhir(float $t, float $u, float $a): float {
    return round($t * BOBOT_TUGAS + $u * BOBOT_UTS + $a * BOBOT_UAS, 2);
}
function tentukan_status(float $akhir): string {
    return $akhir >= BATAS_LULUS ? 'LULUS' : 'TIDAK LULUS';
}
