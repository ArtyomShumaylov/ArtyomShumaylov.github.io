<?php
$errors = isset($_COOKIE['form_errors']) ? unserialize($_COOKIE['form_errors']) : [];
$values = isset($_COOKIE['form_values']) ? unserialize($_COOKIE['form_values']) : [];

foreach (['fio', 'Телефон', 'email', 'birthdate', 'gender', 'languages', 'bio'] as $field) {
    if (!isset($values[$field]) && isset($_COOKIE['form_saved_' . $field])) {
        $cookieValue = $_COOKIE['form_saved_' . $field];
        $values[$field] = is_array(@unserialize($cookieValue)) ? unserialize($cookieValue) : $cookieValue;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Форма регистрации</h2>

<?php if (!empty($errors)): ?>
    <div class="error-message">
        <p>Пожалуйста, исправьте ошибки:</p>
        <ul>
            <?php foreach ($errors as $field => $msg): ?>
                <li><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="save_form.php" method="post">
  <label>ФИО:<br>
      <input type="text" name="fio" value="<?= htmlspecialchars($values['fio'] ?? '') ?>" class="<?= isset($errors['fio']) ? 'error' : '' ?>" required>
  </label><br><br>

  <label>Телефон:<br>
      <input type="tel" name="Телефон" value="<?= htmlspecialchars($values['Телефон'] ?? '') ?>" class="<?= isset($errors['Телефон']) ? 'error' : '' ?>" required>
  </label><br><br>

  <label>Email:<br>
      <input type="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'error' : '' ?>" required>
  </label><br><br>

  <label>Дата рождения:<br>
      <input type="date" name="birthdate" value="<?= htmlspecialchars($values['birthdate'] ?? '') ?>" class="<?= isset($errors['birthdate']) ? 'error' : '' ?>" required>
  </label><br><br>

  <label>Пол:<br>
      <input type="radio" name="gender" value="М" <?= (isset($values['gender']) && $values['gender'] === 'М') ? 'checked' : '' ?>> М
      <input type="radio" name="gender" value="Ж" <?= (isset($values['gender']) && $values['gender'] === 'Ж') ? 'checked' : '' ?>> Ж
  </label><br><br>

  <label>Любимый язык программирования:<br>
      <select name="languages[]" multiple class="<?= isset($errors['languages']) ? 'error' : '' ?>">
          <?php
          $all_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
          foreach ($all_languages as $lang) {
              $selected = (isset($values['languages']) && is_array($values['languages']) && in_array($lang, $values['languages'])) ? 'selected' : '';
              echo "<option value=\"$lang\" $selected>$lang</option>";
          }
          ?>
      </select>
  </label><br><br>

  <label>Биография:<br>
      <textarea name="bio" rows="5" cols="50" class="<?= isset($errors['bio']) ? 'error' : '' ?>"><?= htmlspecialchars($values['bio'] ?? '') ?></textarea>
  </label><br><br>

  <label>
      <input type="checkbox" name="contract" <?= isset($values['contract']) ? 'checked' : '' ?>>
      С контрактом ознакомлен(а)
  </label><br><br>

  <button type="submit">Сохранить</button>
</form>
</body>
</html>
