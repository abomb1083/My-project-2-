<?php
require 'auth.php';
require 'db.php';

include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);

    // Сначала удалим мастер-классы, связанные с мероприятием
    $stmt1 = $pdo->prepare("DELETE FROM master_classes WHERE event_id = ?");
    $stmt1->execute([$event_id]);

    // Затем удалим приказы, если есть
    $stmt2 = $pdo->prepare("DELETE FROM orders WHERE event_id = ?");
    $stmt2->execute([$event_id]);

    // Теперь удалим само мероприятие
    $stmt3 = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt3->execute([$event_id]);
}

header("Location: index.php");
exit;
