// GoWorker Booking Engine
// booking.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. DATE SELECTION
    const calendarDays = document.querySelectorAll(".calendar-day:not(.disabled)");
    const selectedDateEl = document.getElementById("summary-date");

    calendarDays.forEach(day => {
        day.addEventListener("click", () => {
            calendarDays.forEach(d => d.classList.remove("active"));
            day.classList.add("active");
            
            const dateStr = day.getAttribute("data-date");
            if (selectedDateEl) {
                selectedDateEl.textContent = dateStr;
            }
            calculateTotals();
        });
    });

    // 2. TIME SLOT SELECTION
    const timeSlots = document.querySelectorAll(".time-slot");
    const selectedTimeEl = document.getElementById("summary-time");

    timeSlots.forEach(slot => {
        slot.addEventListener("click", () => {
            timeSlots.forEach(s => s.classList.remove("active"));
            slot.classList.add("active");

            const timeStr = slot.getAttribute("data-time");
            if (selectedTimeEl) {
                selectedTimeEl.textContent = timeStr;
            }
        });
    });

    // 3. IMAGE UPLOAD PREVIEW
    const fileInput = document.getElementById("job-images");
    const previewContainer = document.getElementById("preview-container");

    if (fileInput && previewContainer) {
        fileInput.addEventListener("change", (e) => {
            previewContainer.innerHTML = "";
            const files = Array.from(e.target.files).slice(0, 5); // limit to 5
            
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const item = document.createElement("div");
                    item.className = "upload-preview-item";
                    item.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                    previewContainer.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // 4. COUPON CODE ENGINE
    const applyCouponBtn = document.getElementById("apply-coupon-btn");
    const couponInput = document.getElementById("coupon-input");
    const discountEl = document.getElementById("summary-discount");
    let discountAmount = 0;

    if (applyCouponBtn && couponInput) {
        applyCouponBtn.addEventListener("click", () => {
            const val = couponInput.value.toUpperCase().trim();
            if (val === "SAVE10") {
                discountAmount = 30; // ₹30 off
                alert("Coupon SAVE10 applied successfully! Discount: ₹30");
            } else if (val === "WELCOME100") {
                discountAmount = 100;
                alert("Coupon WELCOME100 applied successfully! Discount: ₹100");
            } else {
                discountAmount = 0;
                alert("Invalid coupon code. Try SAVE10 or WELCOME100!");
            }
            calculateTotals();
        });
    }

    // 5. CALCULATE TOTALS
    const basePriceVal = 299;
    const platformFeeVal = 20;
    const taxRate = 0.05; // 5% GST

    function calculateTotals() {
        const subtotalEl = document.getElementById("summary-subtotal");
        const taxEl = document.getElementById("summary-tax");
        const discountRowEl = document.getElementById("summary-discount-row");
        const totalEl = document.getElementById("summary-total");

        const subtotal = basePriceVal;
        const tax = Math.round(subtotal * taxRate);
        const finalTotal = subtotal + platformFeeVal + tax - discountAmount;

        if (subtotalEl) subtotalEl.textContent = `₹${subtotal}`;
        if (taxEl) taxEl.textContent = `₹${tax}`;
        
        if (discountEl) {
            discountEl.textContent = `-₹${discountAmount}`;
            if (discountAmount > 0) {
                discountRowEl.style.display = "flex";
            } else {
                discountRowEl.style.display = "none";
            }
        }
        
        if (totalEl) totalEl.textContent = `₹${finalTotal}`;
    }

    // Initialize totals on start
    calculateTotals();

    // 6. PROCEED TO PAYMENT FORM SUBMIT
    const bookingForm = document.getElementById("booking-main-form");
    const submitBtn = document.getElementById("submit-booking-btn");

    if (bookingForm && submitBtn) {
        bookingForm.addEventListener("submit", (e) => {
            e.preventDefault();
            
            // Check selections
            const activeDate = document.querySelector(".calendar-day.active");
            const activeTime = document.querySelector(".time-slot.active");

            if (!activeDate) {
                alert("Please select a date from the calendar!");
                return;
            }
            if (!activeTime) {
                alert("Please select a convenient time slot!");
                return;
            }

            submitBtn.classList.add("loading");
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.classList.remove("loading");
                alert("Booking created successfully! Redirecting to confirmation page...");
                window.location.href = "booking-history.php";
            }, 1800);
        });
    }
});
