<?php
require 'auth.php';
require 'db.php';

// Проверка наличия всех обязательных данных
if (
    empty($_POST['event_name']) ||
    empty($_POST['event_date']) ||
    empty($_POST['event_location'])
) {
    die("Пожалуйста, заполните все обязательные поля.");
}

// Получение данных мероприятия
$name = $_POST['event_name'];
$date = $_POST['event_date'];
$location = $_POST['event_location'];
$description = $_POST['event_description'] ?? '';

// Вставка мероприятия
$sqlEvent = "INSERT INTO events (name, date, location, description) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sqlEvent);
$stmt->execute([$name, $date, $location, $description]);

$eventId = $pdo->lastInsertId();

// Обработка мастер-классов
$mc_titles = $_POST['mc_title'] ?? [];
$mc_teachers = $_POST['mc_teacher'] ?? [];
$mc_groups = $_POST['mc_group'] ?? [];
$mc_counts = $_POST['mc_count'] ?? [];

$sqlMC = "INSERT INTO master_classes (event_id, title, teacher, group_name, attendees_count)
          VALUES (?, ?, ?, ?, ?)";
$stmtMC = $pdo->prepare($sqlMC);

for ($i = 0; $i < count($mc_titles); $i++) {
    $title = $mc_titles[$i];
    $teacher = $mc_teachers[$i];
    $group = $mc_groups[$i];
    $count = (int) $mc_counts[$i];

    // Если хотя бы одно поле заполнено, добавляем мастер-класс
    if ($title || $teacher || $group || $count) {
        $stmtMC->execute([$eventId, $title, $teacher, $group, $count]);
    }
}

// Перенаправление обратно на главную
header("Location: index.php");
exit;
