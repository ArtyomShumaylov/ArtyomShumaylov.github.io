<?php
session_start();

$patterns = [
    'fio' => '/^[А-Яа-яЁёA-Za-z\s\-]{1,150}$/u',
    'phone' => '/^\+?[0-9\s\-\(\)]{7,20}$/',
    'email' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
    'birthdate' => '/^\d{4}-\d{2}-\d{2}$/',
    'gender' => '/^(М|Ж)$/u',
    'languages' => '/^(Pascal|C|C\+\+|JavaScript|PHP|Python|Java|Haskell|Clojure|Prolog|Scala|Go)$/',
    'bio' => '/^[^<>]{1,1000}$/u',
    'contract' => '/^on$/'
];

$fields = ['fio', 'phone', 'email', 'birthdate', 'gender', 'languages', 'bio', 'contract'];

$errors = [];
$values = [];

foreach ($fields as $field) {
    if (!isset($_POST[$field])) {
        $errors[$field] = 'Поле обязательно для заполнения.';
        continue;
    }

    $value = $_POST[$field];

    if ($field === 'languages') {
        if (!is_array($value) || count($value) == 0) {
            $errors[$field] = 'Выберите хотя бы один язык.';
        } else {
            foreach ($value as $lang) {
                if (!preg_match($patterns[$field], $lang)) {
                    $errors[$field] = 'Недопустимый язык программирования.';
                    break;
                }
            }
        }
    } elseif (!preg_match($patterns[$field], $value)) {
        switch ($field) {
            case 'fio':
                $errors[$field] = 'ФИО может содержать только буквы, пробелы и дефисы.';
                break;
            case 'phone':
                $errors[$field] = 'Поле "Телефон" должен содержать только цифры, пробелы, скобки, плюсы и дефисы и быть правильного формата';
                break;
            case 'email':
                $errors[$field] = 'Неверный формат email.';
                break;
            case 'birthdate':
                $errors[$field] = 'Неверный формат даты.';
                break;
            case 'gender':
                $errors[$field] = 'Выберите пол.';
                break;
            case 'bio':
                $errors[$field] = 'Биография не должна содержать HTML-теги.';
                break;
            case 'contract':
                $errors[$field] = 'Вы должны принять условия.';
                break;
            default:
                $errors[$field] = 'Неверное значение.';
        }
    }

    $values[$field] = $value;
}

if (!empty($errors)) {
    setcookie('form_errors', serialize($errors), 0, '/');
    setcookie('form_values', serialize($values), 0, '/');
    header('Location: index.php');
    exit();
}

foreach ($values as $key => $val) {
    $cookieName = 'form_saved_' . $key;
    if (is_array($val)) {
        setcookie($cookieName, serialize($val), time() + 365 * 24 * 60 * 60, '/');
    } else {
        setcookie($cookieName, $val, time() + 365 * 24 * 60 * 60, '/');
    }
}

setcookie('form_errors', '', time() - 3600, '/');
setcookie('form_values', '', time() - 3600, '/');

header('Location: index.php');
exit();
