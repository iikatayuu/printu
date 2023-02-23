<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/utils.php';

cors();

$upload = !empty($_GET['upload']) ? $conn->real_escape_string($_GET['upload']) : null;
if ($upload === null) die('Invalid params');

$receipt_res = $conn->query("SELECT * FROM documents WHERE upload='$upload' LIMIT 1");
if ($receipt_res->num_rows === 0) die('Invalid params');

$receipt = $receipt_res->fetch_object();

header('Content-Type: application/json');
echo json_encode([
  'idnumber' => $receipt->idnumber,
  'copies' => intval($receipt->copies),
  'email' => $receipt->email,
  'color' => $receipt->color,
  'npps' => intval($receipt->npps)
]);

?>