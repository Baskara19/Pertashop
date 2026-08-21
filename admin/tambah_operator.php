<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO users (username,password,nama_lengkap,role) VALUES (?,?,?,'operator')");
    if ($stmt->execute([$_POST['username'],password_hash($_POST['password'],PASSWORD_DEFAULT),$_POST['nama']])) {
        header("Location: kelola_operator.php"); exit;
    } else { echo "Error: gagal menyimpan."; }
}
?>
<!DOCTYPE html><html><head><title>Tambah Operator</title></head><body>
<h2>Tambah Operator Baru</h2>
<form method="POST">
    <label>Username</label><br><input type="text" name="username" required><br><br>
    <label>Nama Lengkap</label><br><input type="text" name="nama" required><br><br>
    <label>Password</label><br><input type="password" name="password" required><br><br>
    <button type="submit">💾 Simpan</button>
</form></body></html>
