// ========================================
// GOWORKER REGISTER PAGE LOGIC
// signup_page.js
// ========================================

// -------------------------------
// ACCOUNT TYPE SELECTION
// -------------------------------
const options = document.querySelectorAll(".option");

options.forEach(option => {
    option.addEventListener("click", () => {
        options.forEach(card => {
            card.classList.remove("active");
            card.setAttribute("aria-checked", "false");
        });
        option.classList.add("active");
        option.setAttribute("aria-checked", "true");
    });
    
    // Keybind support for radio button accessibility
    option.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            option.click();
        }
    });
});

// -------------------------------
// THEME TOGGLE
// -------------------------------
const themeBtn = document.querySelector(".theme-btn");

// Apply system theme preferences on load
if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) {
    document.body.classList.add("dark-mode");
    if (themeBtn) {
        themeBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
    }
}

if (themeBtn) {
    themeBtn.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
        if (document.body.classList.contains("dark-mode")) {
            themeBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
        } else {
            themeBtn.innerHTML = '<i class="fa-regular fa-sun"></i>';
        }
    });
}

// -------------------------------
// REAL LANGUAGE DROPDOWN
// -------------------------------
const dropdownBtn = document.querySelector(".language-dropdown-btn");
const dropdownMenu = document.querySelector(".language-dropdown-menu");
const selectedLangText = document.querySelector(".selected-lang");

if (dropdownBtn && dropdownMenu) {
    dropdownBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const expanded = dropdownBtn.getAttribute("aria-expanded") === "true";
        dropdownBtn.setAttribute("aria-expanded", !expanded);
        dropdownMenu.classList.toggle("show");
    });

    // Close language list when clicking anywhere else
    document.addEventListener("click", () => {
        dropdownBtn.setAttribute("aria-expanded", "false");
        dropdownMenu.classList.remove("show");
    });

    // Handle language selection options
    const dropdownOptions = dropdownMenu.querySelectorAll("a");
    dropdownOptions.forEach(opt => {
        opt.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            dropdownOptions.forEach(o => o.classList.remove("active"));
            opt.classList.add("active");
            
            // Clean up name by removing emoji flag
            const langName = opt.textContent.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/, "");
            if (selectedLangText) {
                selectedLangText.textContent = langName.trim();
            }
            
            dropdownBtn.setAttribute("aria-expanded", "false");
            dropdownMenu.classList.remove("show");
            
            console.log(`Language changed to: ${opt.getAttribute("data-lang")}`);
        });
    });
}

// -------------------------------
// FIREBASE AUTHENTICATION HOOK
// -------------------------------
// Replace/configure Firebase credentials when deploying to production:
/*
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
};
firebase.initializeApp(firebaseConfig);
*/

function handleGoogleSignIn() {
    if (typeof firebase !== 'undefined' && firebase.auth) {
        const provider = new firebase.auth.GoogleAuthProvider();
        firebase.auth().signInWithPopup(provider)
            .then((result) => {
                const user = result.user;
                console.log("Firebase Auth Success:", user);
                alert("Sign-in successful! Welcome " + user.displayName);
                window.location.href = "Index.html"; 
            })
            .catch((error) => {
                console.error("Firebase Auth Error:", error);
                alert("Authentication Failed: " + error.message);
            });
    } else {
        alert("Google authentication initiated! (Firebase configuration script is ready to be connected in signup_page.js)");
    }
}

// Attach social buttons click events
const googleBtn = document.querySelector(".google-btn");
if (googleBtn) {
    googleBtn.addEventListener("click", () => {
        handleGoogleSignIn();
    });
}

const facebookBtn = document.querySelector(".facebook-btn");
if (facebookBtn) {
    facebookBtn.addEventListener("click", () => {
        alert("Facebook Authentication Coming Soon!");
    });
}

// -------------------------------
// BUTTON & CARDS RIPPLE EFFECT
// -------------------------------
const buttons = document.querySelectorAll("button, .option");

buttons.forEach(btn => {
    btn.addEventListener("click", function(e) {
        const circle = document.createElement("span");
        const diameter = Math.max(this.clientWidth, this.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        
        const rect = this.getBoundingClientRect();
        circle.style.left = `${e.clientX - rect.left - radius}px`;
        circle.style.top = `${e.clientY - rect.top - radius}px`;
        circle.classList.add("ripple");

        const existingRipple = this.querySelector(".ripple");
        if (existingRipple) {
            existingRipple.remove();
        }

        this.appendChild(circle);
    });
});



// -------------------------------
// SCROLL REVEAL ANIMATION
// -------------------------------
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("show");
            observer.unobserve(entry.target); // Animate only once
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll(".feature, .option, .testimonial, .security").forEach(el => {
    el.classList.add("hidden");
    observer.observe(el);
});

// -------------------------------
// PAGE LOAD COMPLETE FLAG
// -------------------------------
window.addEventListener("load", () => {
    document.body.classList.add("loaded");
});
