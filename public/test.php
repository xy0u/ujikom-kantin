<?php
// File untuk test koneksi dan environment
echo "<h1>Kantin Digital - System Check</h1>";

// PHP Version
echo "<h2>PHP Version: " . phpversion() . "</h2>";

// Database Connection
require "core/database.php";
echo "<h2>Database: " . ($conn ? "✅ Connected" : "❌ Failed") . "</h2>";

// Check Tables
$tables = ['users', 'categories', 'products', 'orders', 'order_items'];
echo "<h2>Tables:</h2><ul>";
foreach ($tables as $table) {
     $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
     $status = mysqli_num_rows($result) > 0 ? "✅" : "❌";
     echo "<li>$status $table</li>";
}
echo "</ul>";

// Session
session_start();
echo "<h2>Session: " . (session_status() == PHP_SESSION_ACTIVE ? "✅ Active" : "❌ Inactive") . "</h2>";

// Folders
$folders = ['public/uploads', 'public/assets/img', 'sessions'];
echo "<h2>Folders:</h2><ul>";
foreach ($folders as $folder) {
     $status = is_writable($folder) ? "✅ Writable" : "❌ Not writable";
     echo "<li>$folder: $status</li>";
}
echo "</ul>";

// PHP Extensions
$extensions = ['mysqli', 'session', 'json', 'fileinfo', 'gd'];
echo "<h2>Extensions:</h2><ul>";
foreach ($extensions as $ext) {
     $status = extension_loaded($ext) ? "✅" : "❌";
     echo "<li>$status $ext</li>";
}
echo "</ul>";
?>