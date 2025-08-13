import './bootstrap';
import axios from 'axios';

const token = localStorage.getItem('auth_token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

document.addEventListener('DOMContentLoaded', function () {
    if(!localStorage.getItem('auth_token')){
        window.location.href = '/login';
    }
});