<?php

require_once __DIR__ . '/colors.inc.php';

function get_pdf_pages ($path) {
  $pdfinfo = '';
  $os = '';
  $bit = PHP_INT_SIZE === 4 ? '32' : '64';
  $ext = '';

  if (PHP_OS === 'WINNT' || PHP_OS === 'Windows' || PHP_OS === 'WIN32') {
    $os = 'win';
    $ext = '.exe';
  }

  if (PHP_OS === 'LINUX' || PHP_OS === 'Linux') $os = 'linux';

  $pdfinfo = __DIR__ . "/../bin/xpdf-tools-$os-4.04/bin$bit/pdfinfo$ext";
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

function get_ink_coverage ($path, $tmp_path) {
  $base = basename($path);
  $pdftopng = '';
  $os = '';
  $bit = PHP_INT_SIZE === 4 ? '32' : '64';
  $ext = '';

  if (PHP_OS === 'WINNT' || PHP_OS === 'Windows' || PHP_OS === 'WIN32') {
    $os = 'win';
    $ext = '.exe';
  }

  if (PHP_OS === 'LINUX' || PHP_OS === 'Linux') $os = 'linux';

  $pdftopng = __DIR__ . "/../bin/xpdf-tools-$os-4.04/bin$bit/pdftopng$ext";
  chmod($pdftopng, 0777);
  exec("$pdftopng \"$path\" \"$tmp_path\"");

  $extractor = new GetMostCommonColors();
  $pages = get_pdf_pages($path);
  $output = [];

  for ($i = 1; $i <= $pages; $i++) {
    $page_str = strval($i);
    while (strlen($page_str) < 6) $page_str = "0$page_str";

    $colors = $extractor->Get_Color("$tmp_path-$page_str.png", 2, 1, 1, 24);
    $is_colored = false;

    foreach ($colors as $hex => $percentage) {
      $rgb = intval($hex, 16);
      $r = ($rgb >> 16) & 0xff;
      $g = ($rgb >> 8) & 0xff;
      $b = ($rgb >> 0) & 0xff;
      $luma = ((299 * $r) + (587 * $g) + (114 * $b)) / 1000;
      if ($luma < 40 || $hex === 'ffffff') continue;
      if ($percentage > 0.2) $is_colored = true;
    }

    $output[] = $is_colored ? 'RGB' : 'BW';
  }

  return $output;
}

?>
