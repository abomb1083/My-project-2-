<?php
require 'auth.php';
require 'db.php';

include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $mc_id = intval($_POST['mc_id']);

    $stmt = $pdo->prepare("DELETE FROM master_classes WHERE id = ?");
    $stmt->execute([$mc_id]);
}

header("Location: index.php");
exit;
