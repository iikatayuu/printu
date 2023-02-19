<?php

require_once __DIR__ . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idnumber = $conn->real_escape_string($_POST['idnumber']);
  $copies = intval($_POST['copies']);
  $email = $conn->real_escape_string($_POST['email']);
  $color = $conn->real_escape_string($_POST['color']);
  $npps = intval($_POST['npps']);
  $upload_id = $conn->real_escape_string($_POST['file']);

  $conn->query("INSERT INTO documents (idnumber, copies, email, color, npps, upload)
    VALUES ('$idnumber', $copies, '$email', '$color', $npps, '$upload_id')");

  echo $conn->insert_id;
}

?>