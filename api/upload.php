<?php

require_once __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $upload_id = '';
  while ($upload_id === '') {
    $random_str = random_str(12);
    $path = __DIR__ . "/data/$random_str.pdf";
    if (!file_exists($path)) $upload_id = $random_str;
  }

  $upload_path = __DIR__ . "/data/$upload_id.pdf";
  move_uploaded_file($_FILES['file']['tmp_name'], $upload_path);

  echo $upload_id;
}

?>
