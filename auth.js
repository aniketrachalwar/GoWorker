// GoWorker Authentication Interactions
// auth.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. ROLE SELECTION TOGGLE (Login Page)
    const roleBtns = document.querySelectorAll(".role-selector .role-btn");
    const roleInput = document.getElementById("selected-role") || {}; // Hidden input fallback
    
    roleBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            roleBtns.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            roleInput.value = btn.getAttribute("data-role");
        });
    });

    // 2. ACCOUNT TYPE CARD SELECTION (Signup Page)
    const optionCards = document.querySelectorAll(".account-options .option");
    const signupRoleInput = document.getElementById("signup-role") || {};

    optionCards.forEach(card => {
        card.addEventListener("click", () => {
            optionCards.forEach(c => {
                c.classList.remove("active");
                c.setAttribute("aria-checked", "false");
            });
            card.classList.add("active");
            card.setAttribute("aria-checked", "true");
            signupRoleInput.value = card.getAttribute("data-role");
        });
        
        card.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                card.click();
            }
        });
    });

    // 3. PASSWORD VISIBILITY TOGGLE (Show/Hide)
    const passToggleBtns = document.querySelectorAll(".pass-toggle-btn");
    passToggleBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const inputWrapper = btn.closest(".input-wrapper");
            const passInput = inputWrapper.querySelector("input");
            const icon = btn.querySelector("i");
            
            if (passInput.type === "password") {
                passInput.type = "text";
                icon.className = "fa-solid fa-eye-slash";
            } else {
                passInput.type = "password";
                icon.className = "fa-solid fa-eye";
            }
        });
    });

    // 4. PASSWORD STRENGTH METER (Signup Page)
    const passwordInput = document.getElementById("signup-password");
    const strengthMeter = document.querySelector(".password-strength-meter");
    const strengthBar = document.querySelector(".password-strength-bar");

    if (passwordInput && strengthMeter && strengthBar) {
        passwordInput.addEventListener("input", () => {
            const val = passwordInput.value;
            if (val.length === 0) {
                strengthMeter.style.display = "none";
                return;
            }
            strengthMeter.style.display = "block";
            
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            
            strengthBar.className = "password-strength-bar"; // reset classes
            
            if (score <= 2) {
                strengthBar.classList.add("strength-weak");
            } else if (score <= 4) {
                strengthBar.classList.add("strength-medium");
            } else {
                strengthBar.classList.add("strength-strong");
            }
        });
    }

    // 5. INPUT VALIDATION & FORM SUBMISSION
    const authForms = document.querySelectorAll(".auth-card form");
    authForms.forEach(form => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            
            // Perform active form validation
            let isValid = true;
            
            // Confirm Password check
            const pass = form.querySelector("#signup-password");
            const confirmPass = form.querySelector("#signup-confirm-password");
            if (pass && confirmPass && pass.value !== confirmPass.value) {
                alert("Passwords do not match!");
                isValid = false;
            }
            
            // Phone validation
            const phone = form.querySelector("#signup-phone");
            if (phone && phone.value && !/^[0-9]{10}$/.test(phone.value.replace(/[^0-9]/g, ""))) {
                alert("Please enter a valid 10-digit mobile number!");
                isValid = false;
            }
            
            // Email validation
            const email = form.querySelector("input[type='email']");
            if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                alert("Please enter a valid email address!");
                isValid = false;
            }

            if (isValid) {
                const submitBtn = form.querySelector("button[type='submit']");
                if (submitBtn) {
                    submitBtn.classList.add("loading");
                    
                    // Simulate loading network delay
                    setTimeout(() => {
                        submitBtn.classList.remove("loading");
                        alert("Account verified successfully! Redirecting...");
                        window.location.href = "index.html";
                    }, 1500);
                }
            }
        });
    });

    // 6. RIPPLE CLICK EFFECT
    const rippleButtons = document.querySelectorAll(".btn-primary-auth, .btn-google-auth, .option");
    rippleButtons.forEach(btn => {
        btn.addEventListener("click", function(e) {
            const ripple = document.createElement("span");
            const rect = this.getBoundingClientRect();
            const diameter = Math.max(rect.width, rect.height);
            const radius = diameter / 2;
            
            ripple.style.width = ripple.style.height = `${diameter}px`;
            ripple.style.left = `${e.clientX - rect.left - radius}px`;
            ripple.style.top = `${e.clientY - rect.top - radius}px`;
            ripple.classList.add("ripple");
            
            const existingRipple = this.querySelector(".ripple");
            if (existingRipple) {
                existingRipple.remove();
            }
            
            this.appendChild(ripple);
        });
    });

    // 7. AUTO-DETECT BROWSER DEFAULT LANGUAGE
    const dropdownBtn = document.querySelector(".language-dropdown-btn");
    const dropdownMenu = document.querySelector(".language-dropdown-menu");
    const selectedLangText = document.querySelector(".selected-lang");

    if (dropdownBtn && dropdownMenu) {
        // Toggle language dropdown menu visibility
        dropdownBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const expanded = dropdownBtn.getAttribute("aria-expanded") === "true";
            dropdownBtn.setAttribute("aria-expanded", !expanded);
            dropdownMenu.classList.toggle("show");
        });

        document.addEventListener("click", () => {
            dropdownBtn.setAttribute("aria-expanded", "false");
            dropdownMenu.classList.remove("show");
        });

        const dropdownOptions = dropdownMenu.querySelectorAll("a");
        dropdownOptions.forEach(opt => {
            opt.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropdownOptions.forEach(o => o.classList.remove("active"));
                opt.classList.add("active");
                
                const langName = opt.textContent.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/, "");
                if (selectedLangText) {
                    selectedLangText.textContent = langName.trim();
                }
                dropdownBtn.setAttribute("aria-expanded", "false");
                dropdownMenu.classList.remove("show");
            });
        });

        // Detect default user browser language on first load
        const userLang = navigator.language || navigator.userLanguage;
        if (userLang) {
            const langPrefix = userLang.substring(0, 2).toLowerCase();
            const matchingOpt = dropdownMenu.querySelector(`a[data-lang="${langPrefix}"]`);
            if (matchingOpt) {
                dropdownOptions.forEach(o => o.classList.remove("active"));
                matchingOpt.classList.add("active");
                const langName = matchingOpt.textContent.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/, "");
                if (selectedLangText) {
                    selectedLangText.textContent = langName.trim();
                }
            }
        }
    }
});
