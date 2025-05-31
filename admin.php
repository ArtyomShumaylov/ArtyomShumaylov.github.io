<?php
// Работа админа

session_start();
require_once 'db.php'; 

// --- HTTP авторизация ---
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Требуется авторизация';
    exit;
}

$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

$stmt = $pdo->prepare('SELECT * FROM admin_users WHERE login = ?');
$stmt->execute([$username]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Неверные учетные данные';
    exit;
}

// --- Удаление пользователя ---
if (isset($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM user_languages WHERE user_id = ?')->execute([$userId]);
    $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
    header('Location: admin.php');
    exit;
}

// --- Сохранение изменений ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET fio=?, phone=?, email=?, birthdate=?, gender=?, bio=? WHERE id=?");
    $stmt->execute([
        $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthdate'],
        $_POST['gender'], $_POST['bio'], $userId
    ]);

    $pdo->prepare('DELETE FROM user_languages WHERE user_id = ?')->execute([$userId]);
    if (!empty($_POST['languages'])) {
        $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
        foreach ($_POST['languages'] as $lang) {
            $stmt->execute([$userId, $lang]);
        }
    }

    header('Location: admin.php');
    exit;
}

// --- Получение всех пользователей ---
$users = $pdo->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC);

// --- Получение языков для каждого пользователя ---
$userLanguages = [];
$stmt = $pdo->prepare('SELECT * FROM user_languages WHERE user_id = ?');
foreach ($users as $user) {
    $stmt->execute([$user['id']]);
    $userLanguages[$user['id']] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// --- Статистика по языкам ---
$langStats = $pdo->query('SELECT language, COUNT(*) as count FROM user_languages GROUP BY language')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
</head>
<body>
    <h2>Панель администратора</h2>

    <h3>Пользователи</h3>
    <?php foreach ($users as $user): ?>
        <form method="POST" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            ФИО: <input type="text" name="fio" value="<?= htmlspecialchars($user['fio']) ?>"><br>
            Телефон: <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"><br>
            Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>
            Дата рождения: <input type="date" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>"><br>
            Пол: 
            <select name="gender">
                <option value="М" <?= $user['gender'] === 'М' ? 'selected' : '' ?>>М</option>
                <option value="Ж" <?= $user['gender'] === 'Ж' ? 'selected' : '' ?>>Ж</option>
            </select><br>
            Биография:<br>
            <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea><br>
            Языки программирования:<br>
            <?php
            $langs = ['PHP', 'JavaScript', 'Python', 'C++', 'Java'];
            foreach ($langs as $lang): ?>
                <label>
                    <input type="checkbox" name="languages[]" value="<?= $lang ?>"
                    <?= in_array($lang, $userLanguages[$user['id']] ?? []) ? 'checked' : '' ?>>
                    <?= $lang ?>
                </label><br>
            <?php endforeach; ?>
            <button type="submit" name="update_user">Сохранить</button>
            <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Удалить пользователя?')">Удалить</a>
        </form>
    <?php endforeach; ?>

    <h3>Статистика по языкам программирования</h3>
    <ul>
        <?php foreach ($langStats as $row): ?>
            <li><?= htmlspecialchars($row['language']) ?>: <?= $row['count'] ?> пользователей</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
