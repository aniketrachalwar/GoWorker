// GoWorker Worker Profile Interactions
// worker-profile.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. IMAGE GALLERY MODAL PREVIEW
    const galleryItems = document.querySelectorAll(".gallery-item img");
    const modal = document.createElement("div");
    modal.className = "modal";
    modal.id = "gallery-modal";
    modal.innerHTML = `
        <span class="modal-close"><i class="fa-solid fa-xmark"></i></span>
        <img class="modal-content" id="modal-img" src="" alt="Work Preview">
    `;
    document.body.appendChild(modal);

    const modalImg = modal.querySelector("#modal-img");
    const modalClose = modal.querySelector(".modal-close");

    galleryItems.forEach(item => {
        item.addEventListener("click", () => {
            modal.style.display = "flex";
            modalImg.src = item.src;
        });
    });

    const closeModal = () => {
        modal.style.display = "none";
    };

    modalClose.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // 2. FAVORITE BUTTON ANIMATION
    const favoriteBtn = document.getElementById("fav-profile-btn");
    if (favoriteBtn) {
        favoriteBtn.addEventListener("click", () => {
            favoriteBtn.classList.toggle("active");
            const icon = favoriteBtn.querySelector("i");
            if (favoriteBtn.classList.contains("active")) {
                icon.className = "fa-solid fa-heart";
                icon.style.color = "var(--danger)";
                alert("Worker saved to your Favorites!");
            } else {
                icon.className = "fa-regular fa-heart";
                icon.style.color = "var(--secondary-text)";
                alert("Worker removed from your Favorites.");
            }
        });
    }

    // 3. SERVICE BOOK BUTTON SELECTION
    const serviceBookBtns = document.querySelectorAll(".service-card .btn-book");
    serviceBookBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".service-card");
            const name = card.querySelector("h4").textContent;
            alert(`Selected service: ${name}. Directing you to complete the booking!`);
            window.location.href = "booking.html";
        });
     });
 
     // 4. ACTION BUTTONS (Chat, Call, Save)
     const chatBtn = document.getElementById("chat-worker-btn");
     if (chatBtn) {
         chatBtn.addEventListener("click", () => {
             window.location.href = "chat.html";
         });
     }

    const callBtn = document.getElementById("call-worker-btn");
    if (callBtn) {
        callBtn.addEventListener("click", () => {
            alert("Initiating call simulation to +91 9876543210...");
        });
    }

    // 5. RIPPLE CLICKS
    const rippleButtons = document.querySelectorAll(".btn-book, .btn-icon, .action-buttons-group button");
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
});

// Global function to trigger Worker ID Card Download/Print Layout
window.downloadIDCard = function(workerName, workerUserId, profession, avatar, location, experience, email, phone, memberSince) {
    const printWindow = window.open("", "_blank", "width=800,height=600");
    const idCode = "GW-" + String(workerUserId).padStart(5, '0');
    
    printWindow.document.write(`
        <html>
        <head>
            <title>GoWorker ID Card - \${workerName}</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    background: #f1f5f9;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                }
                .card {
                    width: 320px;
                    height: 500px;
                    background: #ffffff;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    border: 1px solid #e2e8f0;
                    position: relative;
                }
                .header {
                    background: linear-gradient(135deg, #0d4dff 0%, #4a7bff 100%);
                    color: white;
                    padding: 24px;
                    text-align: center;
                    border-bottom-left-radius: 24px;
                    border-bottom-right-radius: 24px;
                }
                .logo {
                    font-size: 20px;
                    font-weight: 800;
                    letter-spacing: 0.5px;
                    margin-bottom: 4px;
                }
                .badge {
                    background: rgba(255,255,255,0.2);
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                }
                .photo-container {
                    margin-top: -50px;
                    align-self: center;
                    z-index: 10;
                }
                .photo {
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                    border: 4px solid white;
                    object-fit: cover;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                }
                .content {
                    flex: 1;
                    padding: 24px;
                    text-align: center;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                .name {
                    font-size: 18px;
                    font-weight: 700;
                    color: #0f172a;
                    margin: 0 0 4px 0;
                }
                .title {
                    font-size: 13px;
                    color: #0d4dff;
                    font-weight: 600;
                    margin: 0;
                }
                .footer {
                    font-size: 11px;
                    color: #94a3b8;
                    margin-top: 5px;
                }
                @media print {
                    body {
                        background: none;
                        min-height: auto;
                    }
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="header">
                    <div class="logo"><i class="fa-solid fa-screwdriver-wrench"></i> GoWorker</div>
                    <span class="badge"><i class="fa-solid fa-circle-check"></i> Verified</span>
                </div>
                <div class="photo-container">
                    <img class="photo" src="\${avatar}" alt="\${workerName}">
                </div>
                <div class="content">
                    <div style="margin-bottom: 10px;">
                        <h2 class="name">\${workerName}</h2>
                        <p class="title">\${profession}</p>
                        <p style="font-size: 11px; color: #64748b; margin: 4px 0 0 0; font-weight: 600;">ID: \${idCode}</p>
                    </div>
                    
                    <div class="info-list" style="text-align: left; margin: 10px 0; border-top: 1.5px solid #f1f5f9; border-bottom: 1.5px solid #f1f5f9; padding: 12px 0; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px;">
                            <span style="color: #64748b; font-weight: 500;">Email:</span>
                            <span style="color: #1e293b; font-weight: 600;">\${email}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px;">
                            <span style="color: #64748b; font-weight: 500;">Phone:</span>
                            <span style="color: #1e293b; font-weight: 600;">\${phone}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px;">
                            <span style="color: #64748b; font-weight: 500;">Location:</span>
                            <span style="color: #1e293b; font-weight: 600;">\${location}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px;">
                            <span style="color: #64748b; font-weight: 500;">Member Since:</span>
                            <span style="color: #1e293b; font-weight: 600;">\${memberSince}</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 5px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://goworker-demo.netlify.app/worker-profile.html?id=\${workerUserId}" style="width: 70px; height: 70px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 2px;" alt="QR Code">
                        <div style="text-align: left; font-size: 10px; color: #64748b; max-width: 140px; line-height: 1.3;">
                            Scan QR to verify professional credentials on the GoWorker platform.
                        </div>
                    </div>
                    <div class="footer">
                        <span>GoWorker Professional ID Card</span>
                    </div>
                </div>
            </div>
            
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
};
