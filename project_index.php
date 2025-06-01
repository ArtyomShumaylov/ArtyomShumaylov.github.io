<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

session_start();
$user = check_auth($pdo);

$errors = $_SESSION['form_errors'] ?? [];
$values = $_SESSION['form_values'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['form_values']);

require_once __DIR__ . '/templates/form_template.php';
?>
