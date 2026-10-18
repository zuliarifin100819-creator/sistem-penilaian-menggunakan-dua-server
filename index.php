<?php
require_once __DIR__ . '/layout.php';

$q = trim($_GET['q'] ?? '');
$sql = 'SELECT m.nim, m.nama, m.prodi, n.nilai_tugas, n.nilai_uts, n.nilai_uas, n.nilai_akhir, n.status
        FROM mahasiswa m LEFT JOIN nilai n ON n.nim = m.nim';
if ($q !== '') {
    $st = db()->prepare($sql . ' WHERE m.nim LIKE ? OR m.nama LIKE ? ORDER BY m.nim');
    $st->execute(["%$q%", "%$q%"]);
} else {
    $st = db()->query($sql . ' ORDER BY m.nim');
}
$rows = $st->fetchAll();

page_header('Data Nilai');
?>
<div class="toolbar">
  <form method="get" class="search">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari NIM / nama...">
    <button>Cari</button>
  </form>
  <span class="info">Nilai akhir = rata-rata tugas, UTS, UAS &bull; Lulus jika &ge; <?= BATAS_LULUS ?></span>
</div>

<table>
  <thead><tr><th>NIM</th><th>Nama</th><th>Prodi</th><th>Tugas</th><th>UTS</th><th>UAS</th><th>Akhir</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php if (!$rows): ?>
    <tr><td colspan="9" class="empty">Belum ada mahasiswa. Tambahkan dulu di Server 1.</td></tr>
  <?php endif; foreach ($rows as $r): ?>
    <tr>
      <td><?= e($r['nim']) ?></td>
      <td><?= e($r['nama']) ?></td>
      <td><?= e($r['prodi']) ?></td>
      <td><?= e($r['nilai_tugas'] ?? '-') ?></td>
      <td><?= e($r['nilai_uts'] ?? '-') ?></td>
      <td><?= e($r['nilai_uas'] ?? '-') ?></td>
      <td><strong><?= e($r['nilai_akhir'] ?? '-') ?></strong></td>
      <td>
        <?php if ($r['status']): ?>
          <span class="badge <?= $r['status'] === 'LULUS' ? 'lulus' : 'gagal' ?>"><?= e($r['status']) ?></span>
        <?php else: ?><span class="badge belum">Belum dinilai</span><?php endif; ?>
      </td>
      <td><a class="btn btn-green" href="nilai.php?nim=<?= urlencode($r['nim']) ?>">Input Nilai</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php page_footer();
