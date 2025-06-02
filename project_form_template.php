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
    
    <form id="mainForm" method="POST" action="project_api.php">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      
      <input type="text" name="fio" placeholder="ФИО" required value="<?= htmlspecialchars($values['fio'] ?? '') ?>">
      <?= isset($errors['fio']) ? '<span class="error">'.htmlspecialchars($errors['fio']).'</span>' : '' ?><br>

      <input type="tel" name="phone" placeholder="Телефон" required value="<?= htmlspecialchars($values['phone'] ?? '') ?>">
      <?= isset($errors['phone']) ? '<span class="error">'.htmlspecialchars($errors['phone']).'</span>' : '' ?><br>

      <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($values['email'] ?? '') ?>">
      <?= isset($errors['email']) ? '<span class="error">'.htmlspecialchars($errors['email']).'</span>' : '' ?><br>

      <input type="date" name="birthdate" required value="<?= htmlspecialchars($values['birthdate'] ?? '') ?>">
      <?= isset($errors['birthdate']) ? '<span class="error">'.htmlspecialchars($errors['birthdate']).'</span>' : '' ?><br>

      <label><input type="radio" name="gender" value="М" <?= (isset($values['gender']) && $values['gender'] === 'М') ? 'checked' : '' ?>> Мужской</label>
      <label><input type="radio" name="gender" value="Ж" <?= (isset($values['gender']) && $values['gender'] === 'Ж') ? 'checked' : '' ?>> Женский</label>
      <?= isset($errors['gender']) ? '<span class="error">'.htmlspecialchars($errors['gender']).'</span>' : '' ?><br>

      <label>Любимые ЯП:</label><br>
      <select name="languages[]" multiple required>
        <?php
        $all_languages = ['Pascal','C','C++','JavaScript','PHP','Python','Java','Haskel','Clojure','Prolog','Scala','Go'];
        foreach ($all_languages as $lang) {
          $selected = (isset($values['languages']) && in_array($lang, $values['languages'])) ? 'selected' : '';
          echo "<option value=\"".htmlspecialchars($lang)."\" $selected>".htmlspecialchars($lang)."</option>";
        }
        ?>
      </select>
      <?= isset($errors['languages']) ? '<span class="error">'.htmlspecialchars($errors['languages']).'</span>' : '' ?><br>

      <textarea name="bio" placeholder="Биография"><?= htmlspecialchars($values['bio'] ?? '') ?></textarea>
      <?= isset($errors['bio']) ? '<span class="error">'.htmlspecialchars($errors['bio']).'</span>' : '' ?><br>

      <label>
        <input type="checkbox" name="contract" value="on" <?= isset($values['contract']) ? 'checked' : '' ?> required>
        С контрактом ознакомлен(а)
      </label>
      <?= isset($errors['contract']) ? '<span class="error">'.htmlspecialchars($errors['contract']).'</span>' : '' ?><br>

      <button type="submit">Сохранить</button>
    </form>

    <div id="response"></div>
  </div>

  <script src="project_form.js"></script>
</body>
</html>