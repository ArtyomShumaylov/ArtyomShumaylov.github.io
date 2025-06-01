<?php
require_once 'includes/project_db.php';
require_once 'includes/project_validator.php';
require_once 'includes/project_auth.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$errors = [];
$data = $_POST;
$validated = validate_project_form($data, $errors);

if (!empty($errors)) {
    setcookie('project_form_errors', serialize($errors), 0, '/');
    setcookie('project_form_values', serialize($data), 0, '/');
    header('Location: index.php');
    exit;
}

$pdo = connect_project_db();

if ($user = get_authenticated_user()) {
    update_user_data($pdo, $user['id'], $validated);
} else {
    $loginInfo = register_new_user($pdo, $validated);
    $_SESSION['user_id'] = $loginInfo['id'];
    $_SESSION['username'] = $loginInfo['login'];
    setcookie('new_credentials', json_encode([
        'login' => $loginInfo['login'],
        'password' => $loginInfo['password'],
        'profile_url' => '/project_profile.php?id=' . $loginInfo['id']
    ]), time() + 60, '/');
}

header('Location: index.php');
exit;
