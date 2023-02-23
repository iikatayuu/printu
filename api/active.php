<?php

require_once __DIR__ . '/config.inc.php';

$timestamp = time();
$activepath = __DIR__ . '/active.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $req_body = file_get_contents('php://input');
  $post = json_decode($req_body, true);
  $pass = isset($post['pass']) ? $post['pass'] : null;
  if ($pass === null) die('Invalid params');

  if ($pass === $active_pass) {
    file_put_contents($activepath, $timestamp);
    echo 'success';
  }
} else {
  $last_active = file_get_contents($activepath);
  $last_time = intval($last_active);

  echo $timestamp - $last_time < 300 ? 'active' : 'offline';
}

?>
