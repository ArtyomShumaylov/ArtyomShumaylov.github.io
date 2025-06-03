document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('mainForm');
  const responseDiv = document.getElementById('response');

  if (!form || !window.fetch) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    try {
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

      const response = await fetch('./project_api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-HTTP-Method-Override': method
        },
        body: JSON.stringify(data),
        credentials: 'include'
      });

      // Проверяем Content-Type перед парсингом
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        throw new Error(`Ожидался JSON, получено: ${text.substring(0, 100)}`);
      }

      const result = await response.json();

    if (!response.ok) {
  // Если есть ошибки - показываются в форме
  if (result.errors) {
    document.querySelectorAll('.error-message').forEach(el => el.remove());

    for (const [field, message] of Object.entries(result.errors)) {
      const input = form.querySelector(`[name="${field}"]`);
      if (input) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '14px';
        errorDiv.textContent = message;
        input.parentElement.appendChild(errorDiv);
      }
    }
    return; 
  }

  throw new Error(result.message || `HTTP error! status: ${response.status}`);
}

      responseDiv.innerHTML = `<div class="alert alert-success">
        ${result.message || 'Успешно!'}
        ${result.login ? `<br>Логин: ${result.login}` : ''}
        ${result.password ? `<br>Пароль: ${result.password}` : ''}
      </div>`;

    } catch (err) {
      responseDiv.innerHTML = `<div class="alert alert-danger">
        Ошибка: ${err.message}
      </div>`;
      console.error('Error details:', err);
    }
  });
});