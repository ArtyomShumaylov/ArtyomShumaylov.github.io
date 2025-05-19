<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit();
}

echo "Добро пожаловать, " . $_SESSION['username'] . "! Это ваш личный кабинет.";
?>
