<?php

require_once __DIR__ . '/config.inc.php';

$conn = new mysqli(
  $mysql_host,
  $mysql_user,
  $mysql_pass,
  $mysql_database
);

if ($conn->connect_errno) die($conn->connect_error);

?>
