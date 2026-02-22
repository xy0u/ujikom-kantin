<?php
// FILE: check_database.php
// Letakkan di folder root: http://localhost/UJIKOM_RIFAT_DWI_PURNAMA_SOPIAN_XII_RPL/check_database.php

echo "<h1>🔍 DATABASE CHECK</h1>";

// Koneksi database
require "core/database.php";

// 1. CEK KONEKSI
echo "<h2>1. Koneksi Database</h2>";
if ($conn) {
     echo "<p style='color:green;'>✅ Koneksi berhasil</p>";
} else {
     echo "<p style='color:red;'>❌ Koneksi gagal</p>";
     exit;
}

// 2. CEK TABEL PRODUCTS
echo "<h2>2. Cek Tabel Products</h2>";
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'products'");
if (mysqli_num_rows($check_table) > 0) {
     echo "<p style='color:green;'>✅ Tabel products ada</p>";
} else {
     echo "<p style='color:red;'>❌ Tabel products TIDAK ADA!</p>";
     echo "<p>Buat tabel dengan SQL:</p>";
     echo "<pre>
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    stock INT NOT NULL,
    category_id INT,
    image VARCHAR(255),
    status VARCHAR(50) DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
    </pre>";
}

// 3. CEK ISI TABEL
echo "<h2>3. Isi Tabel Products</h2>";
$result = mysqli_query($conn, "SELECT * FROM products");
$count = mysqli_num_rows($result);

if ($count > 0) {
     echo "<p style='color:green;'>✅ Ada $count produk</p>";
     echo "<table border='1' cellpadding='10'>";
     echo "<tr><th>ID</th><th>Nama</th><th>Harga</th><th>Stok</th></tr>";
     while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . ($row['name'] ?? '-') . "</td>";
          echo "<td>" . ($row['price'] ?? '-') . "</td>";
          echo "<td>" . ($row['stock'] ?? '-') . "</td>";
          echo "</tr>";
     }
     echo "</table>";
} else {
     echo "<p style='color:red;'>❌ Tabel products KOSONG!</p>";
}

// 4. CEK QUERY DI PUBLIC/INDEX.PHP
echo "<h2>4. Test Query di Public</h2>";
$test_query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
if ($test_query) {
     $test_count = mysqli_num_rows($test_query);
     echo "<p style='color:green;'>✅ Query SELECT * FROM products berhasil</p>";
     echo "<p>Hasil: $test_count produk</p>";
} else {
     echo "<p style='color:red;'>❌ Query gagal: " . mysqli_error($conn) . "</p>";
}

// 5. CEK FOLDER UPLOADS
echo "<h2>5. Cek Folder Uploads</h2>";
$upload_path = "public/uploads/";
if (is_dir($upload_path)) {
     echo "<p style='color:green;'>✅ Folder uploads ada</p>";
     if (is_writable($upload_path)) {
          echo "<p style='color:green;'>✅ Folder uploads bisa ditulis</p>";
     } else {
          echo "<p style='color:red;'>❌ Folder uploads TIDAK bisa ditulis</p>";
     }
} else {
     echo "<p style='color:red;'>❌ Folder uploads TIDAK ADA!</p>";
     echo "<p>Buat folder: mkdir public/uploads</p>";
}

// 6. SARAN PERBAIKAN
echo "<h2>6. Saran Perbaikan</h2>";
if ($count == 0) {
     echo "<p>🔧 Tambahkan produk melalui admin panel atau jalankan SQL:</p>";
     echo "<pre>
INSERT INTO products (name, price, stock, category_id, image, status) VALUES
('Kopi Hitam', 5000, 10, 1, 'kopi.jpg', 'available'),
('Nasi Goreng', 15000, 5, 2, 'nasgor.jpg', 'available'),
('Jus Jeruk', 8000, 8, 1, 'jus.jpg', 'available');
    </pre>";
}
?>

<p><a href="admin/products.php">➡️ Ke Admin Panel</a></p>
<p><a href="public/index.php">➡️ Ke Public Index</a></p>