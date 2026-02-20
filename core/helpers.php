<?php
/**
 * Fungsi untuk format Rupiah
 * Contoh: format_rp(15000) -> Rp 15.000
 */
function format_rp($angka)
{
     return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi untuk membersihkan input agar aman dari hacker (XSS)
 */
function input_bersih($data)
{
     global $conn;
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return mysqli_real_escape_string($conn, $data);
}

/**
 * Fungsi untuk cek status login (Pelanggan)
 * Digunakan di cart, checkout, dll agar tidak bisa diakses tanpa login
 */
function cek_login_pelanggan()
{
     if (!isset($_SESSION['user_id'])) {
          header("Location: ../auth/login.php");
          exit;
     }
}

/**
 * Fungsi untuk upload gambar produk
 */
function upload_gambar($file, $target_dir = "../public/uploads/")
{
     $nama_file = time() . "_" . basename($file["name"]);
     $target_file = $target_dir . $nama_file;
     $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

     // Validasi format
     $valid_extensions = ["jpg", "jpeg", "png", "webp"];
     if (in_array($imageFileType, $valid_extensions)) {
          if (move_uploaded_file($file["tmp_name"], $target_file)) {
               return $nama_file;
          }
     }
     return false;
}

/**
 * Fungsi untuk mengambil status badge (CSS)
 */
function get_status_badge($status)
{
     $status = strtoupper($status);
     if ($status == 'SUCCESS')
          return 'success';
     if ($status == 'PENDING')
          return 'pending';
     return 'danger';
}