// Hard-coded credentials
const ADMIN_USERNAME = 'apploqic';
const ADMIN_PASSWORD = 'apploqic';

// Get form elements
const loginForm = document.getElementById('loginForm');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const errorAlert = document.getElementById('errorAlert');
const errorMessage = document.getElementById('errorMessage');

// Handle form submission
loginForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const username = usernameInput.value.trim();
    const password = passwordInput.value;

    // Validate credentials
    if (username === ADMIN_USERNAME && password === ADMIN_PASSWORD) {
        // Store login status in sessionStorage
        sessionStorage.setItem('admin_logged_in', 'true');
        sessionStorage.setItem('admin_username', username);

        // Redirect to admin dashboard
        window.location.href = 'admin_index.php';
    } else {
        // Show error message
        errorMessage.textContent = 'Invalid username or password';
        errorAlert.classList.remove('d-none');

        // Clear password field
        passwordInput.value = '';
        passwordInput.focus();

        // Hide error after 3 seconds
        setTimeout(() => {
            errorAlert.classList.add('d-none');
        }, 3000);
    }
});

// Hide error when user starts typing
usernameInput.addEventListener('input', () => {
    errorAlert.classList.add('d-none');
});

passwordInput.addEventListener('input', () => {
    errorAlert.classList.add('d-none');
});