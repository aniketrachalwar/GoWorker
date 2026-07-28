// GoWorker Notifications Engine
// notifications.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. MARK A SINGLE NOTIFICATION AS READ
    const unreadCards = document.querySelectorAll(".notif-card.unread");
    const countBadge = document.getElementById("unread-notif-count");
    let unreadCount = unreadCards.length;

    unreadCards.forEach(card => {
        card.addEventListener("click", () => {
            if (card.classList.contains("unread")) {
                card.classList.remove("unread");
                const dot = card.querySelector(".notif-read-dot");
                if (dot) dot.remove();
                
                unreadCount = Math.max(0, unreadCount - 1);
                if (countBadge) {
                    countBadge.textContent = unreadCount;
                }
            }
        });
    });

    // 2. MARK ALL AS READ
    const markAllBtn = document.getElementById("mark-all-read-btn");
    if (markAllBtn) {
        markAllBtn.addEventListener("click", () => {
            const activeUnreadCards = document.querySelectorAll(".notif-card.unread");
            activeUnreadCards.forEach(card => {
                card.classList.remove("unread");
                const dot = card.querySelector(".notif-read-dot");
                if (dot) dot.remove();
            });
            unreadCount = 0;
            if (countBadge) countBadge.textContent = "0";
            alert("All notifications marked as read!");
        });
    }

    // 3. ARCHIVE / DELETE NOTIFICATION
    const notifCards = document.querySelectorAll(".notif-card");
    notifCards.forEach(card => {
        // Add double click or click delete icon action simulator
        const deleteBtn = document.createElement("button");
        deleteBtn.className = "btn-icon";
        deleteBtn.style.position = "absolute";
        deleteBtn.style.bottom = "20px";
        deleteBtn.style.right = "24px";
        deleteBtn.style.border = "none";
        deleteBtn.style.background = "transparent";
        deleteBtn.style.cursor = "pointer";
        deleteBtn.innerHTML = `<i class="fa-regular fa-trash-can" style="color: var(--secondary-text);"></i>`;
        card.appendChild(deleteBtn);

        deleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const confirmDelete = confirm("Do you want to delete this notification?");
            if (confirmDelete) {
                card.style.opacity = "0";
                card.style.transform = "scale(0.95)";
                setTimeout(() => {
                    card.remove();
                    // Update unread count if it was unread
                    if (card.classList.contains("unread")) {
                        unreadCount = Math.max(0, unreadCount - 1);
                        if (countBadge) countBadge.textContent = unreadCount;
                    }
                }, 300);
            }
        });
    });
});
