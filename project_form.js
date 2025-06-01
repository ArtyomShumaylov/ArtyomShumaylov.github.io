document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('projectForm');
  const responseDiv = document.getElementById('project_response');

  if (!form || !window.fetch) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
      if (key in data) {
        if (Array.isArray(data[key])) {
          data[key].push(value);
        } else {
          data[key] = [data[key], value];
        }
      } else {
        data[key] = value;
      }
    });

    const method = form.hasAttribute('data-update') ? 'PUT' : 'POST';

    try {
      const res = await fetch('project_api.php', {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });

      const result = await res.json();
      responseDiv.innerHTML = `<pre>${JSON.stringify(result, null, 2)}</pre>`;
    } catch (err) {
      responseDiv.textContent = 'Ошибка при отправке формы.';
    }
  });
});
