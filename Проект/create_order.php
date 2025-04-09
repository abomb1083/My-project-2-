<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'auth.php';
require 'db.php';

include 'header.php'; 

if (!isset($_GET['event_id'])) {
    die("Не передан ID мероприятия.");
}

$event_id = (int) $_GET['event_id'];

// Получаем информацию о мероприятии
$eventStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$eventStmt->execute([$event_id]);
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Мероприятие не найдено.");
}

// Получаем мастер-классы и агрегируем данные по преподавателям
$mcStmt = $pdo->prepare("SELECT teacher, attendees_count FROM master_classes WHERE event_id = ?");
$mcStmt->execute([$event_id]);
$classes = $mcStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($classes)) {
    die("Нет мастер-классов у данного мероприятия.");
}

// Собираем данные по преподавателям
$teachers = [];

foreach ($classes as $class) {
    $teacher = $class['teacher'];
    $count = $class['attendees_count'];
    if (!isset($teachers[$teacher])) {
        $teachers[$teacher] = 0;
    }
    $teachers[$teacher] += $count;
}

// Формируем текст приказа
$lines = [];
$lines[] = "ПРИКАЗ";
$lines[] = "о проведении мастер-классов в рамках мероприятия «{$event['name']}»";
$lines[] = "";
$lines[] = "Дата проведения: {$event['date']}";
$lines[] = "Место проведения: {$event['location']}";
$lines[] = "";

foreach ($teachers as $teacher => $count) {
    $lines[] = "Преподаватель {$teacher} провёл мастер-классы с общим количеством участников: {$count}";
}

$lines[] = "";
//$lines[] = "Ответственный: ___________________";
$content = implode("\n", $lines);

// Сохраняем в таблицу orders
$insert = $pdo->prepare("INSERT INTO orders (event_id, content) VALUES (?, ?)");
$insert->execute([$event_id, $content]);

$order_id = $pdo->lastInsertId();

// Перенаправляем на просмотр приказа
header("Location: view_order.php?id=$order_id&created=1");
exit;
