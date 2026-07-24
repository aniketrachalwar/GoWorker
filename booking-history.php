<?php
/**
 * GoWorker - Booking History & Tracking
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="booking-history.css">

<!-- ================= LAYOUT CONTAINER ================= -->
<main class="container" style="margin-top: 40px; min-height: 80vh;">
  <!-- Top Summary Statistics Header -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div class="booking-item-card" style="margin-bottom: 0; padding: 20px; display: flex; align-items: center; gap: 16px;">
      <div style="width: 44px; height: 44px; background: var(--primary-light); color: var(--primary); border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 20px;"><i class="fa-solid fa-clock"></i></div>
      <div>
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 2px;">1</h3>
        <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 0;">Upcoming Booking</p>
      </div>
    </div>

    <div class="booking-item-card" style="margin-bottom: 0; padding: 20px; display: flex; align-items: center; gap: 16px;">
      <div style="width: 44px; height: 44px; background: rgba(34, 197, 94, 0.08); color: var(--success); border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 20px;"><i class="fa-solid fa-check-double"></i></div>
      <div>
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 2px;">12</h3>
        <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 0;">Completed Jobs</p>
      </div>
    </div>

    <div class="booking-item-card" style="margin-bottom: 0; padding: 20px; display: flex; align-items: center; gap: 16px;">
      <div style="width: 44px; height: 44px; background: var(--light-bg); color: var(--secondary-text); border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 20px;"><i class="fa-solid fa-wallet"></i></div>
      <div>
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 2px;">₹3,420</h3>
        <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 0;">Total Spent</p>
      </div>
    </div>
  </div>

  <!-- Booking details columns -->
  <div class="history-layout">
    <!-- LEFT LIST (70%) -->
    <section class="history-list-area">
      <!-- Booking Card 1 (Active) -->
      <article class="booking-item-card">
        <header class="booking-item-header">
          <span class="booking-id-tag">ID: #GOW-902183</span>
          <span class="status-badge status-ongoing">Ongoing</span>
        </header>
        <div style="display: flex; gap: 16px; margin-bottom: 20px; align-items: start;">
          <img src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop" alt="Ramesh" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
          <div>
            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 4px; color: var(--dark-navy);">Ramesh Kumar <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0001</span></h4>
            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 4px;">Emergency Electrical Troubleshooting</p>
            <p style="font-size: 12px; color: var(--secondary-text);"><i class="fa-solid fa-calendar"></i> Today, 20 July • 09:00 AM</p>
          </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <button class="btn-book" onclick="location.href='customer-chat.php'">Chat</button>
          <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--border-color); color: var(--primary-text);" onclick="alert('Calling simulation...')"><i class="fa-solid fa-phone"></i> Call</button>
          <button class="btn-book btn-cancel-booking" style="background: var(--white); border: 1.5px solid var(--danger); color: var(--danger);">Cancel Booking</button>
        </div>
      </article>

      <!-- Booking Card 2 (Completed) -->
      <article class="booking-item-card">
        <header class="booking-item-header">
          <span class="booking-id-tag">ID: #GOW-847291</span>
          <span class="status-badge status-completed">Completed</span>
        </header>
        <div style="display: flex; gap: 16px; margin-bottom: 20px; align-items: start;">
          <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&fit=crop" alt="Sohan" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
          <div>
            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 4px; color: var(--dark-navy);">Sohan Singh <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0002</span></h4>
            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 4px;">Plumbing Maintenance & Leak Repair</p>
            <p style="font-size: 12px; color: var(--secondary-text);"><i class="fa-solid fa-calendar"></i> July 14, 2026 • 02:30 PM</p>
          </div>
        </div>
        
        <div style="background: var(--light-bg); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 20px;">
          <p style="font-size: 13px; font-weight: 600; margin-bottom: 8px;">Rate & Review Service</p>
          <div class="rating-stars" style="display: flex; gap: 6px; font-size: 20px; cursor: pointer; color: var(--secondary-text);">
            <i class="fa-regular fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <i class="fa-regular fa-star"></i>
          </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <button class="btn-book btn-invoice" style="background: var(--white); border: 1.5px solid var(--border-color); color: var(--primary-text);"><i class="fa-solid fa-file-arrow-down"></i> Download Invoice</button>
          <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--primary); color: var(--primary);" onclick="location.href='booking.php'">Book Again</button>
        </div>
      </article>
    </section>

    <!-- RIGHT SIDE STICKY DETAILS & LIVE TIMELINE TRACKER (30%) -->
    <aside class="sticky-booking-sidebar">
      <div class="booking-sidebar-card" style="padding: 24px; background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Live Tracker</h3>
        
        <div class="tracker-timeline">
          <div class="tracker-step completed">
            <div class="tracker-step-title">Booking Confirmed</div>
            <div class="tracker-step-time">08:30 AM</div>
          </div>

          <div class="tracker-step completed">
            <div class="tracker-step-title">Worker Accepted</div>
            <div class="tracker-step-time">08:45 AM</div>
          </div>

          <div class="tracker-step active">
            <div class="tracker-step-title">Worker On The Way</div>
            <div class="tracker-step-time">In Progress • ETA 15 mins</div>
          </div>

          <div class="tracker-step">
            <div class="tracker-step-title">Work Started</div>
            <div class="tracker-step-time">-- : --</div>
          </div>

          <div class="tracker-step">
            <div class="tracker-step-title">Completed</div>
            <div class="tracker-step-time">-- : --</div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</main>

<script src="booking-history.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
