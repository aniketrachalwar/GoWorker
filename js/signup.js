/* js/signup.js */

document.addEventListener('DOMContentLoaded', () => {
    // --- Tab Switching Logic ---
    const customerTab = document.getElementById('tab-customer');
    const workerTab = document.getElementById('tab-worker');
    const roleInput = document.getElementById('user_type');

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
        
        // Sync initial state from hidden field if already set (e.g., after validation error reload)
        if (roleInput.value === 'worker') {
            workerTab.classList.add('active');
            customerTab.classList.remove('active');
        } else {
            customerTab.classList.add('active');
            workerTab.classList.remove('active');
        }
    }

    // --- Password Toggle Visibility Logic (Password Field) ---
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
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

    // --- Password Toggle Visibility Logic (Confirm Password Field) ---
    const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            
            const icon = toggleConfirmPassword.querySelector('i');
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

    // --- Sync Theme Toggle with Global Toggle in main.js ---
    const customThemeToggle = document.getElementById('signup-theme-toggle');
    const globalThemeToggle = document.getElementById('theme-toggle');

    if (customThemeToggle && globalThemeToggle) {
        const htmlElement = document.documentElement;
        
        function syncThemeIcon() {
            const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
            const icon = customThemeToggle.querySelector('i');
            if (icon) {
                if (currentTheme === 'dark') {
                    icon.className = 'fa-solid fa-sun';
                } else {
                    icon.className = 'fa-solid fa-moon';
                }
            }
        }

        syncThemeIcon();

        customThemeToggle.addEventListener('click', () => {
            globalThemeToggle.click();
            setTimeout(syncThemeIcon, 50);
        });

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'data-theme') {
                    syncThemeIcon();
                }
            });
        });
        observer.observe(htmlElement, { attributes: true });
    }
});
