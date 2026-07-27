// GoWorker Booking History & Tracking Engine
// booking-history.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. CANCEL BOOKING SIMULATOR
    const cancelBtns = document.querySelectorAll(".btn-cancel-booking");
    cancelBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const bookingId = btn.getAttribute("data-booking-id");
            const confirmCancel = confirm("Are you sure you want to cancel this booking? Cancellation fees may apply.");
            if (confirmCancel) {
                if (!bookingId) {
                    // Fallback to static mock behavior for static/demo pages
                    alert("Booking request cancelled successfully. Refund initiated to source method.");
                    const card = btn.closest(".booking-item-card");
                    if (card) {
                        const badge = card.querySelector(".status-badge");
                        if (badge) {
                            badge.className = "status-badge";
                            badge.style.background = "rgba(239, 68, 68, 0.08)";
                            badge.style.color = "var(--danger)";
                            badge.textContent = "Cancelled";
                        }
                        btn.remove();
                    }
                    return;
                }

                // Perform real cancellation via API
                const base = (window.GOWORKER_BASE_URL ? window.GOWORKER_BASE_URL.replace(/\/$/, '') : '');
                fetch(base + '/api/cancel-booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ booking_id: bookingId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Booking request cancelled successfully. Refund initiated to source method.");
                        // Refresh booking list and stats dynamically by reloading
                        location.reload();
                    } else {
                        alert("Error: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error cancelling booking:", error);
                    alert("Failed to cancel booking. Please try again.");
                });
            }
        });
    });

    // 2. DOWNLOAD INVOICE SIMULATOR
    const invoiceBtns = document.querySelectorAll(".btn-invoice");
    invoiceBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Generating...`;
            setTimeout(() => {
                btn.innerHTML = `<i class="fa-solid fa-file-arrow-down"></i> Invoice Downloaded`;
                alert("Invoice PDF downloaded successfully for Booking ID: #GOW-902183");
            }, 1200);
        });
    });

    // 3. BOOKING RATING SYSTEM
    const ratingStars = document.querySelectorAll(".rating-stars i");
    ratingStars.forEach((star, index) => {
        star.addEventListener("click", () => {
            ratingStars.forEach((s, idx) => {
                if (idx <= index) {
                    s.className = "fa-solid fa-star";
                    s.style.color = "#F59E0B";
                } else {
                    s.className = "fa-regular fa-star";
                    s.style.color = "var(--secondary-text)";
                }
            });
            alert(`You rated this service ${index + 1} Stars!`);
        });
    });
});
