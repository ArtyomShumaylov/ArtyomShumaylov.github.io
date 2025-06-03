<?php
//Функциональность админа
session_start();

// Подключение к базе
$pdo = new PDO('mysql:host=localhost;dbname=u68534;charset=utf8', 'u68534', '9542530');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// HTTP аутентификация
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Админ-панель"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Требуется авторизация';
    exit;
}

$login = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

// Проверка логина и пароля администратора
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE login = ?");
$stmt->execute([$login]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Неверные данные для доступа';
    exit;
}

// Функции для DRY
function getAllUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserLanguages($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT language FROM user_languages WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getLanguageStats($pdo) {
    $stmt = $pdo->query("SELECT language, COUNT(*) as count FROM user_languages GROUP BY language ORDER BY count DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Обработка удаления пользователя
if (isset($_GET['delete_user'])) {
    $user_id = (int)$_GET['delete_user'];
    $stmt = $pdo->prepare("DELETE FROM user_languages WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Удаление пользователя
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    header('Location: admin.php');
    exit();
}

// Обработка редактирования данных пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = (int)$_POST['user_id'];
    $fio = $_POST['fio'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $bio = $_POST['bio'];
    $languages = $_POST['languages'] ?? [];

   
    $stmt = $pdo->prepare("UPDATE users SET fio=?, phone=?, email=?, birthdate=?, gender=?, bio=? WHERE id=?");
    $stmt->execute([$fio, $phone, $email, $birthdate, $gender, $bio, $user_id]);

    $stmt = $pdo->prepare("DELETE FROM user_languages WHERE user_id=?");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
    foreach ($languages as $lang) {
        $stmt->execute([$user_id, $lang]);
    }

    header('Location: admin.php');
    exit();
}

$users = getAllUsers($pdo);
$lang_stats = getLanguageStats($pdo);

$all_languages = ["Pascal","C","C++","JavaScript","PHP","Python","Java","Haskell","Clojure","Prolog","Scala","Go"];

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Админ-панель</title>
    <style>
        table {border-collapse: collapse; width: 100%;}
        th, td {border: 1px solid #ccc; padding: 5px;}
        form.inline {display: inline;}
    </style>
</head>
<body>
    <h1>Админ-панель</h1>
    <p>Вы вошли как: <strong><?=htmlspecialchars($login)?></strong></p>

    <h2>Пользователи</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Логин</th><th>ФИО</th><th>Телефон</th><th>Email</th><th>Дата рождения</th><th>Пол</th><th>Биография</th><th>Языки</th><th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): 
            $user_langs = getUserLanguages($pdo, $user['id']);
        ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username'] ?? $user['login']) ?></td>
                <td><?= htmlspecialchars($user['fio']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['birthdate']) ?></td>
                <td><?= htmlspecialchars($user['gender']) ?></td>
                <td><?= nl2br(htmlspecialchars($user['bio'])) ?></td>
                <td><?= htmlspecialchars(implode(", ", $user_langs)) ?></td>
                <td>
                    <!-- Кнопка удаления -->
                    <form method="get" class="inline" onsubmit="return confirm('Удалить пользователя #<?=$user['id']?>?');">
                        <input type="hidden" name="delete_user" value="<?= $user['id'] ?>">
                        <button type="submit">Удалить</button>
                    </form>

                    <!-- Кнопка редактирования -->
                    <button onclick="document.getElementById('edit-form-<?=$user['id']?>').style.display='block'">Редактировать</button>

                    <!-- Форма редактирования-->
                    <div id="edit-form-<?=$user['id']?>" style="display:none; border:1px solid #aaa; padding:10px; margin-top:10px;">
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="edit_user" value="1">

                            <label>ФИО:<br><input type="text" name="fio" value="<?= htmlspecialchars($user['fio']) ?>" required></label><br>
                            <label>Телефон:<br><input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required></label><br>
                            <label>Email:<br><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
                            <label>Дата рождения:<br><input type="date" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>" required></label><br>
                            <label>Пол:<br>
                                <select name="gender" required>
                                    <option value="М" <?= $user['gender'] === 'М' ? 'selected' : '' ?>>М</option>
                                    <option value="Ж" <?= $user['gender'] === 'Ж' ? 'selected' : '' ?>>Ж</option>
                                </select>
                            </label><br>
                            <label>Биография:<br><textarea name="bio" rows="4" cols="40" required><?= htmlspecialchars($user['bio']) ?></textarea></label><br>
                            <label>Языки программирования:<br>
                                <select name="languages[]" multiple required>
                                    <?php foreach ($all_languages as $lang): ?>
                                        <option value="<?= $lang ?>" <?= in_array($lang, $user_langs) ? 'selected' : '' ?>><?= $lang ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label><br>
                            <button type="submit">Сохранить</button>
                            <button type="button" onclick="document.getElementById('edit-form-<?=$user['id']?>').style.display='none'">Отмена</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Статистика по языкам программирования</h2>
    <table>
        <thead>
            <tr><th>Язык</th><th>Количество пользователей</th></tr>
        </thead>
        <tbody>
        <?php foreach ($lang_stats as $stat): ?>
            <tr>
                <td><?= htmlspecialchars($stat['language']) ?></td>
                <td><?= (int)$stat['count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
