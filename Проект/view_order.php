<?php
require 'auth.php';
require 'db.php';

if (!isset($_GET['id'])) {
    die("ID приказа не указан.");
}

$order_id = (int) $_GET['id'];

// Получаем приказ
$stmt = $pdo->prepare("SELECT o.*, e.name AS event_name
                       FROM orders o
                       JOIN events e ON o.event_id = e.id
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Приказ не найден.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр приказа</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Приказ №<?= $order['id'] ?></h1>
    <p><strong>Мероприятие:</strong> <?= htmlspecialchars($order['event_name']) ?></p>
    <p><strong>Дата:</strong> <?= htmlspecialchars($order['order_date']) ?></p>

    <hr>

    <div style="white-space: pre-wrap; font-size: 16px; line-height: 1.6;">
        <?= htmlspecialchars($order['content']) ?>
    </div>

    <a href="orders.php" class="btn" style="margin-top: 5 px; background-color: #888;">← Назад</a>
</div>
</body>
</html>
