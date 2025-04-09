<?php
require 'auth.php';
require 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$stmt = $pdo->prepare("UPDATE master_classes SET title = ?, teacher = ?, group_name = ?, attendees_count = ? WHERE id = ?");
$success = $stmt->execute([
    $data['title'],
    $data['teacher'],
    $data['group_name'],
    $data['attendees_count'],
    $data['id']
]);

echo json_encode(['success' => $success]);