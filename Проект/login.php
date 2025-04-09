<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Неверный логин или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Вход в систему</h1>

        <?php if ($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Логин</label>
            <input type="text" name="username" required>

            <label>Пароль</label>
            <input type="password" name="password" required>

            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>
