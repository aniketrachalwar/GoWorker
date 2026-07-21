<?php
/**
 * GoWorker - Chat & Messages
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="chat.css">

<!-- ================= CHAT CONTAINER ================= -->
<main class="container" style="min-height: 85vh; display: flex; flex-direction: column;">
  <div class="chat-layout">
    <!-- LEFT PANEL: Conversation List -->
    <section class="chat-left-panel">
      <div class="chat-left-header">
        <input type="text" class="chat-search-input" placeholder="Search conversations...">
      </div>
      <div class="conv-list">
        <!-- Active Card -->
        <div class="conv-card active">
          <div class="conv-avatar-container">
            <img class="conv-avatar" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop" alt="Ramesh">
          </div>
          <div class="conv-meta">
            <div class="conv-name-row">
              <span style="color: var(--dark-navy); font-weight: 700;">Ramesh Kumar</span>
              <span style="font-weight: 400; font-size: 10px; color: var(--secondary-text);">09:12 AM</span>
            </div>
            <div class="conv-last-msg">Electrician - I am on my way to your address.</div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="conv-card">
          <div class="conv-avatar-container">
            <img class="conv-avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&fit=crop" alt="Sohan">
          </div>
          <div class="conv-meta">
            <div class="conv-name-row">
              <span style="color: var(--dark-navy); font-weight: 700;">Sohan Singh</span>
              <span style="font-weight: 400; font-size: 10px; color: var(--secondary-text);">Yesterday</span>
            </div>
            <div class="conv-last-msg">Plumber - Pipe leakage resolved!</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CENTER PANEL: Chat Space -->
    <section class="chat-center-panel">
      <header class="chat-center-header">
        <div>
          <h4 id="active-chat-name" style="font-size: 15px; font-weight: 700; margin-bottom: 2px; color: var(--dark-navy);">Ramesh Kumar</h4>
          <p id="active-chat-prof" style="font-size: 11px; color: var(--secondary-text); margin-bottom: 0;">Electrician • Online</p>
        </div>
        <div>
          <button class="btn-icon" style="border: none;" onclick="alert('Initiating call simulation...')"><i class="fa-solid fa-phone"></i></button>
        </div>
      </header>

      <!-- Messages Area -->
      <div class="chat-msg-area" id="chat-message-area">
        <div class="msg-bubble msg-incoming">
          Hello, I have confirmed your booking request for the Emergency Electrical Troubleshooting.
          <span class="msg-time">08:35 AM</span>
        </div>
        <div class="msg-bubble msg-outgoing">
          Great, thank you Ramesh. How long will it take for you to reach my address?
          <span class="msg-time">08:42 AM</span>
        </div>
        <div class="msg-bubble msg-incoming">
          I am packing my tools now and starting my bike. I will reach in about 15 minutes.
          <span class="msg-time">09:12 AM</span>
        </div>
      </div>

      <!-- Chat Input bar -->
      <footer class="chat-input-bar">
        <button class="btn-icon" style="border: none; color: var(--secondary-text);"><i class="fa-regular fa-face-smile" style="font-size: 18px;"></i></button>
        <button class="btn-icon" style="border: none; color: var(--secondary-text);"><i class="fa-solid fa-paperclip" style="font-size: 18px;"></i></button>
        <input type="text" id="chat-message-input" class="chat-input-field" placeholder="Type a message...">
        <button type="button" id="chat-send-btn" class="btn-book" style="padding: 12px 24px;">Send <i class="fa-solid fa-paper-plane" style="margin-left: 6px;"></i></button>
      </footer>
    </section>

    <!-- RIGHT PANEL: Metadata Details -->
    <section class="chat-right-panel">
      <img src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=150&fit=crop" id="right-side-avatar" alt="Ramesh" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--border-color); margin-bottom: 12px;">
      <h4 id="right-side-name" class="right-meta-title" style="color: var(--dark-navy); font-weight: 700;">Ramesh Kumar</h4>
      <p id="right-side-prof" class="right-meta-desc">Electrician • Pune</p>
      
      <div style="text-align: left; width: 100%;">
        <div class="shared-media-block">
          <div class="shared-media-title">Active Booking Details</div>
          <p style="font-size: 13px; margin-bottom: 6px; color: var(--dark-navy);"><strong>Troubleshooting</strong></p>
          <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 12px;">Date: Today, 20 July • 09:00 AM</p>
          <button class="btn-book" style="width: 100%; font-size: 12px; padding: 10px 14px;" onclick="location.href='booking-history.php'">Track Booking</button>
        </div>

        <div class="shared-media-block">
          <div class="shared-media-title">Shared Media (Photos)</div>
          <div class="shared-media-grid">
            <div class="shared-media-item"><img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=100"></div>
            <div class="shared-media-item"><img src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=100"></div>
            <div class="shared-media-item"><img src="https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?w=100"></div>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

<script src="chat.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
