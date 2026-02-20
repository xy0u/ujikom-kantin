<?php
/**
 * Fungsi untuk format Rupiah
 */
function format_rp($angka)
{
     return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi untuk membersihkan input
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
 * Cek login pelanggan
 */
function cek_login_pelanggan()
{
     if (!isset($_SESSION['user_id'])) {
          header("Location: ../auth/login.php");
          exit;
     }
}

/**
 * Upload gambar produk
 */
function upload_gambar($file, $target_dir = "../public/uploads/")
{
     if (!is_dir($target_dir)) {
          mkdir($target_dir, 0777, true);
     }

     $nama_file = time() . "_" . basename($file["name"]);
     $target_file = $target_dir . $nama_file;
     $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

     $valid_extensions = ["jpg", "jpeg", "png", "webp", "gif"];
     if (in_array($imageFileType, $valid_extensions)) {
          if (move_uploaded_file($file["tmp_name"], $target_file)) {
               return $nama_file;
          }
     }
     return false;
}

/**
 * Get status badge class
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

/**
 * Get cart count
 */
function getCartCount()
{
     if (!isset($_SESSION['cart']))
          return 0;
     return array_sum($_SESSION['cart']);
}
?>