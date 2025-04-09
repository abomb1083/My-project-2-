<?php
require 'auth.php';
require 'db.php';

if (!isset($_GET['id'])) {
    die("Не указан ID приказа");
}

$order_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT o.id, o.order_date, o.content, e.name AS event_name
                       FROM orders o
                       JOIN events e ON o.event_id = e.id
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Приказ не найден");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Печать приказа №<?= $order['id'] ?></title>
    <style>
        @page {
            size: A4;
            margin: 0mm;
        }
        body {
            font-family: Times New Roman, sans-serif;
            font-size: 14pt;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .sheet {
            width: 100%;
            min-height: 100vh;
            padding: 20mm;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header img {
            width: 200px;
            height: auto;
        }
        .header .info {
            flex: 1;
            text-align: center;
            font-size: 11pt;
            line-height: 1.4;
        }
        .order-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-top: 40px;
        }
        .date-number {
            text-align: right;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .content {
            line-height: 1.6;
            white-space: pre-line;
            margin-top: 30px;
            text-align: justify;
        }
        .footer {
            margin-top: 60px;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4b6cb7;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Печать</button>

    <div class="sheet">
        <div class="header">
            <img src="./assets/logo.png" alt="Логотип">
            <div class="info">
                ДЕПАРТАМЕНТ ОБРАЗОВАНИЯ И НАУКИ ГОРОДА МОСКВЫ<br>
                Государственное бюджетное профессиональное образовательное учреждение города Москвы<br>
                <strong>«Московский государственный колледж электроники и информационных технологий»</strong><br>
                115446, г. Москва, ул. Академика Миллионщикова, д. 35. E-mail: spo-mgkeit@edu.mos.ru<br>
                ОГРН 1027739010161, ИНН 7724033545, КПП 772401001
            </div>
        </div>

        <div class="order-title">ПРИКАЗ</div>
        <div class="date-number">№ <?= $order['id'] ?> от <?= date("d.m.Y", strtotime($order['order_date'])) ?></div>

        <div class="content">

            <?= "" . htmlspecialchars($order['content']) ?>
        </div>

        <div class="footer">
            Ответственный: ___________________________
        </div>
    </div>
</body>
</html>