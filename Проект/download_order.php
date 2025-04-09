<?php
require 'auth.php';
require 'db.php';
require 'vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_GET['id'])) {
    die("ID приказа не указан.");
}

$order_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT o.*, e.name AS event_name FROM orders o JOIN events e ON o.event_id = e.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Приказ не найден.");
}

$mpdf = new Mpdf();
$mpdf->SetTitle('Приказ №' . $order['id']);

// HTML-шаблон
$html = '
<html>
<head>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; }
.header { text-align: center; margin-bottom: 10px; }
.header img { float: left; width: 80px; }
.header .info { text-align: center; font-size: 11pt; }
.order-title { text-align: center; font-weight: bold; font-size: 14pt; margin: 30px 0 20px; }
.section { margin-bottom: 20px; }
.content { line-height: 1.6; text-align: justify; font-size: 12pt; }
</style>
</head>
<body>

<div class="header">
    <img src="assets/logo.png" alt="Логотип">
    <div class="info">
        <div>ДЕПАРТАМЕНТ ОБРАЗОВАНИЯ И НАУКИ ГОРОДА МОСКВЫ</div>
        <div><strong>Государственное бюджетное профессиональное образовательное учреждение города Москвы</strong></div>
        <div><strong>«Московский государственный колледж электроники и информационных технологий»</strong></div>
        <div style="margin-top: 10px; font-size: 10pt;">
            115446, г. Москва, ул. Академика Миллионщикова, д. 35. E-mail: spo-mgkeit@edu.mos.ru<br>
            ОГРН 1027739010161, ИНН 7724033545, КПП 772401001
        </div>
    </div>
</div>

<div class="order-title">ПРИКАЗ</div>
<div style="text-align: right; margin-bottom: 10px;">№ ' . $order['id'] . ' от ' . date("d.m.Y", strtotime($order['order_date'])) . '</div>

<div class="section">
    <div class="content">' . nl2br(htmlspecialchars($order['content'])) . '</div>
</div>

</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output('Приказ_' . $order['id'] . '.pdf', 'I'); // I — открыть в браузере
exit;
