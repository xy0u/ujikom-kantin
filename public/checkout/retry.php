<?php
session_start();
require "../../core/database.php";
require "../../vendor/autoload.php";

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

if (!isset($_SESSION['user_id'])) {
     header("Location: ../../auth/login.php");
     exit;
}

$order_id = (int) $_GET['order'];

$order = mysqli_fetch_assoc(mysqli_query(
     $conn,
     "
SELECT * FROM orders 
WHERE id=$order_id 
AND user_id=" . $_SESSION['user_id']
));

if (!$order || $order['status'] != "FAILED") {
     header("Location: ../orders.php");
     exit;
}

/* SET API */
Configuration::setXenditKey("ISI_API_KEY_KAMU");

$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
$project_path = dirname(dirname($_SERVER['PHP_SELF']));
$base = $base_url . $project_path;

$apiInstance = new InvoiceApi();

$createInvoiceRequest = new \Xendit\Invoice\CreateInvoiceRequest([
     'external_id' => 'ORDER-RETRY-' . $order_id,
     'amount' => $order['total'],
     'payer_email' => $_SESSION['user_name'],
     'description' => 'Retry Payment Order #' . $order_id,
     'success_redirect_url' => $base . '/success.php?order=' . $order_id,
     'failure_redirect_url' => $base . '/../orders.php'
]);

$invoice = $apiInstance->createInvoice($createInvoiceRequest);

mysqli_query($conn, "
UPDATE orders
SET invoice_id='" . $invoice['id'] . "', status='PENDING'
WHERE id=$order_id
");

header("Location: " . $invoice['invoice_url']);
exit;
