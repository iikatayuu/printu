<?php

require_once './api/config.inc.php';
require_once './api/database.php';

$pi = $conn->real_escape_string($_GET['payment_intent_id']);
$docs = $conn->query("SELECT * FROM documents WHERE payment='$pi' LIMIT 1");
if ($docs->num_rows === 0) die('Invalid params');
$doc = $docs->fetch_object();

$token = base64_encode("$paymongo_secret_key:");
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://api.paymongo.com/v1/payment_intents/$pi");
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
if ($json->data->attributes->status === 'succeeded') {
  $id = $doc->id;
  $conn->query("UPDATE documents SET paid=1 WHERE id=$id");
  header("Location: /quick-response.php?id=$id");
} else {
  die('Transaction did not succeed');
}

?>
