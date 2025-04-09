<?php
$host = 'localhost'; // или IP-адрес вашего сервера
$db = 'postgres'; // имя вашей базы данных
$user = 'postgres'; // ваше имя пользователя PostgreSQL
$pass = '123'; // ваш пароль PostgreSQL

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    // Установим режим ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}
?>