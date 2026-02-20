<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ujikom_kantin_digital";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
     die("
        <div style='font-family: Inter, sans-serif; padding: 20px; background: #ffebee; border-left: 4px solid #ef4444;'>
            <h3 style='color: #ef4444;'>Koneksi Database Gagal!</h3>
            <p>" . mysqli_connect_error() . "</p>
            <p>Pastikan:
                <ul>
                    <li>MySQL/MariaDB sudah running</li>
                    <li>Database 'ujikom_kantin_digital' sudah dibuat</li>
                    <li>Username/password sesuai (default: root/'')</li>
                </ul>
            </p>
        </div>
    ");
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk debug query (opsional)
function debugQuery($query)
{
     global $conn;
     $result = mysqli_query($conn, $query);
     if (!$result) {
          echo "<div style='background: #fee; padding: 10px; margin: 10px 0;'>";
          echo "<strong>Error:</strong> " . mysqli_error($conn) . "<br>";
          echo "<strong>Query:</strong> " . $query;
          echo "</div>";
          return false;
     }
     return $result;
}
?>