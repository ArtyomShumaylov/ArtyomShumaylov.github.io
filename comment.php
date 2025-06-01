<?php
// Экранирование вводимых данных

$comment = $_POST['comment'] ?? '';

$comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

file_put_contents('comments.txt', $comment . PHP_EOL, FILE_APPEND);

$comments = file_get_contents('comments.txt');
echo nl2br($comments);
?>
