// GoWorker Profile & Settings Engine
// profile.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. SAVE PROFILE FORM CHANGES
    const personalForm = document.getElementById("profile-personal-form");
    if (personalForm) {
        personalForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const btn = personalForm.querySelector("button[type='submit']");
            if (btn) {
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = `Save Changes`;
                    btn.disabled = false;
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
