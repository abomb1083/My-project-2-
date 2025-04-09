<?php
require 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Проверка полей
    if ($username === '' || $password === '' || $confirm === '') {
        $message = "Пожалуйста, заполните все поля.";
    } elseif ($password !== $confirm) {
        $message = "Пароли не совпадают.";
    } else {
        // Проверка, существует ли пользователь
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $message = "Пользователь с таким логином уже существует.";
        } else {
            // Хеширование пароля и сохранение
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
            $stmt->execute(['username' => $username, 'password_hash' => $hash]);
            $message = "Регистрация прошла успешно! <a href='login.php'>Войти</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация администратора</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Регистрация администратора</h1>

        <?php if ($message): ?>
            <p style="color: <?= strpos($message, 'успешно') !== false ? 'green' : 'red' ?>"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Логин</label>
            <input type="text" name="username" required>

            <label>Пароль</label>
            <input type="password" name="password" required>

            <label>Повторите пароль</label>
            <input type="password" name="confirm" required>

            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>
</body>
</html>
