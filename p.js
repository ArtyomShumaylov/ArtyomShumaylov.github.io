document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const responseDiv = document.getElementById('response');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const consentCheckbox = form.querySelector('input[name="consent"]');
        if (!consentCheckbox.checked) {
            responseDiv.textContent = 'Вы должны согласиться на обработку персональных данных!';
            responseDiv.style.color = 'red';
            return;
        }

        const formData = new FormData(form);

        fetch('https://example.com/submit', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(() => {
            responseDiv.textContent = 'Заявка успешно отправлена!';
            responseDiv.style.color = 'green';
            form.reset();
        })
        .catch(() => {
            responseDiv.textContent = 'Произошла ошибка. Попробуйте ещё раз.';
            responseDiv.style.color = 'red';
        });
    });
});
