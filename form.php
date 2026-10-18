<?php
require_once __DIR__ . '/layout.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$data = ['nim' => '', 'nama' => '', 'prodi' => 'Ilmu Komputer', 'semester' => 1];
$error = '';

if ($id) {
    $st = db()->prepare('SELECT * FROM mahasiswa WHERE id = ?');
    $st->execute([$id]);
    $data = $st->fetch();
    if (!$data) { flash('error', 'Data tidak ditemukan.'); header('Location: index.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nim'      => trim($_POST['nim'] ?? ''),
        'nama'     => trim($_POST['nama'] ?? ''),
        'prodi'    => trim($_POST['prodi'] ?? ''),
        'semester' => (int)($_POST['semester'] ?? 0),
    ];
    if ($data['nim'] === '' || $data['nama'] === '' || $data['prodi'] === '') {
        $error = 'NIM, nama, dan program studi wajib diisi.';
    } elseif (!preg_match('/^[0-9A-Za-z]{1,20}$/', $data['nim'])) {
        $error = 'NIM hanya boleh huruf/angka (maks. 20 karakter).';
    } elseif ($data['semester'] < 1 || $data['semester'] > 14) {
        $error = 'Semester harus antara 1 sampai 14.';
    } else {
        try {
            if ($id) {
                $st = db()->prepare('UPDATE mahasiswa SET nim=?, nama=?, prodi=?, semester=? WHERE id=?');
                $st->execute([$data['nim'], $data['nama'], $data['prodi'], $data['semester'], $id]);
                flash('ok', 'Data mahasiswa berhasil diubah.');
            } else {
                $st = db()->prepare('INSERT INTO mahasiswa (nim, nama, prodi, semester) VALUES (?,?,?,?)');
                $st->execute([$data['nim'], $data['nama'], $data['prodi'], $data['semester']]);
                flash('ok', 'Data mahasiswa berhasil ditambahkan.');
            }
            header('Location: index.php'); exit;
        } catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'NIM sudah terdaftar.' : 'Gagal menyimpan: ' . $ex->getMessage();
        }
    }
}

page_header($id ? 'Ubah Mahasiswa' : 'Tambah Mahasiswa');
?>
<h2><?= $id ? 'Ubah' : 'Tambah' ?> Data Mahasiswa</h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card">
  <input type="hidden" name="id" value="<?= $id ?>">
  <label>NIM <input name="nim" value="<?= e($data['nim']) ?>" maxlength="20" required></label>
  <label>Nama <input name="nama" value="<?= e($data['nama']) ?>" maxlength="100" required></label>
  <label>Program Studi <input name="prodi" value="<?= e($data['prodi']) ?>" maxlength="100" required></label>
  <label>Semester <input type="number" name="semester" min="1" max="14" value="<?= e($data['semester']) ?>" required></label>
  <div><button class="btn-green">Simpan</button> <a class="btn" href="index.php">Batal</a></div>
</form>
<?php page_footer();
