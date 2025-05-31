<!-- 
    Aвторизация пользователей

    Проверяет логин и пароль

    При успешной проверке устанавливает сессию и перенаправляет на dashboard.php

    Иначе выводит сообщение ошибки
-->

<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=u68534;charset=utf8', 'u68534', '9542530');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header('Location: dashboard.php'); 
        exit();
    } else {
        echo "Неверный логин или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
</head>
<body>
<h2>Вход в систему</h2>

<form action="" method="POST">
    <label for="username">Логин:</label>
    <input type="text" name="username" required><br><br>

    <label for="password">Пароль:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Войти</button>
</form>

</body>
</html>
