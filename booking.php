<?php
/**
 * GoWorker - Complete Booking
 */
require_once __DIR__ . '/includes/auth.php';

// Enforce customer login
requireCustomer();

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="booking.css">

<!-- ================= BOOKING CONTAINER ================= -->
<main class="container" style="margin-top: 40px; min-height: 80vh;">
  <!-- Step Progress Stepper -->
  <div class="stepper-container">
    <div class="step-item completed">
      <div class="step-dot"><i class="fa-solid fa-check"></i></div>
      <span class="step-title">Worker Selected</span>
    </div>
    <div class="step-item active">
      <div class="step-dot">2</div>
      <span class="step-title">Booking Details</span>
    </div>
    <div class="step-item">
      <div class="step-dot">3</div>
      <span class="step-title">Payment</span>
    </div>
    <div class="step-item">
      <div class="step-dot">4</div>
      <span class="step-title">Confirmation</span>
    </div>
  </div>

  <div class="booking-layout">
    <!-- LEFT SIDE FORM (70%) -->
    <section class="booking-form-area" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm);">
      <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 4px; color: var(--dark-navy);">Book Your Service</h2>
      <p style="color: var(--secondary-text); font-size: 14px; margin-bottom: 32px;">Complete the details below to confirm your booking.</p>

      <!-- Selected Worker Info -->
      <div class="selected-worker-box">
        <img src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop" alt="Ramesh" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;">
        <div>
          <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 2px;">Ramesh Kumar <span style="color: var(--primary); font-size: 11px;"><i class="fa-solid fa-circle-check"></i> Verified</span></h4>
          <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 4px;">Electrician • ★ 4.9 (128 reviews)</p>
          <a href="worker-profile.php" style="font-size: 12px; color: var(--primary); font-weight: 600;">View Profile</a>
        </div>
      </div>

      <form id="booking-main-form" action="#">
        <!-- Service Dropdown -->
        <div class="form-group" style="margin-bottom: 24px;">
          <label for="booking-service" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Select Service</label>
          <select id="booking-service" class="form-input" style="padding-left: 16px;" required>
            <option value="troubleshooting">Emergency Troubleshooting - ₹299/hr</option>
            <option value="wiring">Smart Home Automation & Wiring - ₹399/hr</option>
            <option value="ac">AC Installation & Repair - ₹599/hr</option>
          </select>
        </div>

        <!-- Calendar Picker -->
        <div class="form-group" style="margin-bottom: 24px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Select Date</label>
          <div class="calendar-grid">
            <div class="calendar-day active" data-date="Mon, July 20">
              <span>Mon</span>
              <strong>20</strong>
            </div>
            <div class="calendar-day" data-date="Tue, July 21">
              <span>Tue</span>
              <strong>21</strong>
            </div>
            <div class="calendar-day" data-date="Wed, July 22">
              <span>Wed</span>
              <strong>22</strong>
            </div>
            <div class="calendar-day" data-date="Thu, July 23">
              <span>Thu</span>
              <strong>23</strong>
            </div>
            <div class="calendar-day" data-date="Fri, July 24">
              <span>Fri</span>
              <strong>24</strong>
            </div>
            <div class="calendar-day" data-date="Sat, July 25">
              <span>Sat</span>
              <strong>25</strong>
            </div>
            <div class="calendar-day disabled" data-date="Sun, July 26">
              <span>Sun</span>
              <strong>26</strong>
            </div>
          </div>
        </div>

        <!-- Time slot Selection -->
        <div class="form-group" style="margin-bottom: 24px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Select Time Slot</label>
          <div class="time-slots-container">
            <div class="time-slot active" data-time="09:00 AM - Morning">09:00 AM</div>
            <div class="time-slot" data-time="11:30 AM - Morning">11:30 AM</div>
            <div class="time-slot" data-time="02:30 PM - Afternoon">02:30 PM</div>
            <div class="time-slot" data-time="05:00 PM - Evening">05:00 PM</div>
          </div>
        </div>

        <!-- Address Form -->
        <div class="form-group" style="margin-bottom: 24px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label style="font-weight: 600; font-size: 14px; margin-bottom: 0;">Service Address</label>
            <button type="button" class="btn-coupon" style="padding: 6px 12px; font-size: 12px;"><i class="fa-solid fa-location-crosshairs"></i> Use Current Location</button>
          </div>
          <input type="text" class="form-input" style="padding-left: 16px; margin-bottom: 12px;" placeholder="Flat / House No. / Building Name" required>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <input type="text" class="form-input" style="padding-left: 16px;" placeholder="City (e.g. Pune)" required>
            <input type="text" class="form-input" style="padding-left: 16px;" placeholder="Pincode" required>
          </div>
        </div>

        <!-- Description and Image attachments -->
        <div class="form-group" style="margin-bottom: 24px;">
          <label for="job-desc" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Job Description (Optional)</label>
          <textarea id="job-desc" class="form-input" style="padding-left: 16px; padding-top: 12px; min-height: 100px; resize: vertical;" placeholder="Describe the issue you're facing..."></textarea>
          
          <div style="margin-top: 16px;">
            <label class="btn-coupon" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
              <i class="fa-solid fa-camera"></i> Attach Images (Max 5)
              <input type="file" id="job-images" accept="image/*" multiple style="display: none;">
            </label>
            <div class="upload-preview-container" id="preview-container"></div>
          </div>
        </div>

        <!-- Submit Trigger (handled via sidebar button click submit of form) -->
        <button type="submit" id="hidden-submit-trigger" style="display: none;"></button>
      </form>
    </section>

    <!-- RIGHT SIDE STICKY BOOKING SUMMARY (30%) -->
    <aside class="sticky-summary-sidebar">
      <div class="summary-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--dark-navy);">Booking Summary</h3>
        
        <div class="summary-row">
          <span>Professional:</span>
          <span style="font-weight: 600; color: var(--primary-text);">Ramesh Kumar</span>
        </div>

        <div class="summary-row">
          <span>Date:</span>
          <span id="summary-date" style="font-weight: 600; color: var(--primary-text);">Mon, July 20</span>
        </div>

        <div class="summary-row">
          <span>Time:</span>
          <span id="summary-time" style="font-weight: 600; color: var(--primary-text);">09:00 AM</span>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-row">
          <span>Subtotal:</span>
          <span id="summary-subtotal">₹299</span>
        </div>

        <div class="summary-row">
          <span>Platform Fee:</span>
          <span>₹20</span>
        </div>

        <div class="summary-row">
          <span>Taxes (GST):</span>
          <span id="summary-tax">₹15</span>
        </div>

        <div class="summary-row" id="summary-discount-row" style="display: none; color: var(--success);">
          <span>Discount Applied:</span>
          <span id="summary-discount">-₹0</span>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-row total">
          <span>Total Amount:</span>
          <span id="summary-total">₹334</span>
        </div>

        <!-- Coupon apply -->
        <div class="coupon-box" style="margin-top: 16px;">
          <input type="text" id="coupon-input" class="coupon-input" placeholder="Promo code (SAVE10)">
          <button type="button" id="apply-coupon-btn" class="btn-coupon">Apply</button>
        </div>

        <button type="button" class="btn-primary-auth" style="width: 100%; margin-top: 24px; height: 50px;" onclick="document.getElementById('hidden-submit-trigger').click();" id="submit-booking-btn">
          <span>Proceed to Payment</span>
          <i class="fa-solid fa-credit-card"></i>
        </button>
      </div>
    </aside>
  </div>
</main>

<script src="booking.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
