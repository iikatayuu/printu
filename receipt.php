<?php

require_once './api/config.inc.php';
require_once './api/database.php';
require_once './api/pdf.php';
require_once './api/utils.php';

$id = !empty($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;
if ($id === null) die('Invalid params');

$receipt_res = $conn->query("SELECT * FROM documents WHERE id=$id LIMIT 1");
if ($receipt_res->num_rows === 0) die('Invalid params');

$receipt = $receipt_res->fetch_object();
if ($receipt->paid) {
  echo 'This document was already paid';
  header('Location: /');
  exit;
}

$no_of_copies = intval($receipt->copies);
$color_profile = $receipt->color === 'RGB' ? 'Colored' : 'Grayscale';
$npps = intval($receipt->npps);
$upload = $receipt->upload;
$date = $receipt->added;
$upload_path = __DIR__ . "/api/data/$upload.pdf";
$pages = get_pdf_pages($upload_path);
$total = 0;
$colored = 0;
$grayscaled = 0;
$tmp_path = __DIR__ . "/api/data/$upload";

pdf_generate_images($upload_path, $tmp_path);

$pages = ceil($pages / $npps);

if ($color_profile === 'Grayscale') {
  $grayscaled = $pages * $no_of_copies;
  $total = 3 * $grayscaled;
}

if ($color_profile === 'Colored') {
  $ink_coverages = get_ink_coverage($upload_path, $tmp_path);
  $last = count($ink_coverages);
  $current = 1;
  $rgb = false;

  foreach ($ink_coverages as $page) {
    if ($page === 'RGB') {
      $rgb = true;
    }

    if ($current % $npps === 0 || $current === $last) {
      if ($rgb) {
        $rgb = false;
        $colored += $no_of_copies;
        $total += 5 * $no_of_copies;
      } else {
        $grayscaled += $no_of_copies;
        $total += 3 * $no_of_copies;
      }
    }

    $current++;
  }
}

$data = [
  'currency' => 'PHP',
  'amount' => $total,
  'payment_method' => [
    'type' => 'EWALLET',
    'reusability' => 'ONE_TIME_USE',
    'ewallet' => [
      'channel_code' => 'PAYMAYA',
      'channel_properties' => [
        'success_return_url' => "$xendit_success_url?id=$id",
        'failure_return_url' => "$origin/receipt.php?id=$id",
        'cancel_return_url' => "$origin/receipt.php?id=$id"
      ]
    ]
  ],
  'customer_id' => guidv4(),
  'metadata' => ['id' => $id]
];

$token = base64_encode("$xendit_secret_key:");
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://api.xendit.co/payment_requests');
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/api/cacert.pem');
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Accept: application/json',
  'Content-Type: application/json',
  "Authorization: Basic $token"
]);

$output = curl_exec($curl);
curl_close($curl);

$json = json_decode($output);
$payment = $json->id;
$payment_url = $json->actions[0]->url;
$conn->query("UPDATE documents SET payment='$payment' WHERE id=$id");

?>
<!DOCTYPE html><!--  This site was created in Webflow. https://www.webflow.com  -->
<!--  Last Published: Sat Feb 11 2023 09:58:58 GMT+0000 (Coordinated Universal Time)  -->
<html data-wf-page="63cbddeb1fe6f5bc8d4ce53e" data-wf-site="63c2574b2405e7464ec569cc">
<head>
  <meta charset="utf-8">
  <title>Receipt</title>
  <meta content="Receipt" property="og:title">
  <meta content="Receipt" property="twitter:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta content="Webflow" name="generator">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/printu-247883.webflow.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic","Montserrat:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic","Changa One:400,400italic","PT Serif:400,400italic,700,700italic"]  }});</script>
  <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
  <link href="css/receipt.css" rel="stylesheet">
</head>
<body>
  <div data-animation="default" data-collapse="medium" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="navbar-2 w-nav">
    <div class="w-container">
      <a href="#" class="w-nav-brand"><img src="images/Logo.png" loading="lazy" width="65" sizes="65px" srcset="images/Logo-p-500.png 500w, images/Logo-p-800.png 800w, images/Logo-p-1080.png 1080w, images/Logo-p-1600.png 1600w, images/Logo-p-2000.png 2000w, images/Logo.png 2484w" alt="" class="image-8"></a>
      <nav role="navigation" class="w-nav-menu">
        <a href="index.html" class="nav-link-6 w-nav-link">Home</a>
        <a href="tutorial.html" class="nav-link-5 w-nav-link">Tutorial</a>
        <div data-hover="false" data-delay="0" class="w-dropdown">
          <div class="w-dropdown-toggle">
            <div class="w-icon-dropdown-toggle"></div>
            <div class="text-block-12">Pricing</div>
          </div>
          <nav class="w-dropdown-list">
            <a href="#" class="dropdown-link w-dropdown-link">Plain Black - Php 3.00</a>
            <a href="#" class="dropdown-link-2 w-dropdown-link">Colored - Php 5.00</a>
            <a href="#" class="dropdown-link-4 w-dropdown-link">Full page image - Php 8.00</a>
          </nav>
        </div>
        <a href="about.html" class="nav-link-4 w-nav-link">about</a>
      </nav>
      <div class="w-nav-button">
        <div class="w-icon-nav-menu"></div>
      </div>
    </div>
  </div>
  <div class="container-2 w-container">
    <div class="div-block-7">
      <h3 class="heading-3">Print Set-up Selected:</h3>
      <div class="text-block-17">No. of copies: <?= $no_of_copies ?><br>Color profile: <?= $color_profile ?><br>Pages per sheet: <?= $npps ?></div>
    </div><img src="images/receiptUntitled-2.png" loading="lazy" sizes="(max-width: 479px) 100vw, 450px" srcset="images/receiptUntitled-2-p-500.png 500w, images/receiptUntitled-2-p-800.png 800w, images/receiptUntitled-2-p-1080.png 1080w, images/receiptUntitled-2-p-1600.png 1600w, images/receiptUntitled-2.png 1800w" alt="" class="image-9">
    <a href="<?= $payment_url ?>" id="proceed-gcash" class="button-6 w-button">Proceed to Payment</a>
    <div class="text-block-21">Date of transcation: <?= $date ?><br>OR #:</div>
    <div class="text-block-19 receipt-row">
      <div>Item</div>
      <div>Qty</div>
      <div>Sub</div>
    </div>
    <div class="text-block-18" style="width:200px;">
      <div class="receipt-row">
        <div>Colored:</div>
        <div><?= $colored ?></div>
        <div>Php <?= number_format($colored * 5, 2) ?></div>
      </div>
      <div class="receipt-row">
        <div>Black and white:</div>
        <div><?= $grayscaled ?></div>
        <div>Php <?= number_format($grayscaled * 3, 2) ?></div>
      </div>
    </div>
    <div class="text-block-20">______________________</div>
    <h1 class="heading-4" style="width:auto;">Total: Php <?= number_format($total, 2) ?></h1>
  </div>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=63c2574b2405e7464ec569cc" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/default.js"></script>
    <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
</body>
</html>