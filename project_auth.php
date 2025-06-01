<?php
function get_authenticated_user() {
    session_start();
    if (!empty($_SESSION['user_id'])) {
        return ['id' => $_SESSION['user_id'], 'login' => $_SESSION['username']];
    }
    return null;
}

function update_user_data($pdo, $userId, $data) {
    $stmt = $pdo->prepare("UPDATE users SET fio=?, phone=?, email=?, birthdate=?, gender=?, bio=? WHERE id=?");
    $stmt->execute([
        $data['fio'], $data['phone'], $data['email'],
        $data['birthdate'], $data['gender'], $data['bio'], $userId
    ]);

    $stmt = $pdo->prepare("DELETE FROM user_languages WHERE user_id=?");
    $stmt->execute([$userId]);

    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
    foreach ($data['languages'] as $lang) {
        $stmt->execute([$userId, $lang]);
    }
}

function register_new_user($pdo, $data) {
    $login = 'user' . rand(1000, 9999);
    $password = bin2hex(random_bytes(4));
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (login, password, fio, phone, email, birthdate, gender, bio)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $login, $hashed, $data['fio'], $data['phone'], $data['email'],
        $data['birthdate'], $data['gender'], $data['bio']
    ]);

    $user_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
    foreach ($data['languages'] as $lang) {
        $stmt->execute([$user_id, $lang]);
    }

    return ['id' => $user_id, 'login' => $login, 'password' => $password];
}
