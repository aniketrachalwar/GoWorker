// GoWorker Dashboard Interactions
// dashboard.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. SIDEBAR ACTIVE LINKS
    const sidebarLinks = document.querySelectorAll(".sidebar-link");
    sidebarLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            // If it points to a mockup page, let it naturally navigate
            const href = link.getAttribute("href");
            if (href && href !== "#") return;
            
            e.preventDefault();
            sidebarLinks.forEach(l => l.classList.remove("active"));
            link.classList.add("active");
        });
    });

    // 2. COUNTER ANIMATION SIMULATOR
    const counters = document.querySelectorAll(".stat-info h3");
    counters.forEach(counter => {
        const text = counter.textContent;
        const numericVal = parseInt(text.replace(/[^0-9]/g, ""));
        if (!isNaN(numericVal)) {
            let start = 0;
            const end = numericVal;
            const duration = 1000;
            const stepTime = Math.abs(Math.floor(duration / end));
            
            const timer = setInterval(() => {
                start += 1;
                if (start > end) {
                    counter.textContent = text; // Restore full string (with +, ₹, etc)
                    clearInterval(timer);
                } else {
                    counter.textContent = text.replace(/[0-9]+/, start);
                }
            }, stepTime);
        }
    });

    // 3. BOOKING TRACK ACTION ALERTS
    const trackBtns = document.querySelectorAll(".btn-track");
    trackBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            alert("Redirecting to real-time order tracking map...");
            window.location.href = "booking-history.php";
        });
    });

    // 4. THEME TOGGLING (Simulated using localStorage)
    const themeBtn = document.querySelector(".theme-btn");
    if (themeBtn) {
        themeBtn.addEventListener("click", () => {
            const currentTheme = document.documentElement.getAttribute("data-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";
            document.documentElement.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            
            const icon = themeBtn.querySelector("i");
            if (icon) {
                icon.className = newTheme === "dark" ? "fa-solid fa-sun" : "fa-solid fa-moon";
            }
        });
        
        // Restore preferences on load
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme) {
            document.documentElement.setAttribute("data-theme", savedTheme);
            const icon = themeBtn.querySelector("i");
            if (icon) {
                icon.className = savedTheme === "dark" ? "fa-solid fa-sun" : "fa-solid fa-moon";
            }
        }
    }
});
