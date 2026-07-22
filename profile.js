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

// Canvas high-res PNG Exporter
function downloadCardAsPNG(data) {
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    const scale = 2; // High resolution
    canvas.width = 340 * scale;
    canvas.height = 215 * scale;
    ctx.scale(scale, scale);

    // Draw background linear gradient
    const grad = ctx.createLinearGradient(0, 0, 340, 215);
    grad.addColorStop(0, "#0D4DFF");
    grad.addColorStop(1, "#1e40af");
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 340, 215);

    // Draw brand header text
    ctx.fillStyle = "#ffffff";
    ctx.font = "bold 14px Poppins, sans-serif";
    ctx.fillText("GoWorker", 16, 32);

    // Draw verified pill badge
    ctx.fillStyle = "rgba(16, 185, 129, 0.25)";
    ctx.beginPath();
    ctx.arc(270, 24, 8, 0.5 * Math.PI, 1.5 * Math.PI);
    ctx.arc(315, 24, 8, 1.5 * Math.PI, 0.5 * Math.PI);
    ctx.closePath();
    ctx.fill();
    ctx.fillStyle = "#34d399";
    ctx.font = "bold 9px Poppins, sans-serif";
    ctx.fillText("✓ Verified", 270, 27);

    // Draw details fields labels
    ctx.fillStyle = "rgba(255, 255, 255, 0.65)";
    ctx.font = "500 10.5px Poppins, sans-serif";
    ctx.fillText("ID:", 96, 98);
    ctx.fillText("Role:", 96, 116);
    ctx.fillText("City:", 96, 134);
    ctx.fillText("Joined:", 96, 152);

    // Draw values
    ctx.fillStyle = "#ffffff";
    ctx.font = "600 10.5px Poppins, sans-serif";
    ctx.fillText(data.full_name || "Worker", 96, 78);
    ctx.fillText(data.worker_id || "GW-2026-000000", 146, 98);
    ctx.fillText(data.profession || "Trade", 146, 116);
    ctx.fillText(data.location || "City", 146, 134);
    ctx.fillText(data.joining_date || "July 22, 2026", 146, 152);

    // Draw status badge
    ctx.fillStyle = "rgba(255, 255, 255, 0.1)";
    ctx.beginPath();
    ctx.arc(245, 187, 10, 0.5 * Math.PI, 1.5 * Math.PI);
    ctx.arc(310, 187, 10, 1.5 * Math.PI, 0.5 * Math.PI);
    ctx.closePath();
    ctx.fill();
    ctx.fillStyle = "#34d399";
    ctx.beginPath();
    ctx.arc(245, 187, 3, 0, 2 * Math.PI);
    ctx.fill();
    ctx.fillStyle = "#ffffff";
    ctx.font = "600 10px Poppins, sans-serif";
    ctx.fillText("Verified Worker", 254, 191);

    // Load avatar photo
    const avatarImg = new Image();
    avatarImg.crossOrigin = "anonymous";
    avatarImg.src = data.avatar || "images/avatar_placeholder.png";

    avatarImg.onload = () => {
        // Draw round avatar clipping circle
        ctx.save();
        ctx.beginPath();
        ctx.arc(50, 110, 34, 0, 2 * Math.PI);
        ctx.clip();
        ctx.drawImage(avatarImg, 16, 76, 68, 68);
        ctx.restore();
        
        ctx.strokeStyle = "#ffffff";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(50, 110, 34, 0, 2 * Math.PI);
        ctx.stroke();

        // Load QR Code
        const qrImg = new Image();
        qrImg.crossOrigin = "anonymous";
        const uid = data.uid || "local";
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://goworker-demo.netlify.app/worker-profile.html?id=${uid}`;
        
        qrImg.onload = () => {
            ctx.drawImage(qrImg, 16, 160, 44, 44);
            triggerDownload();
        };
        qrImg.onerror = () => {
            triggerDownload();
        };
    };

    avatarImg.onerror = () => {
        triggerDownload();
    };

    function triggerDownload() {
        const link = document.createElement("a");
        link.download = `goworker_id_${data.worker_id || "card"}.png`;
        link.href = canvas.toDataURL("image/png");
        link.click();
    }
}

