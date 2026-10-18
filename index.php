<?php
require_once __DIR__ . '/layout.php';

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $st = db()->prepare('SELECT * FROM mahasiswa WHERE nim LIKE ? OR nama LIKE ? ORDER BY nim');
    $st->execute(["%$q%", "%$q%"]);
} else {
    $st = db()->query('SELECT * FROM mahasiswa ORDER BY nim');
}
$rows = $st->fetchAll();

page_header('Data Mahasiswa');
?>
<div class="toolbar">
  <form method="get" class="search">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari NIM / nama...">
    <button>Cari</button>
  </form>
</div>

<table>
  <thead><tr><th>No</th><th>NIM</th><th>Nama</th><th>Program Studi</th><th>Semester</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php if (!$rows): ?>
    <tr><td colspan="6" class="empty">Belum ada data.</td></tr>
  <?php endif; foreach ($rows as $i => $r): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= e($r['nim']) ?></td>
      <td><?= e($r['nama']) ?></td>
      <td><?= e($r['prodi']) ?></td>
      <td><?= e($r['semester']) ?></td>
      <td class="aksi">
        <a class="btn" href="form.php?id=<?= (int)$r['id'] ?>">Ubah</a>
        <form method="post" action="hapus.php" onsubmit="return confirm('Hapus data ini?')"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-red">Hapus</button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php page_footer();
