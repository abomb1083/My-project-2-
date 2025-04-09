<?php
require 'auth.php';
require 'db.php';

include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int) $_POST['order_id'];

    // Удаляем приказ
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);

    // Возврат на список приказов
    header("Location: orders.php");
    exit;
} else {
    die("Некорректный запрос");
}
