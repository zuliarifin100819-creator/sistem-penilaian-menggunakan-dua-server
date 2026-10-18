<?php
require_once __DIR__ . '/config.php';
session_start();

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function flash(string $type, string $msg): void { $_SESSION['flash'] = [$type, $msg]; }

function page_header(string $title): void {
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>' . e($title) . ' - Server 1</title>'
       . '<link rel="stylesheet" href="style.css"></head><body>'
       . '<header><div class="wrap"><h1>Server 1 <small>Data Mahasiswa</small></h1><nav>'
       . '<a href="index.php">Data Mahasiswa</a>'
       . '<a href="form.php">+ Tambah</a>'
       . '<a href="' . e(SERVER2_URL) . '/index.php" target="_blank">Ke Server 2 &rarr;</a>'
       . '</nav></div></header><main class="wrap">';
    if ($f) echo '<div class="alert ' . e($f[0]) . '">' . e($f[1]) . '</div>';
}
function page_footer(): void {
    echo '</main><footer class="wrap">Proyek Sistem Jaringan Komputer - Kelompok 4</footer></body></html>';
}
