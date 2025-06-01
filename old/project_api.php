<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validator.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

session_start();
$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$errors = validate($input);
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

if ($method === 'POST') {
    $login = 'user' . rand(1000, 9999);
    $password = bin2hex(random_bytes(4));
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (login, password, fio, phone, email, birthdate, gender, bio)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $login, $hash, $input['fio'], $input['phone'], $input['email'],
        $input['birthdate'], $input['gender'], $input['bio']
    ]);

    $user_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
    foreach ($input['languages'] as $lang) {
        $stmt->execute([$user_id, $lang]);
    }

    echo json_encode([
        'message' => 'User created',
        'login' => $login,
        'password' => $password,
        'profile_url' => "/profile.php?id=$user_id"
    ]);
    exit;
}

if ($method === 'PUT' && isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET fio=?, phone=?, email=?, birthdate=?, gender=?, bio=? WHERE id=?");
    $stmt->execute([
        $input['fio'], $input['phone'], $input['email'],
        $input['birthdate'], $input['gender'], $input['bio'], $id
    ]);

    $pdo->prepare("DELETE FROM user_languages WHERE user_id=?")->execute([$id]);
    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
    foreach ($input['languages'] as $lang) {
        $stmt->execute([$id, $lang]);
    }

    echo json_encode(['message' => 'Data updated']);
    exit;
}

http_response_code(403);
echo json_encode(['error' => 'Unauthorized']);
