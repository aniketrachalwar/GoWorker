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
