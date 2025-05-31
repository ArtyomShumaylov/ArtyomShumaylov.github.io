<!-- 
    ичный кабинет.

    Показывает приветствие авторизованному пользователю.

    Недоступен без входа в систему (проверка сессии).
-->

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit();
}

echo "Добро пожаловать, " . htmlspecialchars($_SESSION['username']) . "! Это ваш личный кабинет.";
?>
