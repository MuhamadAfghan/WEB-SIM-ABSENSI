import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    const alertBox = document.getElementById('alert-box');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                await axios.post('/api/login/admin', formData);
                window.location.href = '/dashboard';
            } catch (err) {
                alertBox.textContent = err.response?.data?.message || 'Login failed';
                alertBox.classList.remove('hidden');
            }
        });
    }
});