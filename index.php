<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); 
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=u68534;charset=utf8', 'u68534', 'webpass123');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo "Этот логин уже занят!";
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)');
    $stmt->execute([$username, $password_hash, $email]);

    echo "Регистрация прошла успешно!";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
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
    <title>Форма регистрации и входа</title>
</head>
<body>
<h2>Форма регистрации и входа</h2>

<form action="" method="POST">
    <h3>Регистрация</h3>
    <label for="username">Логин:</label>
    <input type="text" name="username" required><br><br>

    <label for="password">Пароль:</label>
    <input type="password" name="password" required><br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>

    <button type="submit" name="register">Зарегистрироваться</button>
</form>

<hr>

<form action="" method="POST">
    <h3>Вход</h3>
    <label for="username">Логин:</label>
    <input type="text" name="username" required><br><br>

    <label for="password">Пароль:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Войти</button>
</form>

</body>
</html>
