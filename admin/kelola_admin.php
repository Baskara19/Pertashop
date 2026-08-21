<?php
require __DIR__ . '/../auth_admin.php';
require __DIR__ . '/../db.php';
if ($_SESSION['role'] !== 'admin') { header("Location: ../operator/dashboard_operator.php"); exit; }

if (isset($_POST['tambah'])) {
    $stmt = $pdo->prepare("INSERT INTO users (username,password,nama_lengkap,role) VALUES (?,?,?,'admin')");
    $stmt->execute([$_POST['username'], password_hash($_POST['password'],PASSWORD_DEFAULT), $_POST['nama']]);
    header("Location: kelola_admin.php"); exit;
}
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    if (!empty($_POST['password'])) {
        $stmt = $pdo->prepare("UPDATE users SET username=?,nama_lengkap=?,password=? WHERE id=? AND role='admin'");
        $stmt->execute([$_POST['username'],$_POST['nama'],password_hash($_POST['password'],PASSWORD_DEFAULT),$id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?,nama_lengkap=? WHERE id=? AND role='admin'");
        $stmt->execute([$_POST['username'],$_POST['nama'],$id]);
    }
    header("Location: kelola_admin.php"); exit;
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id == $_SESSION['user_id']) { echo "<script>alert('❌ Tidak bisa hapus akun sendiri!');window.location='kelola_admin.php';</script>"; exit; }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='admin'"); $stmt->execute([$id]);
    header("Location: kelola_admin.php"); exit;
}
$admins = $pdo->query("SELECT * FROM users WHERE role='admin' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html><html><head><title>Kelola Admin</title><style>
body{font-family:Arial,sans-serif;padding:20px;background:#f8f9fa}
.container{background:white;padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1)}
h2{text-align:center;color:#007bff;margin-bottom:20px}h3{color:#444;margin-top:20px}
form{margin-top:15px;display:flex;flex-wrap:wrap;gap:10px}
input,button{padding:10px;border-radius:6px;border:1px solid #ccc}
input{flex:1;min-width:180px}button{background:#007bff;color:white;font-weight:bold;cursor:pointer;border:none}
table{border-collapse:collapse;width:100%;margin-top:20px}
th,td{border:1px solid #ddd;padding:10px;text-align:center}th{background:#007bff;color:white}
.edit-btn,.delete-btn{display:inline-block;padding:6px 12px;border-radius:6px;color:white;text-decoration:none;margin:2px}
.edit-btn{background:#17a2b8}.delete-btn{background:#dc3545}
.back-btn{display:inline-block;margin-top:20px;padding:10px 20px;background:#6c757d;color:white;border-radius:6px;text-decoration:none;font-weight:bold}
.badge{background:#28a745;color:white;padding:3px 7px;border-radius:5px;font-size:12px}
</style></head><body>
<div class="container">
<h2>👨‍💼 Kelola Admin</h2>
<h3>Tambah Admin Baru</h3>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="nama" placeholder="Nama Lengkap" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="tambah">➕ Tambah</button>
</form>
<h3>Daftar Admin</h3>
<table><tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Dibuat</th><th>Aksi</th></tr>
<?php foreach ($admins as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['username']) ?><?php if ($row['id']==$_SESSION['user_id']): ?> <span class="badge">Anda</span><?php endif; ?></td>
    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
    <td><?= $row['created_at'] ?></td>
    <td>
        <form method="POST" style="display:inline-block;">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
            <input type="text" name="nama" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ganti">
            <button type="submit" name="edit" class="edit-btn">✏️ Edit</button>
        </form>
        <?php if ($row['id'] != $_SESSION['user_id']): ?>
        <a class="delete-btn" href="kelola_admin.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<a href="dashboard.php" class="back-btn">⬅️ Kembali ke Dashboard</a>
</div></body></html>
