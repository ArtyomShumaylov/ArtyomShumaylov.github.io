<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Форма регистрации</title>
  <link rel="stylesheet" href="project_style.css">
</head>
<body>
  <div class="container">
    <?php if ($user): ?>
      <p>Вы авторизованы как <?= htmlspecialchars($user['login']) ?></p>
    <?php endif; ?>
    <form id="projectForm" method="POST" action="project_save_form.php">
      <input type="text" name="fio" placeholder="ФИО" required value="<?= htmlspecialchars($values['fio'] ?? '') ?>">
      <?= $errors['fio'] ?? '' ?><br>

      <input type="tel" name="phone" placeholder="Телефон" required value="<?= htmlspecialchars($values['phone'] ?? '') ?>">
      <?= $errors['phone'] ?? '' ?><br>

      <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($values['email'] ?? '') ?>">
      <?= $errors['email'] ?? '' ?><br>

      <input type="date" name="birthdate" required value="<?= htmlspecialchars($values['birthdate'] ?? '') ?>">
      <?= $errors['birthdate'] ?? '' ?><br>

      <label><input type="radio" name="gender" value="М" <?= (isset($values['gender']) && $values['gender'] === 'М') ? 'checked' : '' ?>> Мужской</label>
      <label><input type="radio" name="gender" value="Ж" <?= (isset($values['gender']) && $values['gender'] === 'Ж') ? 'checked' : '' ?>> Женский</label>
      <?= $errors['gender'] ?? '' ?><br>

      <label>Любимые ЯП:</label><br>
      <select name="languages[]" multiple required>
        <?php
        $all_languages = ['Pascal','C','C++','JavaScript','PHP','Python','Java','Haskel','Clojure','Prolog','Scala','Go'];
        foreach ($all_languages as $lang) {
          $selected = (isset($values['languages']) && in_array($lang, $values['languages'])) ? 'selected' : '';
          echo "<option value=\"$lang\" $selected>$lang</option>";
        }
        ?>
      </select>
      <?= $errors['languages'] ?? '' ?><br>

      <textarea name="bio" placeholder="Биография"><?= htmlspecialchars($values['bio'] ?? '') ?></textarea>
      <?= $errors['bio'] ?? '' ?><br>

      <label><input type="checkbox" name="contract" required> С контрактом ознакомлен(а)</label>
      <?= $errors['contract'] ?? '' ?><br>

      <button type="submit">Сохранить</button>
    </form>

    <div id="project_response"></div>
  </div>

  <script src="project_form.js"></script>
</body>
</html>
