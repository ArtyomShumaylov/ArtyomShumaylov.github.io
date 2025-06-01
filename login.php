<?php
/*
    Aвторизация пользователей

    Проверяет логин и пароль

    При успешной проверке устанавливает сессию и перенаправляет на dashboard.php

    Иначе выводит сообщение ошибки
*/

session_start();

$pdo = new PDO('mysql:host=localhost;dbname=testdb', 'username', 'password');

// Получение данных из формы
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Подготовка SQL-запроса
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username AND password = :password');
$stmt->execute(['username' => $username, 'password' => $password]);

// Проверка наличия пользователя
$user = $stmt->fetch();
if ($user) {
    echo 'Добро пожаловать, ' . htmlspecialchars($user['username']);
} else {
    echo 'Неверный логин или пароль';
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
