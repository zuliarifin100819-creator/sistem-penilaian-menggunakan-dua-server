<?php
require_once __DIR__ . '/layout.php';

$nim = $_GET['nim'] ?? $_POST['nim'] ?? '';
$st = db()->prepare('SELECT m.nim, m.nama, m.prodi, n.nilai_tugas, n.nilai_uts, n.nilai_uas
                     FROM mahasiswa m LEFT JOIN nilai n ON n.nim = m.nim WHERE m.nim = ?');
$st->execute([$nim]);
$m = $st->fetch();
if (!$m) { flash('error', 'Mahasiswa tidak ditemukan.'); header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = [];
    foreach (['nilai_tugas', 'nilai_uts', 'nilai_uas'] as $f) {
        $v = $_POST[$f] ?? '';
        if ($v === '' || !is_numeric($v) || $v < 0 || $v > 100) { $error = 'Semua nilai harus berupa angka 0 - 100.'; break; }
        $in[$f] = (float)$v;
    }
    if (!$error) {
        $akhir  = hitung_nilai_akhir($in['nilai_tugas'], $in['nilai_uts'], $in['nilai_uas']);
        $status = tentukan_status($akhir);
        db()->prepare(
            'INSERT INTO nilai (nim, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, status) VALUES (?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE nilai_tugas=VALUES(nilai_tugas), nilai_uts=VALUES(nilai_uts),
               nilai_uas=VALUES(nilai_uas), nilai_akhir=VALUES(nilai_akhir), status=VALUES(status)'
        )->execute([$nim, $in['nilai_tugas'], $in['nilai_uts'], $in['nilai_uas'], $akhir, $status]);
        flash('ok', "Nilai {$m['nama']} tersimpan. Nilai akhir: $akhir ($status)");
        header('Location: index.php'); exit;
    }
    $m = array_merge($m, $in);
}

page_header('Input Nilai');
?>
<h2>Input Nilai</h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card">
  <input type="hidden" name="nim" value="<?= e($m['nim']) ?>">
  <div><strong><?= e($m['nama']) ?></strong><br><small>NIM <?= e($m['nim']) ?> &bull; <?= e($m['prodi']) ?></small></div>
  <label>Nilai Tugas <input type="number" step="0.01" min="0" max="100" name="nilai_tugas" value="<?= e($m['nilai_tugas']) ?>" required></label>
  <label>Nilai UTS   <input type="number" step="0.01" min="0" max="100" name="nilai_uts"   value="<?= e($m['nilai_uts']) ?>"   required></label>
  <label>Nilai UAS   <input type="number" step="0.01" min="0" max="100" name="nilai_uas"   value="<?= e($m['nilai_uas']) ?>"   required></label>
  <div><button class="btn-green">Hitung &amp; Simpan</button> <a class="btn" href="index.php">Batal</a></div>
</form>
<?php page_footer();
