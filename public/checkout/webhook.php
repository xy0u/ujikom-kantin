<?php
require "../../core/database.php";

/* ======================
   VERIFY CALLBACK TOKEN
====================== */

$callback_token = "ISI_CALLBACK_TOKEN_DARI_XENDIT";

$headers = getallheaders();

if (
    !isset($headers['X-Callback-Token']) ||
    $headers['X-Callback-Token'] !== $callback_token
) {

    http_response_code(403);
    exit("Invalid token");
}

/* ======================
   GET DATA
====================== */

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    exit("No data");
}

$invoice_id = mysqli_real_escape_string($conn, $data['id']);
$status = mysqli_real_escape_string($conn, $data['status']);

/* ======================
   UPDATE ORDER
====================== */

mysqli_query($conn, "
UPDATE orders 
SET status='$status'
WHERE invoice_id='$invoice_id'
");

http_response_code(200);
echo "OK";
