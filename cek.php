<?php
echo "DB_HOST: " . getenv('DB_HOST') . "<br>";
echo "DB_PORT: " . getenv('DB_PORT') . "<br>";
echo "DB_NAME: " . getenv('DB_NAME') . "<br>";
echo "DB_USER: " . getenv('DB_USER') . "<br>";
echo "DB_PASS: " . (getenv('DB_PASS') ? "ada" : "kosong") . "<br>";

// Test koneksi
$dsn = "pgsql:host=".getenv('DB_HOST').";port=".getenv('DB_PORT').";dbname=".getenv('DB_NAME').";sslmode=require";
try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
    echo "✅ Koneksi berhasil!";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>