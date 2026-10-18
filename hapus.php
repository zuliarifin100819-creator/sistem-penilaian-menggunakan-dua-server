<?php
require_once __DIR__ . '/layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($id = (int)($_POST['id'] ?? 0))) {
    $st = db()->prepare('DELETE FROM mahasiswa WHERE id = ?');
    $st->execute([$id]);
    flash('ok', 'Data mahasiswa dihapus.');
}
header('Location: index.php');
