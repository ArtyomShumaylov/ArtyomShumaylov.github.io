<?php
// Белый список доступных страниц
$allowed_pages = ['home.php', 'about.php', 'contact.php'];

// Получение запрашиваемой страницы
$page = $_GET['page'] ?? 'home.php';

// Проверка
if (in_array($page, $allowed_pages)) {
    include $page;
} else {
    echo 'Page not found';
}
?>
