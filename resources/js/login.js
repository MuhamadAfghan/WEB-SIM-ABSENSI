import axios from "axios";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("login-form");
    const alertBox = document.getElementById("alert-box");
    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const response = await axios.post("/api/login/admin", formData);
                if (response.data.status == "success") {
                    // Save token to localStorage as backup
                    if (response.data.data && response.data.data.token) {
                        localStorage.setItem('auth_token', response.data.data.token);
                        sessionStorage.setItem('auth_token', response.data.data.token);
                        console.log('Token saved to localStorage and sessionStorage');
                    }
                    window.location.href = "/dashboard";
                } else {
                    alertBox.textContent =
                        response.data.message || "Login failed";
                    alertBox.classList.remove("hidden");
                }
            } catch (err) {
                alertBox.textContent =
                    err.response?.data?.message || "Login failed";
                alertBox.classList.remove("hidden");
            }
        });
    }
});
