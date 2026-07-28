// Initialize Firebase
const firebaseConfig = {
    apiKey: "AIzaSyFakeKeyPlaceholderForNetlifyStaticDemo",
    authDomain: "goworker-demo.firebaseapp.com",
    projectId: "goworker-demo",
    storageBucket: "goworker-demo.appspot.com",
    messagingSenderId: "1234567890",
    appId: "1:1234567890:web:abcdef123456"
};

let useFirebase = false;

if (typeof firebase !== "undefined") {
    try {
        firebase.initializeApp(firebaseConfig);
        useFirebase = true;
    } catch (e) {
        console.warn("Firebase initialization failed, falling back to LocalStorage demo mode:", e);
    }
}

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
            window.location.href = "booking-history.html";
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

    // Verify session and load details from Vercel me API
    fetch('/api/auth/me')
        .then(res => {
            if (!res.ok) throw new Error("Not logged in");
            return res.json();
        })
        .then(data => {
            if (data.status !== 'success' || !data.user) {
                alert("Please log in to access your dashboard.");
                window.location.href = "login.html";
                return;
            }

            const session = data.user;
            const currentPage = window.location.pathname.split("/").pop();

            if (currentPage === "worker-dashboard.html" && session.user_type !== "worker") {
                alert("Access denied. Directing to Customer Dashboard.");
                window.location.href = "customer-dashboard.html";
                return;
            }
            if (currentPage === "customer-dashboard.html" && session.user_type !== "customer") {
                alert("Access denied. Directing to Worker Dashboard.");
                window.location.href = "worker-dashboard.html";
                return;
            }

            localStorage.setItem("user_session", JSON.stringify(session));
            populateDashboardInfo(session);
        })
        .catch(err => {
            console.error("Dashboard session error:", err);
            alert("Please log in to access your dashboard.");
            window.location.href = "login.html";
        });

    function populateDashboardInfo_stub() {}

    function populateDashboardInfo(data) {
        // Populate dashboard welcome title
        const welcomeHeading = document.querySelector(".dashboard-header h2") || document.querySelector(".dashboard-content h2") || document.querySelector(".dashboard-content h1");
        if (welcomeHeading) {
            const currentText = welcomeHeading.textContent;
            const greeting = currentText.includes("Morning") ? "Good Morning" : (currentText.includes("Welcome") ? "Welcome" : "Hello");
            welcomeHeading.textContent = `${greeting}, ${data.full_name || 'User'} 👋`;
        }

        // Handle Digital Identity Card for Worker Dashboard
        if (data.user_type === "worker") {
            const idValEl = document.getElementById("dashboard-worker-id");
            if (idValEl) {
                idValEl.textContent = data.worker_id || "GW-2026-000000";
            }
            const statusEl = document.getElementById("dashboard-verification-status");
            if (statusEl) {
                const status = data.verification_status || "Pending Verification";
                statusEl.textContent = status;
                if (status === "Verified") {
                    statusEl.style.background = "rgba(34, 197, 94, 0.1)";
                    statusEl.style.color = "var(--success)";
                } else if (status === "Rejected") {
                    statusEl.style.background = "rgba(239, 68, 68, 0.1)";
                    statusEl.style.color = "var(--danger)";
                } else {
                    statusEl.style.background = "rgba(245, 158, 11, 0.1)";
                    statusEl.style.color = "var(--warning)";
                }
            }
        }
    }
});
