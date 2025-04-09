<?php
require 'auth.php';
require 'db.php';

header('Content-Type: application/json');

// Читаем JSON из запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Подготовка и выполнение запроса
$stmt = $pdo->prepare("UPDATE events SET name = ?, date = ?, location = ?, description = ? WHERE id = ?");
$success = $stmt->execute([
    $data['name'],
    $data['date'],
    $data['location'],
    $data['description'],
    $data['id']
]);

echo json_encode(['success' => $success]);
