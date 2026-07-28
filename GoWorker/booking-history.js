// GoWorker Booking History & Tracking Engine
// booking-history.js

document.addEventListener("DOMContentLoaded", () => {
    const listContainer = document.getElementById("bookings-list-container");
    const sessionUser = JSON.parse(localStorage.getItem("user_session") || "{}");

    // Fetch and render bookings dynamically
    function fetchBookings() {
        if (!listContainer) return;

        fetch('/api/bookings/list')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.bookings) {
                    renderBookings(data.bookings);
                } else {
                    listContainer.innerHTML = `<p style="padding: 24px; text-align: center; color: var(--secondary-text);">No bookings found.</p>`;
                }
            })
            .catch(err => {
                console.error("Fetch bookings error:", err);
                listContainer.innerHTML = `<p style="padding: 24px; text-align: center; color: var(--danger);">Failed to load bookings history.</p>`;
            });
    }

    function renderBookings(bookings) {
        listContainer.innerHTML = "";

        if (bookings.length === 0) {
            listContainer.innerHTML = `<p style="padding: 24px; text-align: center; color: var(--secondary-text);">No booking records found.</p>`;
            return;
        }

        bookings.forEach(booking => {
            const card = document.createElement("article");
            card.className = "booking-item-card";

            const statusClass = getStatusClass(booking.status);
            const formattedDate = formatDateString(booking.booking_date);
            
            let detailsHtml = "";
            let actionsHtml = "";

            if (sessionUser.user_type === 'worker') {
                // Worker's view of bookings (details about customer)
                detailsHtml = `
                    <div style="display: flex; gap: 16px; margin-bottom: 20px; align-items: start;">
                        <div class="avatar" style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; justify-content: center; align-items: center; font-weight: 700; font-size: 20px;">
                            ${booking.customer_name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 4px;">${escapeHTML(booking.customer_name)}</h4>
                            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 4px;">${escapeHTML(booking.description)}</p>
                            <p style="font-size: 12px; color: var(--secondary-text);"><i class="fa-solid fa-calendar"></i> ${formattedDate} • ${booking.time_slot}</p>
                            <p style="font-size: 12px; color: var(--secondary-text);"><i class="fa-solid fa-location-dot"></i> ${escapeHTML(booking.address)}</p>
                            <p style="font-size: 13px; font-weight: 600; color: var(--primary); margin-top: 4px;">Value: ₹${booking.total_price}</p>
                        </div>
                    </div>
                `;
                actionsHtml = `
                    <button class="btn-book" onclick="location.href='/messages?worker_id=${booking.customer_id}&name=${encodeURIComponent(booking.customer_name)}&profession=Customer&avatar='">Chat with Customer</button>
                    <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--border-color); color: var(--primary-text);" onclick="alert('Calling ${booking.customer_phone}...')"><i class="fa-solid fa-phone"></i> Call</button>
                `;
            } else {
                // Customer's view of bookings (details about worker)
                const avatarUrl = booking.worker_avatar || 'images/avatar_placeholder.png';
                detailsHtml = `
                    <div style="display: flex; gap: 16px; margin-bottom: 20px; align-items: start;">
                        <img src="${avatarUrl}" alt="${escapeHTML(booking.worker_name)}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 4px;">${escapeHTML(booking.worker_name)}</h4>
                            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 4px;">${escapeHTML(booking.category_name)} - ${escapeHTML(booking.description)}</p>
                            <p style="font-size: 12px; color: var(--secondary-text);"><i class="fa-solid fa-calendar"></i> ${formattedDate} • ${booking.time_slot}</p>
                            <p style="font-size: 13px; font-weight: 600; color: var(--primary); margin-top: 4px;">Paid: ₹${booking.total_price}</p>
                        </div>
                    </div>
                `;

                const chatParams = `worker_id=${booking.worker_id}&name=${encodeURIComponent(booking.worker_name)}&profession=${encodeURIComponent(booking.category_name)}&avatar=${encodeURIComponent(avatarUrl)}`;
                
                if (booking.status === 'completed') {
                    detailsHtml += `
                        <div style="background: var(--light-bg); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                            <p style="font-size: 13px; font-weight: 600; margin-bottom: 8px;">Rate & Review Service</p>
                            <div class="rating-stars" data-booking-id="${booking.id}" data-worker-id="${booking.worker_id}" style="display: flex; gap: 6px; font-size: 20px; cursor: pointer; color: var(--secondary-text);">
                                <i class="fa-regular fa-star" data-rating="1"></i>
                                <i class="fa-regular fa-star" data-rating="2"></i>
                                <i class="fa-regular fa-star" data-rating="3"></i>
                                <i class="fa-regular fa-star" data-rating="4"></i>
                                <i class="fa-regular fa-star" data-rating="5"></i>
                            </div>
                        </div>
                    `;
                    actionsHtml = `
                        <button class="btn-book btn-invoice" data-booking-id="${booking.id}"><i class="fa-solid fa-file-arrow-down"></i> Download Invoice</button>
                        <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--primary); color: var(--primary);" onclick="location.href='/find-workers'">Book Again</button>
                    `;
                } else if (booking.status === 'cancelled') {
                    actionsHtml = `
                        <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--primary); color: var(--primary);" onclick="location.href='/find-workers'">Book Again</button>
                    `;
                } else {
                    actionsHtml = `
                        <button class="btn-book" onclick="location.href='/messages?${chatParams}'">Chat</button>
                        <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--border-color); color: var(--primary-text);" onclick="alert('Calling simulation...')"><i class="fa-solid fa-phone"></i> Call</button>
                        <button class="btn-book btn-cancel" data-booking-id="${booking.id}" style="background: var(--white); border: 1.5px solid var(--danger); color: var(--danger);">Cancel Booking</button>
                    `;
                }
            }

            card.innerHTML = `
                <header class="booking-item-header">
                    <span class="booking-id-tag">ID: #GOW-${String(booking.id).padStart(6, '0')}</span>
                    <span class="status-badge ${statusClass}">${booking.status.toUpperCase()}</span>
                </header>
                ${detailsHtml}
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    ${actionsHtml}
                </div>
            `;

            listContainer.appendChild(card);
        });

        // Add event listeners for dynamic widgets
        attachCardListeners();
    }

    function attachCardListeners() {
        // Cancel button click
        document.querySelectorAll(".btn-cancel").forEach(btn => {
            btn.addEventListener("click", () => {
                const bookingId = btn.getAttribute("data-booking-id");
                if (confirm("Are you sure you want to cancel this booking?")) {
                    alert(`Simulated cancellation for booking #${bookingId}`);
                    location.reload();
                }
            });
        });

        // Invoice download simulator
        document.querySelectorAll(".btn-invoice").forEach(btn => {
            btn.addEventListener("click", () => {
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Generating...`;
                setTimeout(() => {
                    btn.innerHTML = `<i class="fa-solid fa-file-arrow-down"></i> Invoice Downloaded`;
                    alert("Invoice PDF downloaded successfully!");
                }, 1000);
            });
        });

        // Rating submission
        document.querySelectorAll(".rating-stars i").forEach(star => {
            star.addEventListener("click", (e) => {
                const rating = star.getAttribute("data-rating");
                const starsContainer = star.closest(".rating-stars");
                const bookingId = starsContainer.getAttribute("data-booking-id");
                const workerId = starsContainer.getAttribute("data-worker-id");

                // Highlight stars
                const stars = starsContainer.querySelectorAll("i");
                stars.forEach((s, idx) => {
                    if (idx < rating) {
                        s.className = "fa-solid fa-star";
                        s.style.color = "#F59E0B";
                    } else {
                        s.className = "fa-regular fa-star";
                        s.style.color = "var(--secondary-text)";
                    }
                });

                // Post review
                fetch('/api/reviews/submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        booking_id: bookingId,
                        worker_id: workerId,
                        rating: rating,
                        review_text: 'Excellent service provided!'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert("Thank you for your rating!");
                    } else {
                        alert("Review submission failed: " + data.message);
                    }
                })
                .catch(err => {
                    console.error("Review error:", err);
                    alert("Error submitting review.");
                });
            });
        });
    }

    function getStatusClass(status) {
        if (status === 'pending') return 'status-ongoing'; // Orange-like status
        if (status === 'confirmed') return 'status-ongoing';
        if (status === 'completed') return 'status-completed'; // Green
        return 'status-cancelled'; // Red/Cancelled
    }

    function formatDateString(dateStr) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const d = new Date(dateStr);
        return isNaN(d.getTime()) ? dateStr : d.toLocaleDateString('en-US', options);
    }

    function escapeHTML(str) {
        if (!str) return '';
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

    // Trigger initial load
    fetchBookings();
});
