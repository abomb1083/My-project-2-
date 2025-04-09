<?php
require_once __DIR__ . '/vendor/autoload.php';
require 'db.php';

if (!isset($_GET['id'])) {
    die("ID приказа не передан.");
}

$order_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT o.*, e.name AS event_name, e.date AS event_date, e.location 
                       FROM orders o 
                       JOIN events e ON o.event_id = e.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Приказ не найден.");
}

$logoPath = __DIR__ . '/assets/logo.png';
$logoData = base64_encode(file_get_contents($logoPath));
$logoSrc = 'data:image/png;base64,' . $logoData;

$html = '
<style>
body {
    font-family: Times New Roman, sans-serif;
    font-size: 14pt;
    line-height: 1.5;
    margin: 0;
    padding: 0;
}
.page {
    width: 210mm;
    height: 280mm;
    padding: 30mm 30mm 25mm 25mm;
    box-sizing: border-box;
}
.header {
    text-align: center;
    margin-bottom: 20px;
}
.logo {
    width: 130px;
    margin-bottom: 10px;
}
.title {
    font-weight: bold;
    font-size: 16pt;
    margin-top: 10px;
}
.subtitle {
    font-size: 14pt;
    margin-top: 5px;
    margin-bottom: 20px;
}
.content {
    white-space: pre-wrap;
    text-align: justify;
}
</style>

<div class="page">
    <div class="header">
        <img src="' . $logoSrc . '" class="logo" />
        <div class="title">ГБПОУ МГКЭИТ "Колледж"</div>
        <div class="subtitle">ПРИКАЗ №' . $order['id'] . ' от ' . date('d.m.Y', strtotime($order['order_date'])) . '</div>
    </div>

    <div class="content">' . nl2br(htmlspecialchars($order['content'])) . '</div>
</div>
';

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'margin_left' => 25,
    'margin_right' => 25,
    'margin_top' => 30,
    'margin_bottom' => 25,
]);

$mpdf->WriteHTML($html);
$mpdf->Output('prikaz_' . $order['id'] . '.pdf', 'I');
