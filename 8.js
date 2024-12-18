const openFormButton = document.getElementById('openFormButton');
const formOverlay = document.getElementById('formOverlay');
const feedbackForm = document.getElementById('feedbackForm');

function togglePopup(state) {
    if (state) {
        formOverlay.style.display = 'flex';
        history.pushState({ formVisible: true }, '', '#feedback-form');
    } else {
        formOverlay.style.display = 'none';
        if (location.hash === '#feedback-form') history.back();
    }
}

openFormButton.addEventListener('click', () => {
    restoreFormData();
    togglePopup(true);
});

formOverlay.addEventListener('click', (e) => {
    if (e.target === formOverlay) togglePopup(false);
});

window.addEventListener('popstate', (event) => {
    if (!event.state || !event.state.formVisible) {
        formOverlay.style.display = 'none';
    }
});

feedbackForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        organization: document.getElementById('organization').value,
        message: document.getElementById('message').value,
    };

    try {
        const response = await fetch('https://formcarry.com/s/FbtsF_FFZji', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        });
        if (response.ok) {
            alert('Сообщение отослано успешно!');
            feedbackForm.reset();
            localStorage.removeItem('feedbackFormData');
            togglePopup(false);
        } else {
            throw new Error('Failed to send message');
        }
    } catch (error) {
        alert('Ошибка: ' + error.message);
    }
});

function saveFormData() {
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        organization: document.getElementById('organization').value,
        message: document.getElementById('message').value,
    };
    localStorage.setItem('feedbackFormData', JSON.stringify(formData));
}

function restoreFormData() {
    const savedData = localStorage.getItem('feedbackFormData');
    if (savedData) {
        const formData = JSON.parse(savedData);
        document.getElementById('name').value = formData.name || '';
        document.getElementById('email').value = formData.email || '';
        document.getElementById('phone').value = formData.phone || '';
        document.getElementById('organization').value = formData.organization || '';
        document.getElementById('message').value = formData.message || '';
    }
}

window.addEventListener('beforeunload', saveFormData);
