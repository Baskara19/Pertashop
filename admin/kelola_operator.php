<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';

if (isset($_POST['tambah'])) {
    $stmt = $pdo->prepare("INSERT INTO users (username,password,nama_lengkap,role) VALUES (?,?,?,'operator')");
    $stmt->execute([$_POST['username'],password_hash($_POST['password'],PASSWORD_DEFAULT),$_POST['nama']]);
    header("Location: kelola_operator.php"); exit;
}
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    if (!empty($_POST['password'])) {
        $stmt = $pdo->prepare("UPDATE users SET username=?,nama_lengkap=?,password=? WHERE id=?");
        $stmt->execute([$_POST['username'],$_POST['nama'],password_hash($_POST['password'],PASSWORD_DEFAULT),$id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?,nama_lengkap=? WHERE id=?");
        $stmt->execute([$_POST['username'],$_POST['nama'],$id]);
    }
    header("Location: kelola_operator.php"); exit;
}
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='operator'");
    $stmt->execute([(int)$_GET['hapus']]);
    header("Location: kelola_operator.php"); exit;
}
$operators = $pdo->query("SELECT * FROM users WHERE role='operator' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html><html><head><title>Kelola Operator</title><style>
body{font-family:'Segoe UI',sans-serif;padding:30px;background:#f9f9f9}
h2{text-align:center;color:#2c3e50;margin-bottom:30px}
.card{background:white;border-radius:12px;padding:25px;box-shadow:0 4px 12px rgba(0,0,0,0.1);margin-bottom:30px}
.card h3{margin-top:0;color:#27ae60}
form input,form button{padding:10px;margin:5px;border-radius:6px;border:1px solid #ccc;font-size:14px}
form button{background:#27ae60;color:white;border:none;cursor:pointer}
table{border-collapse:collapse;width:100%;margin-top:15px}
th,td{border:1px solid #ddd;padding:12px;text-align:center;font-size:14px}
th{background:#27ae60;color:white}
.btn-delete{padding:6px 12px;background:#e74c3c;color:white;border-radius:5px;text-decoration:none}
.btn-dashboard{display:inline-block;margin-top:20px;padding:12px 20px;background:#2980b9;color:white;text-decoration:none;border-radius:8px;font-weight:bold}
.edit-form{display:flex;gap:8px;justify-content:center;flex-wrap:wrap}
.edit-form button{background:#f39c12}
</style></head><body>
<h2>👥 Kelola Operator</h2>
<div class="card"><h3>➕ Tambah Operator Baru</h3>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="nama" placeholder="Nama Lengkap" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="tambah">Tambah</button>
</form></div>
<div class="card"><h3>📋 Daftar Operator</h3>
<table><tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Dibuat</th><th>Aksi</th></tr>
<?php foreach ($operators as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
    <td><?= $row['created_at'] ?></td>
    <td>
        <form method="POST" class="edit-form">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
            <input type="text" name="nama" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required>
            <input type="password" name="password" placeholder="Password baru (opsional)">
            <button type="submit" name="edit">✏️ Simpan</button>
        </form>
        <a class="btn-delete" href="kelola_operator.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<div style="text-align:center;"><a href="dashboard.php" class="btn-dashboard">⬅️ Kembali ke Dashboard</a></div>
</body></html>
