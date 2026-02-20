<?php
require "../../core/database.php";
$token = "ISI_TOKEN_VERIFIKASI_DARI_DASHBOARD_XENDIT"; // Wajib isi!

$headers = getallheaders();
if (!isset($headers['X-Callback-Token']) || $headers['X-Callback-Token'] !== $token) {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if ($data) {
    $inv_id = mysqli_real_escape_string($conn, $data['id']);
    $status = mysqli_real_escape_string($conn, $data['status']); // SETTLED / PAID

    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE invoice_id='$inv_id'");
    http_response_code(200);
}