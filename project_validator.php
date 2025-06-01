<?php
function validate_project_form($data, &$errors) {
    $validated = [];

    if (preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]{1,150}$/u', $data['fio'] ?? '')) {
        $validated['fio'] = trim($data['fio']);
    } else {
        $errors['fio'] = 'Некорректное ФИО';
    }

    if (preg_match('/^\+?[0-9\s\-\(\)]{7,15}$/', $data['phone'] ?? '')) {
        $validated['phone'] = trim($data['phone']);
    } else {
        $errors['phone'] = 'Некорректный телефон';
    }

    if (filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $validated['email'] = trim($data['email']);
    } else {
        $errors['email'] = 'Некорректный email';
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birthdate'] ?? '')) {
        $validated['birthdate'] = $data['birthdate'];
    } else {
        $errors['birthdate'] = 'Неверный формат даты';
    }

    if (in_array($data['gender'] ?? '', ['М', 'Ж'])) {
        $validated['gender'] = $data['gender'];
    } else {
        $errors['gender'] = 'Неверный пол';
    }

    $valid_languages = ['Pascal','C','C++','JavaScript','PHP','Python','Java','Haskel','Clojure','Prolog','Scala','Go'];
    $langs = $data['languages'] ?? [];
    if (!is_array($langs)) $langs = [$langs];

    $validated_langs = array_intersect($langs, $valid_languages);
    if (count($validated_langs) > 0) {
        $validated['languages'] = $validated_langs;
    } else {
        $errors['languages'] = 'Выберите хотя бы один язык';
    }

    if (strlen($data['bio'] ?? '') >= 10) {
        $validated['bio'] = trim($data['bio']);
    } else {
        $errors['bio'] = 'Биография должна быть минимум 10 символов';
    }

    if (!empty($data['contract'])) {
        $validated['contract'] = true;
    } else {
        $errors['contract'] = 'Необходимо согласие с контрактом';
    }

    return $validated;
}
