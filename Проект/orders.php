<?php
require 'auth.php';
require 'db.php';

include 'header.php'; 

$stmt = $pdo->query("SELECT o.id, o.order_date, o.content, e.name AS event_name
                     FROM orders o
                     JOIN events e ON o.event_id = e.id
                     ORDER BY o.order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список приказов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Список приказов</h1>
        <a href="index.php" class="btn" style="background-color: #888;">← Назад</a>
    </header>

    <?php if (count($orders) === 0): ?>
        <p style="color: #666;">Пока нет созданных приказов.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Мероприятие</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= date("d.m.Y", strtotime($order['order_date'])) ?></td>
                        <td><?= htmlspecialchars($order['event_name']) ?></td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id'] ?>" class="btn">👁️ Просмотр</a>
                            <a href="print_order.php?id=<?= $order['id'] ?>" class="btn" target="_blank">🖨️ Печать</a>

                            <form action="delete_order.php" method="POST" onsubmit="return confirm('Удалить приказ №<?= $order['id'] ?>?');" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn" style="background-color: #b33;">🗑 Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
