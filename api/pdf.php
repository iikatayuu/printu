<?php

function get_pdf_pages ($path) {
  $pdfinfo = '';
  $os = '';
  $bit = PHP_INT_SIZE === 4 ? '32' : '64';

  if (PHP_OS === 'WINNT' || PHP_OS === 'Windows' || PHP_OS === 'WIN32') $os = 'win';
  if (PHP_OS === 'LINUX' || PHP_OS === 'Linux') $os = 'linux';

  $pdfinfo = __DIR__ . "/../bin/xpdf-tools-$os-4.04/bin$bit/pdfinfo";
  chmod($pdfinfo, 0777);
  exec("$pdfinfo \"$path\"", $output);

  $pages = 0;
  foreach ($output as $line) {
    if (preg_match('/Pages:\s*(\d+)/i', $line, $matches) === 1) {
      $pages = intval($matches[1]);
      break;
    }
  }

  return $pages;
}

function get_ink_coverage ($path) {
  $pdfinfo = '';
  $os = '';

  if (PHP_OS === 'WINNT' || PHP_OS === 'Windows' || PHP_OS === 'WIN32') $os = 'win';
  if (PHP_OS === 'LINUX' || PHP_OS === 'Linux') $os = 'linux';

  $cmd = __DIR__ . "/../bin/gs-$os/gs";
  chmod($cmd, 0777);
  exec("$cmd -q -o - -sDEVICE=inkcov \"$path\"", $output);

  $pages = [];
  foreach ($output as $line) {
    preg_match('/([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\sCMYK OK/', $line, $matches);
    $c = floatval($matches[1]);
    $y = floatval($matches[2]);
    $m = floatval($matches[3]);
    $k = floatval($matches[4]);
    if ($c + $y + $m > 0.5) {
      $pages[] = 'RGB';
    } else {
      $pages[] = 'BW';
    }
  }

  return $pages;
}

?>
