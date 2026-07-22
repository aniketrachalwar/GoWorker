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
            const role = card.getAttribute("data-role");
            signupRoleInput.value = role;

            // Toggle worker signup fields
            const isWorker = role === "worker";
            const container = document.getElementById("worker-fields-container");
            if (container) {
                if (isWorker) {
                    container.style.display = "grid";
                    container.querySelectorAll("select, input").forEach(input => {
                        input.removeAttribute("disabled");
                    });
                } else {
                    container.style.display = "none";
                    container.querySelectorAll("select, input").forEach(input => {
                        input.setAttribute("disabled", "true");
                    });
                }
            }
        });
        
        card.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                card.click();
            }
        });
    });

    // Searchable Profession Dropdown Setup
    const professions = [
        "Electrician", "Plumber", "Carpenter", "Painter", "Mason (Mistri)", "Construction Labour", 
        "Welder", "Fabricator", "Tile Fitter", "POP Worker", "Steel Worker", "Aluminium Worker", 
        "Cleaner", "House Cleaner", "Bathroom Cleaner", "Sofa Cleaner", "Water Tank Cleaner", 
        "Pest Control", "Gardener", "Driver", "Truck Driver", "Tempo Driver", "Loader", "Unloader", 
        "Helper Labour", "Security Guard", "Cook", "Babysitter", "Elder Care Assistant", "Mechanic", 
        "Bike Repair", "Car Washing", "Mobile Repair", "Laptop Repair", "Computer Repair", "TV Repair", 
        "Refrigerator Repair", "AC Technician", "Washing Machine Repair", "Microwave Repair", 
        "Water Purifier Repair", "Solar Panel Technician", "CCTV Installer", "Network Technician", 
        "Photographer", "Videographer", "DJ Service", "Beautician", "Hair Stylist", "Makeup Artist", 
        "Mehendi Artist"
    ];

    const professionSelect = document.getElementById("signup-profession");
    if (professionSelect) {
        professionSelect.innerHTML = '<option value="">Select Profession</option>';
        professions.sort().forEach(p => {
            const opt = document.createElement("option");
            opt.value = p;
            opt.textContent = p;
            professionSelect.appendChild(opt);
        });
    }

    const searchInput = document.getElementById("signup-profession-search");
    if (searchInput && professionSelect) {
        searchInput.addEventListener("input", () => {
            const query = searchInput.value.toLowerCase();
            const opts = professionSelect.querySelectorAll("option");
            opts.forEach(opt => {
                if (opt.value === "") return;
                const text = opt.textContent.toLowerCase();
                if (text.includes(query)) {
                    opt.style.display = "";
                } else {
                    opt.style.display = "none";
                }
            });
            const activeOpt = professionSelect.options[professionSelect.selectedIndex];
            if (activeOpt && activeOpt.value !== "" && activeOpt.style.display === "none") {
                professionSelect.value = "";
            }
        });
    }

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
    const signupForm = document.getElementById("signup-form");
    if (signupForm) {
        signupForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            
            let isValid = true;
            const fullName = document.getElementById("signup-fullname").value.trim();
            const email = document.getElementById("signup-email").value.trim();
            const phone = document.getElementById("signup-phone").value.trim();
            const locationVal = document.getElementById("signup-location").value.trim();
            const password = document.getElementById("signup-password").value;
            const confirmPassword = document.getElementById("signup-confirm-password").value;
            const role = document.getElementById("signup-role").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                isValid = false;
                return;
            }

            if (!/^[0-9]{10}$/.test(phone.replace(/[^0-9]/g, ""))) {
                alert("Please enter a valid 10-digit mobile number!");
                isValid = false;
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Please enter a valid email address!");
                isValid = false;
                return;
            }

            if (isValid) {
                const submitBtn = signupForm.querySelector("button[type='submit']");
                if (submitBtn) {
                    submitBtn.classList.add("loading");
                    submitBtn.disabled = true;
                }

                // Handle worker fields
                let profession = "";
                let category = "";
                let experience = "";
                let avatarBase64 = "";
                let workerId = "";
                let verificationCode = "";
                let joiningDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

                if (role === "worker") {
                    profession = document.getElementById("signup-profession").value;
                    category = document.getElementById("signup-category").value;
                    experience = document.getElementById("signup-experience").value;
                    
                    const avatarFile = document.getElementById("signup-avatar").files[0];
                    if (avatarFile) {
                        try {
                            avatarBase64 = await new Promise((resolve, reject) => {
                                const reader = new FileReader();
                                reader.readAsDataURL(avatarFile);
                                reader.onload = () => resolve(reader.result);
                                reader.onerror = err => reject(err);
                            });
                        } catch (err) {
                            console.error("Error reading profile photo:", err);
                        }
                    }

                    // Generate Worker ID index
                    let workerIndex = 1;
                    if (useFirebase) {
                        const snapshot = await firebase.firestore().collection("users").where("user_type", "==", "worker").get();
                        workerIndex = snapshot.size + 1;
                    } else {
                        const localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
                        const workers = localUsers.filter(u => u.user_type === "worker");
                        workerIndex = workers.length + 1;
                    }
                    workerId = "GW-2026-" + String(workerIndex).padStart(6, '0');
                    verificationCode = String(Math.floor(100000 + Math.random() * 900000));
                }

                if (useFirebase) {
                    firebase.auth().createUserWithEmailAndPassword(email, password)
                        .then((userCredential) => {
                            const user = userCredential.user;
                            const userData = {
                                uid: user.uid,
                                full_name: fullName,
                                email: email,
                                phone: phone,
                                location: locationVal,
                                user_type: role
                            };

                            if (role === "worker") {
                                userData.profession = profession;
                                userData.category = category;
                                userData.experience = experience;
                                userData.avatar = avatarBase64;
                                userData.worker_id = workerId;
                                userData.verification_status = "Pending Verification";
                                userData.verification_code = verificationCode;
                                userData.joining_date = joiningDate;
                            }

                            return firebase.firestore().collection("users").doc(user.uid).set(userData);
                        })
                        .then(() => {
                            const sessionData = {
                                uid: firebase.auth().currentUser.uid,
                                full_name: fullName,
                                email: email,
                                user_type: role
                            };
                            if (role === "worker") {
                                sessionData.profession = profession;
                                sessionData.category = category;
                                sessionData.experience = experience;
                                sessionData.avatar = avatarBase64;
                                sessionData.worker_id = workerId;
                                sessionData.verification_status = "Pending Verification";
                                sessionData.verification_code = verificationCode;
                                sessionData.joining_date = joiningDate;
                            }
                            localStorage.setItem("user_session", JSON.stringify(sessionData));
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            alert("Account created successfully! Redirecting...");
                            window.location.href = "profile.html";
                        })
                        .catch((error) => {
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            alert("Signup Error: " + error.message);
                        });
                } else {
                    // LocalStorage Mock
                    setTimeout(() => {
                        let localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
                        if (localUsers.find(u => u.email === email)) {
                            alert("Email already registered!");
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            return;
                        }
                        const newUser = {
                            uid: "local_" + Date.now(),
                            full_name: fullName,
                            email: email,
                            phone: phone,
                            location: locationVal,
                            user_type: role
                        };

                        if (role === "worker") {
                            newUser.profession = profession;
                            newUser.category = category;
                            newUser.experience = experience;
                            newUser.avatar = avatarBase64;
                            newUser.worker_id = workerId;
                            newUser.verification_status = "Pending Verification";
                            newUser.verification_code = verificationCode;
                            newUser.joining_date = joiningDate;
                        }

                        localUsers.push(newUser);
                        localStorage.setItem("local_users", JSON.stringify(localUsers));
                        
                        const sessionData = {
                            uid: newUser.uid,
                            full_name: newUser.full_name,
                            email: newUser.email,
                            user_type: newUser.user_type
                        };
                        if (role === "worker") {
                            sessionData.profession = profession;
                            sessionData.category = category;
                            sessionData.experience = experience;
                            sessionData.avatar = avatarBase64;
                            sessionData.worker_id = workerId;
                            sessionData.verification_status = "Pending Verification";
                            sessionData.verification_code = verificationCode;
                            sessionData.joining_date = joiningDate;
                        }
                        
                        localStorage.setItem("user_session", JSON.stringify(sessionData));
                        if (submitBtn) {
                            submitBtn.classList.remove("loading");
                            submitBtn.disabled = false;
                        }
                        alert("Account created successfully! Redirecting...");
                        window.location.href = "profile.html";
                    }, 1200);
                }
            }
        });
    }

    const loginForm = document.getElementById("login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault();
            
            const email = document.getElementById("login-email").value.trim();
            const password = document.getElementById("login-password").value;
            const role = document.getElementById("selected-role").value;
            let isValid = true;

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Please enter a valid email address!");
                isValid = false;
                return;
            }

            if (isValid) {
                const submitBtn = loginForm.querySelector("button[type='submit']");
                if (submitBtn) {
                    submitBtn.classList.add("loading");
                    submitBtn.disabled = true;
                }

                if (useFirebase) {
                    firebase.auth().signInWithEmailAndPassword(email, password)
                        .then((userCredential) => {
                            const user = userCredential.user;
                            return firebase.firestore().collection("users").doc(user.uid).get();
                        })
                        .then((doc) => {
                            let name = "User";
                            let user_type = role;
                            if (doc.exists) {
                                const data = doc.data();
                                name = data.full_name || name;
                                user_type = data.user_type || user_type;
                            }
                            localStorage.setItem("user_session", JSON.stringify({
                                uid: firebase.auth().currentUser.uid,
                                full_name: name,
                                email: email,
                                user_type: user_type
                            }));
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            alert("Welcome back! Redirecting...");
                            window.location.href = "profile.html";
                        })
                        .catch((error) => {
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            alert("Login Error: " + error.message);
                        });
                } else {
                    // LocalStorage Mock
                    setTimeout(() => {
                        let localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
                        if (localUsers.length === 0) {
                            localUsers = [
                                { uid: "local_1", full_name: "Aniket Rachalwar", email: "aniket@example.com", phone: "9876543210", location: "Pune", user_type: "customer" },
                                { 
                                    uid: "local_2", 
                                    full_name: "Ramesh Kumar", 
                                    email: "ramesh@example.com", 
                                    phone: "9876543211", 
                                    location: "Pune", 
                                    user_type: "worker",
                                    profession: "Electrician",
                                    category: "Skilled Trades",
                                    experience: "5",
                                    avatar: "", 
                                    worker_id: "GW-2026-000001",
                                    verification_status: "Verified",
                                    verification_code: "839402",
                                    joining_date: "January 15, 2026"
                                }
                            ];
                            localStorage.setItem("local_users", JSON.stringify(localUsers));
                        }
                        const user = localUsers.find(u => u.email === email);
                        if (user) {
                            // Self-repair: Ensure Ramesh or other workers have a valid worker_id
                            if (user.user_type === "worker" && !user.worker_id) {
                                const workers = localUsers.filter(u => u.user_type === "worker" && u.worker_id);
                                const idx = workers.length + 1;
                                user.worker_id = "GW-2026-" + String(idx).padStart(6, '0');
                                user.profession = user.profession || "Electrician";
                                user.category = user.category || "Skilled Trades";
                                user.experience = user.experience || "5";
                                user.joining_date = user.joining_date || "January 15, 2026";
                                user.verification_status = user.verification_status || "Verified";
                                user.verification_code = user.verification_code || "839402";
                                localStorage.setItem("local_users", JSON.stringify(localUsers));
                            }

                            const sessionData = {
                                uid: user.uid,
                                full_name: user.full_name,
                                email: user.email,
                                user_type: user.user_type
                            };
                            if (user.user_type === "worker") {
                                sessionData.profession = user.profession;
                                sessionData.category = user.category;
                                sessionData.experience = user.experience;
                                sessionData.avatar = user.avatar;
                                sessionData.worker_id = user.worker_id;
                                sessionData.verification_status = user.verification_status;
                                sessionData.verification_code = user.verification_code;
                                sessionData.joining_date = user.joining_date;
                            }
                            localStorage.setItem("user_session", JSON.stringify(sessionData));
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            alert("Welcome back! Redirecting...");
                            window.location.href = "profile.html";
                        } else {
                            if (submitBtn) {
                                submitBtn.classList.remove("loading");
                                submitBtn.disabled = false;
                            }
                            alert("Invalid email or password. Use seed account: 'aniket@example.com' or register a new one.");
                        }
                    }, 1200);
                }
            }
        });
    }

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
