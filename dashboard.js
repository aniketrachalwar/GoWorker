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

    // Verify session and load details
    const sessionStr = localStorage.getItem("user_session");
    if (!sessionStr) {
        alert("Please log in to access your dashboard.");
        window.location.href = "login.html";
        return;
    }
    const session = JSON.parse(sessionStr);
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

    function ensureWorkerID(user, callback) {
        if (user.user_type === "worker" && !user.worker_id) {
            let workerIndex = 1;
            if (useFirebase) {
                firebase.firestore().collection("users").where("user_type", "==", "worker").get()
                    .then(snapshot => {
                        const existingWorkers = snapshot.docs.filter(d => d.data().worker_id);
                        workerIndex = existingWorkers.length + 1;
                        const newWorkerId = "GW-2026-" + String(workerIndex).padStart(6, '0');
                        const updates = {
                            worker_id: newWorkerId,
                            verification_status: "Verified",
                            verification_code: String(Math.floor(100000 + Math.random() * 900000)),
                            joining_date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
                        };
                        firebase.firestore().collection("users").doc(user.uid).update(updates)
                            .then(() => {
                                Object.assign(user, updates);
                                const sess = JSON.parse(localStorage.getItem("user_session"));
                                Object.assign(sess, updates);
                                localStorage.setItem("user_session", JSON.stringify(sess));
                                callback(user);
                            });
                    });
            } else {
                const localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
                const existingWorkers = localUsers.filter(u => u.user_type === "worker" && u.worker_id);
                workerIndex = existingWorkers.length + 1;
                const newWorkerId = "GW-2026-" + String(workerIndex).padStart(6, '0');
                const updates = {
                    worker_id: newWorkerId,
                    verification_status: "Verified",
                    verification_code: "839402",
                    joining_date: "January 15, 2026"
                };
                const idx = localUsers.findIndex(u => u.uid === user.uid);
                if (idx !== -1) {
                    Object.assign(localUsers[idx], updates);
                    localStorage.setItem("local_users", JSON.stringify(localUsers));
                }
                Object.assign(user, updates);
                const sess = JSON.parse(localStorage.getItem("user_session"));
                Object.assign(sess, updates);
                localStorage.setItem("user_session", JSON.stringify(sess));
                callback(user);
            }
        } else {
            callback(user);
        }
    }

    // Load full user details dynamically
    if (useFirebase) {
        firebase.auth().onAuthStateChanged((user) => {
            if (user) {
                firebase.firestore().collection("users").doc(user.uid).get()
                    .then((doc) => {
                        if (doc.exists) {
                            ensureWorkerID(doc.data(), (updatedData) => {
                                populateDashboardInfo(updatedData);
                            });
                        } else {
                            ensureWorkerID(session, (updatedData) => {
                                populateDashboardInfo(updatedData);
                            });
                        }
                    });
            } else {
                ensureWorkerID(session, (updatedData) => {
                    populateDashboardInfo(updatedData);
                });
            }
        });
    } else {
        const localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
        const userData = localUsers.find(u => u.uid === session.uid) || session;
        ensureWorkerID(userData, (updatedData) => {
            populateDashboardInfo(updatedData);
        });
    }

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
