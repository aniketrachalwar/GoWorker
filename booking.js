// GoWorker Booking Engine
// booking.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. DYNAMIC DATE & TIME GENERATION
    const calendarGrid = document.querySelector(".calendar-grid");
    const timeSlotsContainer = document.querySelector(".time-slots-container");
    const selectedDateEl = document.getElementById("summary-date");
    const selectedTimeEl = document.getElementById("summary-time");

    if (calendarGrid && timeSlotsContainer) {
        const daysShort = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        const monthsShort = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        const today = new Date();
        
        // Define all hourly 24h slots
        const allSlots = [
            "09:00", "10:00", "11:00", "12:00", "13:00", 
            "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"
        ];
        
        function renderTimeSlots(isSelectedDateToday) {
            timeSlotsContainer.innerHTML = "";
            
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            
            let firstAvailableSlot = null;
            
            allSlots.forEach(slot => {
                let isAvailable = true;
                
                if (isSelectedDateToday) {
                    const [slotHour, slotMinute] = slot.split(":").map(Number);
                    // slot has passed if slot total minutes <= current system total minutes
                    if ((slotHour * 60 + slotMinute) <= (currentHour * 60 + currentMinute)) {
                        isAvailable = false;
                    }
                }
                
                if (isAvailable) {
                    const slotEl = document.createElement("div");
                    slotEl.className = "time-slot";
                    slotEl.setAttribute("data-time", slot);
                    slotEl.textContent = slot;
                    
                    slotEl.addEventListener("click", () => {
                        document.querySelectorAll(".time-slot").forEach(s => s.classList.remove("active"));
                        slotEl.classList.add("active");
                        if (selectedTimeEl) {
                            selectedTimeEl.textContent = slot;
                        }
                    });
                    
                    timeSlotsContainer.appendChild(slotEl);
                    
                    if (!firstAvailableSlot) {
                        firstAvailableSlot = slotEl;
                    }
                }
            });
            
            if (firstAvailableSlot) {
                firstAvailableSlot.classList.add("active");
                if (selectedTimeEl) {
                    selectedTimeEl.textContent = firstAvailableSlot.getAttribute("data-time");
                }
            } else {
                if (selectedTimeEl) {
                    selectedTimeEl.textContent = "—";
                }
                if (isSelectedDateToday) {
                    timeSlotsContainer.innerHTML = `<div style="grid-column: 1 / -1; color: var(--secondary-text); font-size: 13px; text-align: center; padding: 12px; border: 1.5px dashed var(--border-color); border-radius: var(--radius-sm);">No time slots available for today. Please select a future date.</div>`;
                }
            }
        }

        // Generate 7 calendar days dynamically
        for (let i = 0; i < 7; i++) {
            const d = new Date(today);
            d.setDate(today.getDate() + i);
            
            const dayName = daysShort[d.getDay()];
            const dateNum = d.getDate();
            const monthName = monthsShort[d.getMonth()];
            const yearNum = d.getFullYear();
            
            const dateStr = `${dayName} ${dateNum} ${monthName} ${yearNum}`;
            
            const dayCard = document.createElement("div");
            dayCard.className = "calendar-day";
            if (i === 0) {
                dayCard.classList.add("active");
                dayCard.setAttribute("data-is-today", "true");
                if (selectedDateEl) {
                    selectedDateEl.textContent = dateStr;
                }
            } else {
                dayCard.setAttribute("data-is-today", "false");
            }
            dayCard.setAttribute("data-date", dateStr);
            
            // Add label: Today on the current date card, hidden placeholder on others to maintain heights
            if (i === 0) {
                dayCard.innerHTML = `
                    <span class="today-label" style="font-size: 10px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 2px; opacity: 0.85;">Today</span>
                    <span>${dayName}</span>
                    <strong>${dateNum}</strong>
                `;
            } else {
                dayCard.innerHTML = `
                    <span class="today-label" style="font-size: 10px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 2px; visibility: hidden;">Today</span>
                    <span>${dayName}</span>
                    <strong>${dateNum}</strong>
                `;
            }
            
            dayCard.addEventListener("click", () => {
                document.querySelectorAll(".calendar-day").forEach(c => c.classList.remove("active"));
                dayCard.classList.add("active");
                
                if (selectedDateEl) {
                    selectedDateEl.textContent = dateStr;
                }
                
                const isToday = dayCard.getAttribute("data-is-today") === "true";
                renderTimeSlots(isToday);
                calculateTotals();
            });
            
            calendarGrid.appendChild(dayCard);
        }
        
        // Initial render of time slots for today
        renderTimeSlots(true);
    }

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
    const serviceSelect = document.getElementById("booking-service");
    const platformFeeVal = 20;
    const taxRate = 0.05; // 5% GST

    function getBasePrice() {
        if (serviceSelect) {
            const selectedOpt = serviceSelect.options[serviceSelect.selectedIndex];
            if (selectedOpt && selectedOpt.hasAttribute("data-rate")) {
                return parseInt(selectedOpt.getAttribute("data-rate")) || window.workerHourlyRate || 299;
            }
        }
        return window.workerHourlyRate || 299;
    }

    function calculateTotals() {
        const subtotalEl = document.getElementById("summary-subtotal");
        const taxEl = document.getElementById("summary-tax");
        const discountRowEl = document.getElementById("summary-discount-row");
        const totalEl = document.getElementById("summary-total");

        const subtotal = getBasePrice();
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

    if (serviceSelect) {
        serviceSelect.addEventListener("change", calculateTotals);
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
                window.location.href = "booking-history.html";
            }, 1800);
        });
    }
});
