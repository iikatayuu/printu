<?php

require_once './api/database.php';

$id = !empty($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;
if ($id === null) die('Invalid params');

$receipt_res = $conn->query("SELECT * FROM documents WHERE id=$id LIMIT 1");
if ($receipt_res->num_rows === 0) die('Invalid params');

$receipt = $receipt_res->fetch_object();
$upload = $receipt->id;
$upload_path = __DIR__ . "/api/data/$upload.pdf";

readfile($upload_path);

?>
