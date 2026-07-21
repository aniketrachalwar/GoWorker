<?php
/**
 * GoWorker - Notifications Center
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="notifications.css">

<!-- ================= NOTIFICATIONS LAYOUT ================= -->
<main class="container" style="margin-top: 40px; min-height: 80vh;">
  <div class="notif-layout">
    <!-- LEFT FILTER SIDEBAR -->
    <aside class="notif-sidebar">
      <ul class="notif-menu">
        <li>
          <a href="#" class="notif-menu-link active">
            <span>All Notifications</span>
            <span class="notif-badge-count" id="unread-notif-count">3</span>
          </a>
        </li>
        <li><a href="#" class="notif-menu-link"><span>Bookings</span></a></li>
        <li><a href="#" class="notif-menu-link"><span>Messages</span></a></li>
        <li><a href="#" class="notif-menu-link"><span>Payments</span></a></li>
        <li><a href="#" class="notif-menu-link"><span>Offers & Coupons</span></a></li>
      </ul>
    </aside>

    <!-- CENTER NOTIFICATION FEED -->
    <section class="notif-feed-area">
      <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 24px; color: var(--dark-navy);">Notifications Center</h2>

      <!-- Notification 1 -->
      <article class="notif-card unread">
        <div class="notif-icon-box" style="background: rgba(34, 197, 94, 0.08); color: var(--success);">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div class="notif-info">
          <h4 class="notif-title">Booking Accepted</h4>
          <p class="notif-desc">Ramesh Kumar (Electrician) has accepted your booking request for today, 20 July.</p>
          <span class="notif-time">08:45 AM</span>
        </div>
        <span class="notif-read-dot"></span>
      </article>

      <!-- Notification 2 -->
      <article class="notif-card unread">
        <div class="notif-icon-box" style="background: var(--primary-light); color: var(--primary);">
          <i class="fa-solid fa-comments"></i>
        </div>
        <div class="notif-info">
          <h4 class="notif-title">New Message Received</h4>
          <p class="notif-desc">Ramesh Kumar: "I am on my way to your address. I will reach in 15 mins."</p>
          <span class="notif-time">09:12 AM</span>
        </div>
        <span class="notif-read-dot"></span>
      </article>

      <!-- Notification 3 -->
      <article class="notif-card unread">
        <div class="notif-icon-box" style="background: rgba(245, 158, 11, 0.08); color: var(--warning);">
          <i class="fa-solid fa-tags"></i>
        </div>
        <div class="notif-info">
          <h4 class="notif-title">Festival Promo Active!</h4>
          <p class="notif-desc">Use coupon code <strong>SAVE10</strong> to get flat 10% off on all electrical services this week.</p>
          <span class="notif-time">Yesterday</span>
        </div>
        <span class="notif-read-dot"></span>
      </article>

      <!-- Notification 4 (Read) -->
      <article class="notif-card">
        <div class="notif-icon-box" style="background: rgba(34, 197, 94, 0.08); color: var(--success);">
          <i class="fa-solid fa-credit-card"></i>
        </div>
        <div class="notif-info">
          <h4 class="notif-title">Payment Settled</h4>
          <p class="notif-desc">Payment of ₹499 completed successfully for Booking ID: #GOW-847291.</p>
          <span class="notif-time">July 14, 2026</span>
        </div>
      </article>
    </section>

    <!-- RIGHT QUICK ACTIONS PANEL -->
    <aside class="notif-right-panel">
      <div class="booking-item-card" style="padding: 20px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px; color: var(--dark-navy);">Quick Settings</h4>
        <button class="btn-book" id="mark-all-read-btn" style="width: 100%; margin-bottom: 12px; font-size: 12px; padding: 10px 14px;">Mark All as Read</button>
        <button class="btn-book" style="width: 100%; font-size: 12px; padding: 10px 14px; background: var(--white); border: 1.5px solid var(--border-color); color: var(--secondary-text);" onclick="alert('Cleared notification cache.')">Clear History</button>
      </div>
    </aside>
  </div>
</main>

<script src="notifications.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
