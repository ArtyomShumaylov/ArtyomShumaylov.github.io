<?php
$host = 'localhost';
$dbname = 'u68534';
$user = 'u68534';
$pass = '9542530';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$errors = [];
$fio = trim($_POST['fio']);
if (!preg_match('/^[А-Яа-яЁё\s]+$/u', $fio) || mb_strlen($fio) > 150) {
    $errors[] = "ФИО должно содержать только буквы и пробелы, не более 150 символов.";
}

$phone = trim($_POST['phone']);
if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
    $errors[] = "Некорректный телефон.";
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    $errors[] = "Некорректный email.";
}

$birthdate = $_POST['birthdate'];
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
    $errors[] = "Некорректная дата рождения.";
}

$gender = $_POST['gender'];
if (!in_array($gender, ['М', 'Ж'])) {
    $errors[] = "Пол должен быть 'М' или 'Ж'.";
}

$languages = $_POST['languages'] ?? [];
$valid_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
foreach ($languages as $lang) {
    if (!in_array($lang, $valid_languages)) {
        $errors[] = "Недопустимый язык программирования: $lang";
    }
}

$bio = trim($_POST['bio']);
$contract = isset($_POST['contract']) ? 1 : 0;
if (!$contract) {
    $errors[] = "Необходимо согласие с контрактом.";
}

if (!empty($errors)) {
    echo "<h3>Ошибки:</h3><ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul><a href='javascript:history.back()'>Назад</a>";
    exit();
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO applications (fio, phone, email, birthdate, gender, bio, contract_accepted)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fio, $phone, $email, $birthdate, $gender, $bio, $contract]);

    $app_id = $pdo->lastInsertId();

    $stmt_lang = $pdo->prepare("INSERT INTO languages (application_id, language) VALUES (?, ?)");
    foreach ($languages as $lang) {
        $stmt_lang->execute([$app_id, $lang]);
    }

    $pdo->commit();
    echo "<h3> Данные успешно сохранены! </h3><a href='index.html'> Назад</a>";
} catch (Exception $e) {
    $pdo->rollBack();
    die("Ошибка при сохранении: " . $e->getMessage());
}
?>
