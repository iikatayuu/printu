<?php

require_once './api/database.php';

$id = !empty($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;
$hash = !empty($_GET['hash']) ? $conn->real_escape_string($_GET['hash']) : null;

if ($id === null || $hash === null) die('Invalid params');

$receipt_res = $conn->query("SELECT * FROM documents WHERE id=$id LIMIT 1");
if ($receipt_res->num_rows === 0) die('Invalid params');

$receipt = $receipt_res->fetch_object();
$upload = $receipt->id;
$upload_path = __DIR__ . "/api/data/$upload.pdf";

$hashfile = hash_file('md5', $upload_path);
if ($hashfile === $hash) {
  $conn->query("UPDATE documents SET printed=1 WHERE id=$id");
  echo 'success';
} else {
  echo 'invalid hash';
}

?>