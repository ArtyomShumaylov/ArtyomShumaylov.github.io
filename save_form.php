<?php
session_start();

$fields = ['fio', 'phone', 'email', 'birthdate', 'gender', 'languages', 'bio', 'contract'];
$errors = [];
$values = [];

$patterns = [
    'fio' => '/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u',
    'phone' => '/^\+?[0-9\s\-\(\)]{7,15}$/',
    'email' => '/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4}$/',
    'birthdate' => '/^\d{4}-\d{2}-\d{2}$/',
    'gender' => '/^(М|Ж)$/',
    'bio' => '/^.{10,}$/', 
];

foreach ($fields as $field) {
    if (!isset($_POST[$field])) {
        $errors[$field] = 'Поле обязательно для заполнения.';
        continue;
    }
    $value = $_POST[$field];
    if ($field === 'languages') {
        if (empty($value)) {
            $errors[$field] = 'Выберите хотя бы один язык.';
        }
    } elseif (!preg_match($patterns[$field], $value)) {
        $errors[$field] = 'Неверный формат поля ' . ucfirst($field) . '.';
    }
    $values[$field] = $value;
}

if (!empty($errors)) {
    setcookie('form_errors', serialize($errors), 0, '/');
    setcookie('form_values', serialize($values), 0, '/');
    header('Location: index.php');
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=u68534;charset=utf8', 'u68534', '9542530');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET fio=?, phone=?, email=?, birthdate=?, gender=?, bio=? WHERE id=?");
    $stmt->execute([
        $values['fio'], $values['phone'], $values['email'],
        $values['birthdate'], $values['gender'], $values['bio'],
        $_SESSION['user_id']
    ]);

    $stmt = $pdo->prepare("DELETE FROM user_languages WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);

    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
    foreach ($values['languages'] as $lang) {
        $stmt->execute([$_SESSION['user_id'], $lang]);
    }

    header('Location: index.php');
    exit();
}

$login = 'user' . rand(1000, 9999); 
$password = bin2hex(random_bytes(4)); 
$hashed = password_hash($password, PASSWORD_DEFAULT); 

$stmt = $pdo->prepare("INSERT INTO users (login, password, fio, phone, email, birthdate, gender, bio)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $login, $hashed, $values['fio'], $values['phone'], $values['email'],
    $values['birthdate'], $values['gender'], $values['bio']
]);

$user_id = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
foreach ($values['languages'] as $lang) {
    $stmt->execute([$user_id, $lang]);
}

$_SESSION['user_id'] = $user_id;

header('Location: index.php');
exit();
?>
