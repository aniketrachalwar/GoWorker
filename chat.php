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
  <div class="chat-layout" id="chat-layout-wrapper">
    
    <!-- LEFT PANEL: Conversation List -->
    <section class="chat-left-panel">
      <div class="chat-left-header">
        <div class="search-box-container">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="conversation-search" class="chat-search-input" placeholder="Search conversations...">
        </div>
      </div>
      <div class="conv-list" id="conversations-container">
        <!-- Ramesh Kumar -->
        <div class="conv-card active" data-worker-id="2" data-name="Ramesh Kumar" data-profession="Electrician" data-avatar="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop" data-location="Pune">
          <div class="conv-avatar-container">
            <img class="conv-avatar" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop" alt="Ramesh">
            <span class="online-indicator online"></span>
          </div>
          <div class="conv-meta">
              <span class="conv-name">Ramesh Kumar</span>
              <span class="conv-time">09:12 AM</span>
            <div class="conv-prof">Electrician</div>
            <div class="conv-last-msg">Electrician - I am on my way to your address.</div>
          </div>
          <div class="conv-badge-container">
            <span class="unread-badge">2</span>
          </div>
        </div>

        <!-- Sohan Singh -->
        <div class="conv-card" data-worker-id="3" data-name="Sohan Singh" data-profession="Plumber" data-avatar="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&fit=crop" data-location="Mumbai">
          <div class="conv-avatar-container">
            <img class="conv-avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&fit=crop" alt="Sohan">
            <span class="online-indicator online"></span>
          </div>
          <div class="conv-meta">
              <span class="conv-name">Sohan Singh</span>
              <span class="conv-time">Yesterday</span>
            <div class="conv-prof">Plumber</div>
            <div class="conv-last-msg">Plumber - Pipe leakage resolved!</div>
          </div>
          <div class="conv-badge-container" style="display: none;">
            <span class="unread-badge">0</span>
          </div>
        </div>
      </div>
    </section>

    <!-- CENTER PANEL: Chat Space -->
      <!-- Drag & Drop Overlay -->
      <div class="drag-drop-overlay" id="drag-overlay">
        <div class="drag-drop-box">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <h3>Drop files here</h3>
          <p>Support images and documents up to 10MB</p>
        </div>
      </div>

      <header class="chat-center-header">
        <div class="chat-header-info">
          <button id="chat-back-btn" class="chat-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
          </button>
          <img id="active-chat-avatar" class="chat-header-avatar" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop" alt="Active User">
          <div>
            <h4 id="active-chat-name" style="font-size: 15px; font-weight: 700; margin-bottom: 2px; color: var(--dark-navy);">Ramesh Kumar</h4>
            <p id="active-chat-prof" style="font-size: 11px; color: var(--secondary-text); margin-bottom: 0;">Electrician • Online</p>
          </div>
        </div>
        <div class="header-actions">
          <button class="btn-header-call" onclick="alert('Simulating voice call to ' + document.getElementById('active-chat-name').textContent + '...')">
            <i class="fa-solid fa-phone"></i> <span>Voice Call</span>
          </button>
          <button class="btn-header-call" onclick="alert('Simulating video call to ' + document.getElementById('active-chat-name').textContent + '...')">
            <i class="fa-solid fa-video"></i> <span>Video Call</span>
          </button>
          <button class="btn-header-options" onclick="alert('Options Menu Coming Soon!')">
            <i class="fa-solid fa-ellipsis-vertical"></i>
          </button>
        </div>
      </header>

      <!-- Messages Area -->
      <div class="chat-msg-area" id="chat-message-area">
        <!-- Conversation will load here dynamically via JS -->
      </div>

      <!-- Chat Input bar -->
      <footer class="chat-input-bar">
        <!-- Emoji Picker -->
        <div class="emoji-picker-container" id="emoji-picker">
          <div class="emoji-picker-header">Select Emoji</div>
          <div class="emoji-picker-grid">
            <span class="emoji-item">😀</span>
            <span class="emoji-item">😁</span>
            <span class="emoji-item">😂</span>
            <span class="emoji-item">🤣</span>
            <span class="emoji-item">😊</span>
            <span class="emoji-item">😍</span>
            <span class="emoji-item">😎</span>
            <span class="emoji-item">😢</span>
            <span class="emoji-item">😡</span>
            <span class="emoji-item">👍</span>
            <span class="emoji-item">❤️</span>
            <span class="emoji-item">🎉</span>
            <span class="emoji-item">🔥</span>
            <span class="emoji-item">👏</span>
            <span class="emoji-item">🙌</span>
            <span class="emoji-item">🤔</span>
            <span class="emoji-item">🚀</span>
            <span class="emoji-item">✨</span>
          </div>
        </div>
        <!-- Pre-send File Preview Bar (Images/Documents) -->
        <div class="upload-preview-bar" id="file-preview-bar" style="display: none; align-items: center; gap: 12px; background: var(--white); padding: 12px 16px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: var(--shadow-premium); margin-bottom: 10px; width: 100%; box-sizing: border-box;">
          <div id="file-preview-content" style="display: flex; align-items: center; gap: 12px; flex: 1;">
            <!-- Rendered inline via JS (80x80 thumbnail for images, icon + name for files) -->
          </div>
          <button type="button" class="btn-cancel-upload" id="cancel-file-upload" style="background: none; border: none; color: var(--secondary-text); font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
            <i class="fa-solid fa-xmark"></i> Remove
          </button>
        </div>

        <!-- Voice Recording Controls Inline -->
        <div class="voice-record-bar" id="voice-record-bar" style="display: none; align-items: center; justify-content: space-between; background: var(--white); padding: 8px 16px; border-radius: 26px; border: 1.5px solid var(--border-color); width: 100%; box-sizing: border-box; height: 52px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-microphone" style="color: #EF4444; animation: blinkDot 1s infinite alternate;"></i>
            <span style="font-weight: 600; font-size: 14px; color: var(--primary-text);">Recording...</span>
            <span id="record-timer" style="font-family: monospace; font-size: 14px; color: var(--secondary-text);">00:00</span>
          </div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" id="btn-cancel-record" style="background: none; border: none; color: #EF4444; font-weight: 600; font-size: 13px; cursor: pointer;">Cancel</button>
            <button type="button" id="btn-send-record" class="btn-chat-send" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0D4DFF 0%, #4A7BFF 100%); border: none; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Send Voice"><i class="fa-solid fa-paper-plane" style="font-size: 14px;"></i></button>
          </div>
        </div>

        <!-- Input Control Row -->
        <div class="input-row-controls" id="input-controls-row">
          <button type="button" class="btn-input-widget" id="emoji-trigger-btn" title="Add Emojis">
            <i class="fa-regular fa-face-smile"></i>
          </button>
          <button type="button" class="btn-input-widget" id="file-trigger-btn" title="Attach Document/Image">
            <i class="fa-solid fa-paperclip"></i>
          </button>
          <button type="button" class="btn-input-widget" id="mic-trigger-btn" title="Record Voice Message">
            <i class="fa-solid fa-microphone"></i>
          </button>
          
          <!-- Hidden inputs for file uploading -->
          <input type="file" id="hidden-file-input" style="display: none;" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
          
          <textarea id="chat-message-input" class="chat-input-field" rows="1" placeholder="Type a message..."></textarea>
          <button type="button" id="chat-send-btn" class="btn-chat-send">
            <i class="fa-solid fa-paper-plane"></i>
          </button>
        </div>
      </footer>
    </section>

    <!-- RIGHT PANEL: Metadata Details -->
    <section class="chat-right-panel" id="chat-right-meta-panel">
      <img src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=150&fit=crop" id="right-side-avatar" class="right-avatar" alt="Ramesh">
      <h4 id="right-side-name" class="right-meta-title" style="color: var(--dark-navy); font-weight: 700;">Ramesh Kumar</h4>
      <p id="right-side-prof" class="right-meta-desc">Electrician • Pune</p>
      
      <div style="text-align: left; width: 100%;">
        <div class="shared-media-block">
          <div class="shared-media-title">Active Booking Details</div>
          <p style="font-size: 13px; margin-bottom: 6px; color: var(--dark-navy);"><strong>Troubleshooting Services</strong></p>
          <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 12px;">Date: Today • 09:00 AM</p>
          <button class="btn-book" style="width: 100%; font-size: 12px; padding: 10px 14px;" onclick="location.href='booking-history.php'">Track Booking</button>
        </div>

        <div class="shared-media-block">
          <div class="shared-media-title">Shared Media (Photos)</div>
          <div class="shared-media-grid">
            <div class="shared-media-item"><img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=100" style="width:100%; height:100%; object-fit:cover;"></div>
            <div class="shared-media-item"><img src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=100" style="width:100%; height:100%; object-fit:cover;"></div>
            <div class="shared-media-item"><img src="https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?w=100" style="width:100%; height:100%; object-fit:cover;"></div>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>



