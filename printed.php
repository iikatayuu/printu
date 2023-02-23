<?php

require_once './api/database.php';
require_once './api/utils.php';

cors();

$upload = !empty($_GET['upload']) ? $conn->real_escape_string($_GET['upload']) : null;
$page = !empty($_GET['page']) ? intval($_GET['page']) : null;
$hash = !empty($_GET['hash']) ? $conn->real_escape_string($_GET['hash']) : null;

if ($upload === null || $hash === null) die('Invalid params');

$receipt_res = $conn->query("SELECT * FROM documents WHERE upload='$upload' LIMIT 1");
if ($receipt_res->num_rows === 0) die('Invalid params');

$receipt = $receipt_res->fetch_object();
$id = $receipt->id;
$upload_path = __DIR__ . "/api/data/$upload.pdf";
$hashfile = hash_file('md5', $upload_path);

if ($hashfile === $hash) {
  $conn->query("UPDATE documents SET printed=$page WHERE id=$id");
  echo 'success';
} else {
  echo 'invalid hash';
}

?>
