<?php

require_once './api/config.inc.php';
require_once './api/database.php';

$id = !empty($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;
if ($id === null) die('Invalid params');

$receipt_res = $conn->query("SELECT * FROM documents WHERE id=$id LIMIT 1");
if ($receipt_res->num_rows === 0) die('Invalid params');

$receipt = $receipt_res->fetch_object();
$payment = $receipt->payment;
$token = base64_encode("$xendit_secret_key:");
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://api.xendit.co/payment_requests/$payment");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/api/cacert.pem');
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Accept: application/json',
  'Content-Type: application/json',
  "Authorization: Basic $token"
]);

$output = curl_exec($curl);
curl_close($curl);

$json = json_decode($output);
if ($json->status === 'SUCCEEDED') {
  $conn->query("UPDATE documents SET paid=1 WHERE id=$id");
  header("Location: /quick-response.html?id=$id");
} else echo "Payment was not successful.";

?>