<!-- GoWorker AI Chatbot Assistant -->
<button class="ai-assistant-toggle" id="ai-assistant-toggle" title="GoWorker AI Assistant">
  <i class="fa-solid fa-robot"></i>
</button>

<div class="ai-assistant-window" id="ai-assistant-window">
  <div class="ai-window-header">
    <div class="ai-header-title">
      <i class="fa-solid fa-robot" style="margin-right: 8px;"></i>
      <span>GoWorker Assistant</span>
    </div>
    <button class="ai-close-btn" id="ai-close-btn"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="ai-window-messages" id="ai-messages-container">
    <div class="ai-msg ai-bot">
      Hello! I am your GoWorker Support Assistant. How can I help you today?
    </div>
    <div class="ai-suggested-actions">
      <button class="ai-btn" data-action="track">Track Booking</button>
      <button class="ai-btn" data-action="find">Find Worker</button>
      <button class="ai-btn" data-action="payment">Payment Help</button>
      <button class="ai-btn" data-action="support">Contact Support</button>
      <button class="ai-btn" data-action="report">Report Issue</button>
    </div>
  </div>
  <div class="ai-window-input-bar">
    <input type="text" id="ai-msg-input" placeholder="Type a message...">
    <button type="button" id="ai-send-btn"><i class="fa-solid fa-paper-plane"></i></button>
  </div>
