document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('mainForm');
  const responseDiv = document.getElementById('response');

  if (!form || !window.fetch) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
      if (key === 'languages[]') {
        if (!data['languages']) data['languages'] = [];
        data['languages'].push(value);
      } else {
        data[key] = value;
      }
    });

    const isUpdate = document.cookie.includes('user_id=');
    const method = isUpdate ? 'PUT' : 'POST';

    try {
      const response = await fetch('/project_api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-HTTP-Method-Override': method
        },
        body: JSON.stringify(data),
        credentials: 'include'
      });

      const result = await response.json();
      
      if (!response.ok) {
        if (response.status === 422) {
          let errorMsg = 'Ошибки валидации:\n';
          for (const field in result.errors) {
            errorMsg += `${field}: ${result.errors[field]}\n`;
          }
          throw new Error(errorMsg);
        }
        throw new Error(`Ошибка сервера: ${result.error || 'Неизвестная ошибка'}`);
      }

      responseDiv.innerHTML = `<div class="alert alert-success">
        ${result.message || 'Успешно!'}
        ${result.login ? `<br>Логин: ${result.login}` : ''}
        ${result.password ? `<br>Пароль: ${result.password}` : ''}
      </div>`;
      
      if (result.login && result.password) {
        alert(`Ваши учетные данные:\nЛогин: ${result.login}\nПароль: ${result.password}`);
      }
    } catch (err) {
      responseDiv.innerHTML = `<div class="alert alert-danger">
        Ошибка: ${err.message}
      </div>`;
      console.error('Error:', err);
    }
  });
});