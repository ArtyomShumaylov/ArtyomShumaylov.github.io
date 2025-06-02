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

      if (!response.ok) {
        throw new Error('Network response was not ok');
      }

      const result = await response.json();
      responseDiv.innerHTML = `<div class="alert alert-success">
        <pre>${JSON.stringify(result, null, 2)}</pre>
      </div>`;
      
      if (result.login && result.password) {
        alert(`Ваши учетные данные:\nЛогин: ${result.login}\nПароль: ${result.password}`);
      }
    } catch (err) {
      responseDiv.innerHTML = `<div class="alert alert-danger">
        Ошибка при отправке формы: ${err.message}
      </div>`;
      console.error('Error:', err);
    }
  });
});