</div>

<!-- GoWorker AI Chatbot Assistant -->
<button class="ai-assistant-toggle" id="ai-assistant-toggle" title="GoWorker AI Assistant">
  <i class="fa-solid fa-robot"></i>
</button>

<div class="ai-assistant-window" id="ai-assistant-window">
  <div class="ai-window-header">
    <div class="ai-header-title">
      <i class="fa-solid fa-robot" style="margin-right: 8px;"></i>
      <span>GoWorker Assistant</span>
    </div>
    <button class="ai-close-btn" id="ai-close-btn"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="ai-window-messages" id="ai-messages-container">
    <div class="ai-msg ai-bot">
      Hello! I am your GoWorker Support Assistant. How can I help you today?
    </div>
    <div class="ai-suggested-actions">
      <button class="ai-btn" data-action="track">Track Booking</button>
      <button class="ai-btn" data-action="find">Find Worker</button>
      <button class="ai-btn" data-action="payment">Payment Help</button>
      <button class="ai-btn" data-action="support">Contact Support</button>
      <button class="ai-btn" data-action="report">Report Issue</button>
    </div>
  </div>
  <div class="ai-window-input-bar">
    <input type="text" id="ai-msg-input" placeholder="Type a message...">
    <button type="button" id="ai-send-btn"><i class="fa-solid fa-paper-plane"></i></button>
  </div>
</div>

<script src="chat.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