// A4 print-optimized PDF Exporter
function downloadCardAsPDF(data) {
    const printWindow = window.open("", "_blank");
    printWindow.document.write(`
        <html>
        <head>
            <title>GoWorker ID Card - ${data.full_name}</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    margin: 0;
                    padding: 40px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    background: #fff;
                }
                .a4-container {
                    width: 210mm;
                    padding: 20mm;
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                }
                .pdf-header {
                    text-align: center;
                    margin-bottom: 40px;
                    border-bottom: 2px solid #0D4DFF;
                    width: 100%;
                    padding-bottom: 20px;
                }
                .pdf-header h2 {
                    color: #0D4DFF;
                    margin: 0;
                    font-size: 24px;
                }
                .pdf-header p {
                    margin: 5px 0 0 0;
                    color: #6b7280;
                    font-size: 14px;
                }
                .cards-wrapper {
                    display: flex;
                    gap: 40px;
                    justify-content: center;
                    margin-bottom: 40px;
                    width: 100%;
                }
                .id-card-front, .id-card-back {
                    width: 340px;
                    height: 215px;
                    border-radius: 14px;
                    padding: 16px;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    box-sizing: border-box;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                }
                .id-card-front {
                    background: linear-gradient(135deg, #0D4DFF 0%, #1e40af 100%);
                    color: #ffffff;
                }
                .id-card-back {
                    background: #ffffff;
                    color: #1f2937;
                    border: 1px solid #e5e7eb;
                }
                .id-card-header-brand {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .id-brand-name {
                    font-weight: 700;
                    font-size: 14px;
                }
                .id-verified-badge {
                    margin-left: auto;
                    background: rgba(16, 185, 129, 0.25);
                    color: #34d399;
                    font-size: 10px;
                    font-weight: 600;
                    padding: 3px 8px;
                    border-radius: 20px;
                }
                .id-card-front-body {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                }
                .id-photo-container {
                    width: 68px;
                    height: 68px;
                    border-radius: 50%;
                    border: 2px solid #ffffff;
                    overflow: hidden;
                    background: #e5e7eb;
                }
                .id-photo-container img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .id-front-details {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    gap: 2px;
                }
                .id-front-details h4 {
                    font-size: 14px;
                    margin: 0;
                }
                .id-field {
                    font-size: 10.5px;
                    margin: 0;
                    display: flex;
                    gap: 6px;
                }
                .id-label {
                    color: rgba(255, 255, 255, 0.65);
                    width: 50px;
                }
                .id-val {
                    font-weight: 600;
                }
                .id-card-front-footer {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                .id-qr-box {
                    width: 44px;
                    height: 44px;
                    background: #ffffff;
                    padding: 2px;
                    border-radius: 4px;
                }
                .id-qr-box img {
                    width: 100%;
                    height: 100%;
                }
                .id-status-text {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 11px;
                    font-weight: 600;
                    background: rgba(255, 255, 255, 0.1);
                    padding: 4px 10px;
                    border-radius: 6px;
                }
                .status-indicator {
                    width: 6px;
                    height: 6px;
                    background: #34d399;
                    border-radius: 50%;
                }
                .id-card-back-header {
                    border-bottom: 1.5px solid #e5e7eb;
                    padding-bottom: 6px;
                }
                .id-card-back-header h4 {
                    font-size: 11.5px;
                    font-weight: 700;
                    color: #0D4DFF;
                    margin: 0;
                }
                .id-card-back-header p {
                    font-size: 9px;
                    color: #6b7280;
                    margin: 2px 0 0 0;
                }
                .id-card-back-body {
                    display: flex;
                    flex-direction: column;
                    gap: 3px;
                }
                .id-back-field {
                    font-size: 10px;
                    margin: 0;
                }
                .id-back-field strong {
                    display: inline-block;
                    width: 120px;
                    color: #6b7280;
                }
                .id-terms {
                    font-size: 8px;
                    color: #6b7280;
                    margin-top: 8px;
                    line-height: 1.3;
                    border-top: 1px dashed #e5e7eb;
                    padding-top: 6px;
                }
                .id-card-back-footer {
                    font-size: 8.5px;
                    color: #0D4DFF;
                    font-weight: 600;
                    text-align: center;
                }
                .pdf-instructions {
                    margin-top: 40px;
                    text-align: center;
                    font-size: 13px;
                    color: #4b5563;
                    border-top: 1px solid #e5e7eb;
                    padding-top: 20px;
                    width: 100%;
                }
                @media print {
                    body {
                        padding: 0;
                        background: none;
                    }
                    .a4-container {
                        border: none;
                        padding: 0;
                    }
                    .pdf-instructions {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="a4-container">
                <div class="pdf-header">
                    <h2>GoWorker Digital Identity</h2>
                    <p>Official Verified Professional Credentials Document</p>
                </div>
                
                <div class="cards-wrapper">
                    <!-- Front Card -->
                    <div class="id-card-front">
                        <div class="id-card-header-brand">
                            <span class="id-brand-name">GoWorker</span>
                            <span class="id-verified-badge"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        </div>
                        <div class="id-card-front-body">
                            <div class="id-photo-container">
                                <img src="${data.avatar || 'images/avatar_placeholder.png'}">
                            </div>
                            <div class="id-front-details">
                                <h4>${data.full_name || 'Worker'}</h4>
                                <p class="id-field"><span class="id-label">ID:</span> <span class="id-val">${data.worker_id || 'GW-2026-000000'}</span></p>
                                <p class="id-field"><span class="id-label">Role:</span> <span class="id-val">${data.profession || 'Trade'}</span></p>
                                <p class="id-field"><span class="id-label">City:</span> <span class="id-val">${data.location || 'City'}</span></p>
                                <p class="id-field"><span class="id-label">Joined:</span> <span class="id-val">${data.joining_date || 'July 22, 2026'}</span></p>
                            </div>
                        </div>
                        <div class="id-card-front-footer">
                            <div class="id-qr-box">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://goworker-demo.netlify.app/worker-profile.html?id=${data.uid || 'local'}">
                            </div>
                            <div class="id-status-text">
                                <div class="status-indicator"></div>
                                <span>Verified Worker</span>
                            </div>
                        </div>
                    </div>

                    <!-- Back Card -->
                    <div class="id-card-back">
                        <div class="id-card-back-header">
                            <h4>GoWorker Digital Identity Card</h4>
                            <p>Standard Corporate Terms & Info</p>
                        </div>
                        <div class="id-card-back-body">
                            <p class="id-back-field"><strong>Worker Category:</strong> <span>${data.category || 'General'}</span></p>
                            <p class="id-back-field"><strong>Experience:</strong> <span>${data.experience || '0'}</span> Years</p>
                            <p class="id-back-field"><strong>Emergency Contact:</strong> <span>${data.phone || 'Emergency'}</span></p>
                            <p class="id-back-field"><strong>Verification Code:</strong> <code>${data.verification_code || '000000'}</code></p>
                            <p class="id-terms">This card is issued by GoWorker. Scanning the QR code validates the credentials. Any unauthorized use is subject to prosecution.</p>
                        </div>
                        <div class="id-card-back-footer">
                            <span>Website: www.goworker.com</span>
                        </div>
                    </div>
                </div>

                <div class="pdf-instructions">
                    <p><strong>Print Instructions:</strong> Press <strong>Ctrl+P</strong> (or Command+P on Mac) to print this page. To save as a PDF file, select <strong>"Save as PDF"</strong> as your printer Destination.</p>
                </div>
            </div>
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

document.addEventListener("DOMContentLoaded", () => {
    // Check session
    const sessionStr = localStorage.getItem("user_session");
    if (!sessionStr) {
        alert("Please log in to view your profile.");
        window.location.href = "login.html";
        return;
    }
    const session = JSON.parse(sessionStr);

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

    // Load user data
    if (useFirebase) {
        firebase.auth().onAuthStateChanged((user) => {
            if (user) {
                firebase.firestore().collection("users").doc(user.uid).get()
                    .then((doc) => {
                        if (doc.exists) {
                            ensureWorkerID(doc.data(), (updatedData) => {
                                populateProfile(updatedData);
                            });
                        } else {
                            ensureWorkerID(session, (updatedData) => {
                                populateProfile(updatedData);
                            });
                        }
                    });
            } else {
                ensureWorkerID(session, (updatedData) => {
                    populateProfile(updatedData);
                });
            }
        });
    } else {
        const localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
        const userData = localUsers.find(u => u.uid === session.uid) || session;
        ensureWorkerID(userData, (updatedData) => {
            populateProfile(updatedData);
        });
    }

    function populateProfile(data) {
        const nameInput = document.getElementById("profile-name");
        if (nameInput) nameInput.value = data.full_name || "";
        
        const emailInput = document.getElementById("profile-email");
        if (emailInput) emailInput.value = data.email || "";
        
        const phoneInput = document.getElementById("profile-phone");
        if (phoneInput) phoneInput.value = data.phone || "";
        
        const cityInput = document.getElementById("profile-city");
        if (cityInput) cityInput.value = data.location || "";
        
        // Update right sidebar
        const summaryName = document.querySelector(".profile-summary-box h4");
        if (summaryName) {
            summaryName.textContent = data.full_name || "User";
        }
        
        // Update navbar user display
        const navRight = document.querySelector(".nav-right");
        if (navRight) {
            navRight.innerHTML = `
                <button class="login-btn" onclick="location.href='profile.html'"><i class="fa-regular fa-user"></i> My Profile</button>
                <button class="signup-btn" id="nav-logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            `;
            const logoutBtn = document.getElementById("nav-logout-btn");
            if (logoutBtn) {
                logoutBtn.addEventListener("click", () => {
                    logoutUser();
                });
            }
        }
        
        // Update mobile drawer navbar user display (if present)
        const mobileLinks = document.querySelector(".mobile-nav-links");
        if (mobileLinks) {
            // Find logout or login links in mobile links
            let authItems = mobileLinks.querySelectorAll("li");
            // Add profile and logout options
            mobileLinks.innerHTML = `
                <li><a href="Index.html"><i class="fa-solid fa-house"></i> Home</a></li>
                <li><a href="find-workers.html"><i class="fa-solid fa-magnifying-glass"></i> Find Workers</a></li>
                <li><a href="worker-registration.html"><i class="fa-solid fa-briefcase"></i> Become a Worker</a></li>
                <li><a href="profile.html" class="active"><i class="fa-regular fa-user"></i> My Profile</a></li>
                <li><a href="#" id="mobile-logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            `;
            const mobLogout = document.getElementById("mobile-logout-btn");
            if (mobLogout) {
                mobLogout.addEventListener("click", (e) => {
                    e.preventDefault();
                    logoutUser();
                });
            }
        // Render ID Card if user is a worker
        if (data.user_type === "worker") {
            // Hide edit form by default, show profile summary card
            const personalInfoCard = document.getElementById("personal-info");
            if (personalInfoCard) personalInfoCard.style.display = "none";

            const workerProfileCard = document.getElementById("worker-profile-view");
            if (workerProfileCard) workerProfileCard.style.display = "block";

            // Fill summary details
            const viewProf = document.getElementById("view-profession");
            if (viewProf) viewProf.textContent = data.profession || "N/A";

            const viewExp = document.getElementById("view-experience");
            if (viewExp) viewExp.textContent = data.experience || "0";

            const viewCat = document.getElementById("view-category");
            if (viewCat) viewCat.textContent = data.category || "N/A";

            const viewCity = document.getElementById("view-city");
            if (viewCity) viewCity.textContent = data.location || "N/A";

            // Setup edit modal elements
            const editModal = document.getElementById("worker-edit-profile-modal");
            const editTrigger = document.getElementById("worker-edit-profile-trigger");
            const editClose = document.getElementById("close-worker-edit-modal");

            if (editTrigger && editModal) {
                editTrigger.onclick = () => {
                    document.getElementById("modal-profile-name").value = data.full_name || "";
                    document.getElementById("modal-profile-phone").value = data.phone || "";
                    document.getElementById("modal-profile-city").value = data.location || "";
                    document.getElementById("modal-profile-password").value = "";
                    editModal.style.display = "flex";
                };
            }

            if (editClose && editModal) {
                editClose.onclick = () => {
                    editModal.style.display = "none";
                };
            }

            if (editModal) {
                editModal.onclick = (e) => {
                    if (e.target === editModal) {
                        editModal.style.display = "none";
                    }
                };
            }

            // Submit handler for modal edit form
            const editForm = document.getElementById("modal-worker-edit-form");
            if (editForm) {
                editForm.onsubmit = (e) => {
                    e.preventDefault();
                    const newName = document.getElementById("modal-profile-name").value.trim();
                    const newPhone = document.getElementById("modal-profile-phone").value.trim();
                    const newCity = document.getElementById("modal-profile-city").value.trim();
                    const newPassword = document.getElementById("modal-profile-password").value;

                    const btn = editForm.querySelector("button[type='submit']");
                    if (btn) {
                        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
                        btn.disabled = true;
                    }

                    if (useFirebase) {
                        const user = firebase.auth().currentUser;
                        if (user) {
                            const updates = {
                                full_name: newName,
                                phone: newPhone,
                                location: newCity
                            };
                            
                            let updatePromise = firebase.firestore().collection("users").doc(user.uid).update(updates);
                            if (newPassword) {
                                updatePromise = updatePromise.then(() => user.updatePassword(newPassword));
                            }

                            updatePromise
                            .then(() => {
                                session.full_name = newName;
                                localStorage.setItem("user_session", JSON.stringify(session));
                                if (btn) {
                                    btn.innerHTML = `Save Settings`;
                                    btn.disabled = false;
                                }
                                editModal.style.display = "none";
                                alert("Profile settings saved successfully!");
                                window.location.reload();
                            })
                            .catch((error) => {
                                if (btn) {
                                    btn.innerHTML = `Save Settings`;
                                    btn.disabled = false;
                                }
                                alert("Error saving profile: " + error.message);
                            });
                        } else {
                            alert("Authentication session lost. Please log in again.");
                            window.location.href = "login.html";
                        }
                    } else {
                        // LocalStorage Mock update
                        setTimeout(() => {
                            const localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
                            const idx = localUsers.findIndex(u => u.uid === session.uid);
                            if (idx !== -1) {
                                localUsers[idx].full_name = newName;
                                localUsers[idx].phone = newPhone;
                                localUsers[idx].location = newCity;
                                localStorage.setItem("local_users", JSON.stringify(localUsers));
                            }
                            session.full_name = newName;
                            localStorage.setItem("user_session", JSON.stringify(session));
                            if (btn) {
                                btn.innerHTML = `Save Settings`;
                                btn.disabled = false;
                            }
                            editModal.style.display = "none";
                            alert("Profile settings saved successfully!");
                            window.location.reload();
                        }, 1200);
                    }
                };
            }

            const idCardMenu = document.getElementById("menu-id-card-item");
            if (idCardMenu) idCardMenu.style.display = "block";

            const idCardSection = document.getElementById("my-id-card");
            if (idCardSection) idCardSection.style.display = "block";

            // Fill details
            const nameEl = document.getElementById("id-front-name");
            if (nameEl) nameEl.textContent = data.full_name || "Worker User";

            const numEl = document.getElementById("id-front-number");
            if (numEl) numEl.textContent = data.worker_id || "GW-2026-000000";

            const profEl = document.getElementById("id-front-profession");
            if (profEl) profEl.textContent = data.profession || "Trade";

            const cityEl = document.getElementById("id-front-city");
            if (cityEl) cityEl.textContent = data.location || "City";

            const joinedEl = document.getElementById("id-front-joined");
            if (joinedEl) joinedEl.textContent = data.joining_date || "July 22, 2026";

            const photoEl = document.getElementById("id-front-photo");
            if (photoEl && data.avatar) {
                photoEl.src = data.avatar;
            }

            const catEl = document.getElementById("id-back-category");
            if (catEl) catEl.textContent = data.category || "General";

            const expEl = document.getElementById("id-back-experience");
            if (expEl) expEl.textContent = data.experience || "0";

            const emergEl = document.getElementById("id-back-emergency");
            if (emergEl) emergEl.textContent = data.phone || "Emergency Contact";

            const verifEl = document.getElementById("id-back-verification");
            if (verifEl) verifEl.textContent = data.verification_code || "000000";

            // Generate QR Code
            const qrEl = document.getElementById("id-front-qr");
            if (qrEl) {
                const uid = data.uid || "local";
                qrEl.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://goworker-demo.netlify.app/worker-profile.html?id=${uid}`;
            }

            // Fill Modal details too
            const mNameEl = document.getElementById("id-front-name-modal");
            if (mNameEl) mNameEl.textContent = data.full_name || "Worker User";

            const mNumEl = document.getElementById("id-front-number-modal");
            if (mNumEl) mNumEl.textContent = data.worker_id || "GW-2026-000000";

            const mProfEl = document.getElementById("id-front-profession-modal");
            if (mProfEl) mProfEl.textContent = data.profession || "Trade";

            const mCityEl = document.getElementById("id-front-city-modal");
            if (mCityEl) mCityEl.textContent = data.location || "City";

            const mJoinedEl = document.getElementById("id-front-joined-modal");
            if (mJoinedEl) mJoinedEl.textContent = data.joining_date || "July 22, 2026";

            const mPhotoEl = document.getElementById("id-front-photo-modal");
            if (mPhotoEl && data.avatar) {
                mPhotoEl.src = data.avatar;
            }

            const mCatEl = document.getElementById("id-back-category-modal");
            if (mCatEl) mCatEl.textContent = data.category || "General";

            const mExpEl = document.getElementById("id-back-experience-modal");
            if (mExpEl) mExpEl.textContent = data.experience || "0";

            const mEmergEl = document.getElementById("id-back-emergency-modal");
            if (mEmergEl) mEmergEl.textContent = data.phone || "Emergency Contact";

            const mVerifEl = document.getElementById("id-back-verification-modal");
            if (mVerifEl) mVerifEl.textContent = data.verification_code || "000000";

            const mQrEl = document.getElementById("id-front-qr-modal");
            if (mQrEl) {
                const uid = data.uid || "local";
                mQrEl.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://goworker-demo.netlify.app/worker-profile.html?id=${uid}`;
            }

            // Modal Open/Close Controls
            const modal = document.getElementById("id-card-modal");
            const viewModalBtn = document.getElementById("view-id-modal-btn");
            const closeBtn = document.getElementById("close-id-modal-btn");

            if (viewModalBtn && modal) {
                viewModalBtn.onclick = () => {
                    modal.style.display = "flex";
                };
            }
            if (closeBtn && modal) {
                closeBtn.onclick = () => {
                    modal.style.display = "none";
                };
            }
            if (modal) {
                modal.onclick = (e) => {
                    if (e.target === modal) {
                        modal.style.display = "none";
                    }
                };
            }

            // Set up action buttons
            const printBtn = document.getElementById("print-id-card-btn");
            if (printBtn) {
                printBtn.onclick = () => {
                    window.print();
                };
            }
            const modalPrintBtn = document.getElementById("modal-print-btn");
            if (modalPrintBtn) {
                modalPrintBtn.onclick = () => {
                    window.print();
                };
            }

            const pngBtn = document.getElementById("download-png-btn");
            if (pngBtn) {
                pngBtn.onclick = () => {
                    downloadCardAsPNG(data);
                };
            }

            const pdfBtn = document.getElementById("download-pdf-btn");
            if (pdfBtn) {
                pdfBtn.onclick = () => {
                    downloadCardAsPDF(data);
                };
            }

            // Share functionality
            const shareBtn = document.getElementById("share-id-card-btn");
            const modalShareBtn = document.getElementById("modal-share-btn");
            const profileUrl = `https://goworker-demo.netlify.app/worker-profile.html?id=${data.uid || "local"}`;

            const triggerShare = () => {
                if (navigator.share) {
                    navigator.share({
                        title: `${data.full_name || 'Worker'} - GoWorker ID Card`,
                        text: `Check out my verified professional ID card on GoWorker!`,
                        url: profileUrl
                    })
                    .catch((error) => console.log('Error sharing:', error));
                } else {
                    navigator.clipboard.writeText(profileUrl);
                    alert("Profile URL copied to clipboard! Share it anywhere.");
                }
            };

            if (shareBtn) shareBtn.onclick = triggerShare;
            if (modalShareBtn) modalShareBtn.onclick = triggerShare;
        } else {
            // Customer - Ensure edit form is visible, summary card is hidden
            const personalInfoCard = document.getElementById("personal-info");
            if (personalInfoCard) personalInfoCard.style.display = "block";

            const workerProfileCard = document.getElementById("worker-profile-view");
            if (workerProfileCard) workerProfileCard.style.display = "none";
        }
    }

    function logoutUser() {
        if (useFirebase) {
            firebase.auth().signOut().then(() => {
                localStorage.removeItem("user_session");
                alert("Logged out successfully!");
                window.location.href = "login.html";
            });
        } else {
            localStorage.removeItem("user_session");
            alert("Logged out successfully!");
            window.location.href = "login.html";
        }
    }
    // 1. SAVE PROFILE FORM CHANGES
    const personalForm = document.getElementById("profile-personal-form");
    if (personalForm) {
        personalForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const newName = document.getElementById("profile-name").value.trim();
            const newPhone = document.getElementById("profile-phone").value.trim();
            const newCity = document.getElementById("profile-city").value.trim();
            
            const btn = personalForm.querySelector("button[type='submit']");
            if (btn) {
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
                btn.disabled = true;
            }
            
            if (useFirebase) {
                const user = firebase.auth().currentUser;
                if (user) {
                    firebase.firestore().collection("users").doc(user.uid).update({
                        full_name: newName,
                        phone: newPhone,
                        location: newCity
                    })
                    .then(() => {
                        session.full_name = newName;
                        localStorage.setItem("user_session", JSON.stringify(session));
                        const summaryName = document.querySelector(".profile-summary-box h4");
                        if (summaryName) {
                            summaryName.textContent = newName;
                        }
                        if (btn) {
                            btn.innerHTML = `Save Changes`;
                            btn.disabled = false;
                        }
                        alert("Profile settings saved successfully!");
                    })
                    .catch((error) => {
                        if (btn) {
                            btn.innerHTML = `Save Changes`;
                            btn.disabled = false;
                        }
                        alert("Error saving profile: " + error.message);
                    });
                } else {
                    alert("Authentication session lost. Please log in again.");
                    window.location.href = "login.html";
                }
            } else {
                // LocalStorage Mock
                setTimeout(() => {
                    const localUsers = JSON.parse(localStorage.getItem("local_users")) || [];
                    const idx = localUsers.findIndex(u => u.uid === session.uid);
                    if (idx !== -1) {
                        localUsers[idx].full_name = newName;
                        localUsers[idx].phone = newPhone;
                        localUsers[idx].location = newCity;
                        localStorage.setItem("local_users", JSON.stringify(localUsers));
                    }
                    session.full_name = newName;
                    localStorage.setItem("user_session", JSON.stringify(session));
                    const summaryName = document.querySelector(".profile-summary-box h4");
                    if (summaryName) {
                        summaryName.textContent = newName;
                    }
                    if (btn) {
                        btn.innerHTML = `Save Changes`;
                        btn.disabled = false;
                    }
                    alert("Profile settings saved successfully!");
                }, 1200);
            }
        });
    }

    // 2. REMOVE SAVED WORKER
    const removeSavedBtns = document.querySelectorAll(".remove-saved-btn");
    removeSavedBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".saved-worker-card");
            const name = card.querySelector("h4").textContent;
            const confirmRemove = confirm(`Do you want to remove ${name} from your Saved list?`);
            
            if (confirmRemove) {
                card.style.opacity = "0";
                card.style.transform = "scale(0.9)";
                setTimeout(() => {
                    card.remove();
                    // check if empty
                    const grid = document.querySelector(".saved-workers-grid");
                    if (grid && grid.children.length === 0) {
                        grid.innerHTML = `<p style="font-size:13px; color:var(--secondary-text);">No saved professionals yet.</p>`;
                    }
                }, 300);
            }
        });
    });

    // 3. EDIT PROFILE PHOTO TRIGGER
    const editPhotoBtn = document.getElementById("edit-avatar-btn");
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.accept = "image/*";

    if (editPhotoBtn) {
        editPhotoBtn.addEventListener("click", () => {
            fileInput.click();
        });

        fileInput.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const avatarImgs = document.querySelectorAll("#profile-summary-avatar, #right-sidebar-avatar");
                    avatarImgs.forEach(img => {
                        img.src = event.target.result;
                    });
                    alert("Profile photo updated successfully!");
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 4. ADD NEW ADDRESS
    const addAddressBtn = document.getElementById("add-address-btn");
    if (addAddressBtn) {
        addAddressBtn.addEventListener("click", () => {
            const addressVal = prompt("Enter new Address details:");
            if (addressVal && addressVal.trim() !== "") {
                const list = document.getElementById("address-list-container");
                const newCard = document.createElement("div");
                newCard.className = "address-card";
                newCard.innerHTML = `
                    <div>
                        <h4 style="font-size:14px; font-weight:600; margin-bottom:4px;"><i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> Other Address</h4>
                        <p style="font-size:12px; color:var(--secondary-text); margin-bottom:0;">${escapeHTML(addressVal)}</p>
                    </div>
                    <button class="remove-saved-btn" style="position:static;" onclick="this.closest('.address-card').remove();"><i class="fa-regular fa-trash-can"></i></button>
                `;
                list.insertBefore(newCard, addAddressBtn);
                alert("New address added successfully!");
            }
        });
    }

    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }
});
