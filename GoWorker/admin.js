// GoWorker Platform Administration Engine
// admin.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. DRAWER TOGGLE UTILITY
    const drawer = document.getElementById("admin-detail-drawer");
    const overlay = document.getElementById("admin-drawer-overlay");
    const closeBtn = document.querySelector(".drawer-close-btn");

    const openDrawer = () => {
        if (drawer && overlay) {
            drawer.classList.add("open");
            overlay.classList.add("show");
        }
    };

    const closeDrawer = () => {
        if (drawer && overlay) {
            drawer.classList.remove("open");
            overlay.classList.remove("show");
        }
    };

    if (closeBtn) closeBtn.addEventListener("click", closeDrawer);
    if (overlay) overlay.addEventListener("click", closeDrawer);

    // 2. ROW CLICK ACTION (Simulating loading data in details panel)
    const tableRows = document.querySelectorAll(".admin-table tbody tr");
    tableRows.forEach(row => {
        row.addEventListener("click", (e) => {
            // Avoid triggering drawer if clicking action buttons directly
            if (e.target.closest("button") || e.target.closest("a")) return;

            const nameEl = row.querySelector(".row-name");
            const profEl = row.querySelector(".row-profession");
            const avatarEl = row.querySelector(".row-avatar");

            if (nameEl) {
                document.getElementById("drawer-name").textContent = nameEl.textContent;
            }
            if (profEl) {
                document.getElementById("drawer-prof").textContent = profEl.textContent;
            }
            if (avatarEl && document.getElementById("drawer-avatar")) {
                document.getElementById("drawer-avatar").src = avatarEl.src;
            }

            openDrawer();
        });
    });

    // 3. ADMIN DECISION ACTIONS (Approve, Reject, Suspend)
    const approveBtns = document.querySelectorAll(".btn-approve-action");
    approveBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const confirmApprove = confirm("Are you sure you want to APPROVE this registration request?");
            if (confirmApprove) {
                alert("Application approved successfully! Credentials sent to partner.");
                closeDrawer();
                // update row badge status
                const row = btn.closest("tr");
                if (row) {
                    const badge = row.querySelector(".status-badge");
                    if (badge) {
                        badge.style.background = "rgba(34, 197, 94, 0.08)";
                        badge.style.color = "var(--success)";
                        badge.textContent = "Approved";
                    }
                }
            }
        });
    });

    const rejectBtns = document.querySelectorAll(".btn-reject-action");
    rejectBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const reason = prompt("Enter rejection remarks:");
            if (reason !== null) {
                alert(`Application rejected. Remarks sent: "${reason}"`);
                closeDrawer();
                const row = btn.closest("tr");
                if (row) {
                    const badge = row.querySelector(".status-badge");
                    if (badge) {
                        badge.style.background = "rgba(239, 68, 68, 0.08)";
                        badge.style.color = "var(--danger)";
                        badge.textContent = "Rejected";
                    }
                }
            }
        });
    });

    // 4. BULK EXPORT ACTIONS
    const exportBtn = document.getElementById("admin-export-btn");
    if (exportBtn) {
        exportBtn.addEventListener("click", () => {
            exportBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Exporting...`;
            setTimeout(() => {
                exportBtn.innerHTML = `<i class="fa-solid fa-file-export"></i> Export CSV`;
                alert("System operations log CSV file generated and downloaded successfully!");
            }, 1200);
        });
    }
});
