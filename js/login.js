/* js/login.js */

document.addEventListener('DOMContentLoaded', () => {
    // --- Tab Switching Logic ---
    const customerTab = document.getElementById('tab-customer');
    const workerTab = document.getElementById('tab-worker');
    const roleInput = document.getElementById('user_role');

    if (customerTab && workerTab && roleInput) {
        customerTab.addEventListener('click', () => {
            customerTab.classList.add('active');
            workerTab.classList.remove('active');
            roleInput.value = 'customer';
        });

        workerTab.addEventListener('click', () => {
            workerTab.classList.add('active');
            customerTab.classList.remove('active');
            roleInput.value = 'worker';
        });
    }

    // --- Password Toggle Visibility Logic ---
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the eye / eye-slash icon
            const icon = togglePassword.querySelector('i');
            if (icon) {
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }
});
