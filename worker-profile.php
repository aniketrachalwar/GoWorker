<?php
/**
 * GoWorker - Worker Profile Page (Ramesh Kumar Example)
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="worker-profile.css">

<!-- ================= PROFILE LAYOUT ================= -->
<main class="container" style="min-height: 80vh;">
  <div class="profile-layout">
    
    <!-- LEFT CONTENT AREA (70%) -->
    <section class="main-profile-content">
      <!-- Profile Header Banner -->
      <div class="profile-hero">
        <div class="cover-banner" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); height: 160px; border-radius: var(--radius-lg) var(--radius-lg) 0 0;"></div>
        <div class="header-info-bar">
          <div class="avatar-wrapper">
            <img class="avatar-img" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=200&fit=crop" alt="Ramesh Kumar">
            <span class="status-dot status-online"></span>
          </div>
          
          <div class="profile-title-section">
            <h1 style="color: var(--dark-navy); font-weight: 700; margin-bottom: 4px;">Ramesh Kumar <span class="verified-badge" style="position: static; display: inline-flex; border: none; font-size: 13px; width: auto; height: auto; padding: 4px 8px; border-radius: 6px;"><i class="fa-solid fa-check"></i> Verified</span></h1>
            <p class="profession">Professional Electrician & AC Technician</p>
            
            <div class="meta-stats">
              <span><i class="fa-solid fa-star" style="color: #F59E0B;"></i> 4.9 (128 Reviews)</span>
              <span><i class="fa-solid fa-briefcase"></i> 5+ Years Experience</span>
              <span><i class="fa-solid fa-location-dot"></i> Pune, Maharashtra</span>
            </div>
          </div>

          <div class="action-buttons-group">
            <button class="btn-icon" id="fav-profile-btn" style="border: none;" title="Add to Favorites"><i class="fa-regular fa-heart"></i></button>
            <button class="btn-icon" style="border: none;" title="Share Profile"><i class="fa-solid fa-share-nodes"></i></button>
          </div>
        </div>
      </div>

      <!-- About Me Section -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 12px;">About Ramesh</h3>
        <p style="font-size: 14px; color: var(--secondary-text); line-height: 1.6; margin-bottom: 12px;">
          I am a certified, professional electrician with over 5 years of experience serving residential and commercial properties in Pune. I specialize in complete house wiring, smart home automation, AC servicing, and emergency electrical troubleshooting.
        </p>
        <p style="font-size: 14px; color: var(--secondary-text); line-height: 1.6;">
          I prioritize safety, precision, and customer satisfaction above all. All operations are completed strictly following the national electricity guidelines.
        </p>
      </div>

      <!-- Services Section -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 16px;">Services Offered</h3>
        <div class="services-list" style="display: flex; flex-direction: column; gap: 16px;">
          <div class="service-card" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            <div class="service-info">
              <h4 style="color: var(--dark-navy); margin-bottom: 4px;">Smart Home Automation & Wiring</h4>
              <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Complete wiring of devices, smart switches, and smart lights configuration.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
              <span style="font-weight: 700; color: var(--primary);">₹399/hr</span>
              <button class="btn-book">Select</button>
            </div>
          </div>

          <div class="service-card" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            <div class="service-info">
              <h4 style="color: var(--dark-navy); margin-bottom: 4px;">AC Installation & Basic Repair</h4>
              <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Mounting, copper piping installation, gas charging, and structural check.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
              <span style="font-weight: 700; color: var(--primary);">₹599/hr</span>
              <button class="btn-book">Select</button>
            </div>
          </div>

          <div class="service-card" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            <div class="service-info">
              <h4 style="color: var(--dark-navy); margin-bottom: 4px;">Emergency Troubleshooting</h4>
              <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Finding short circuits, voltage issues, and fuse replacements.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
              <span style="font-weight: 700; color: var(--primary);">₹299/hr</span>
              <button class="btn-book">Select</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Work Gallery Grid -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 16px;">Work Portfolio</h3>
        <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px;">
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=300" alt="Work 1" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=300" alt="Work 2" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?w=300" alt="Work 3" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=300" alt="Work 4" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 16px;">Ratings & Reviews</h3>
        <div class="review-summary" style="display: flex; gap: 32px; align-items: center; margin-bottom: 24px; flex-wrap: wrap;">
          <div class="rating-big" style="font-size: 42px; font-weight: 800; color: var(--primary-text); line-height: 1;">
            4.9
            <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 0;">out of 5.0</p>
          </div>
          
          <div class="rating-breakdown" style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
            <div class="breakdown-row" style="display: flex; align-items: center; gap: 12px; font-size: 13px;">
              <span>5 Stars</span>
              <div class="progress-bar-bg" style="flex: 1; height: 6px; background: var(--border-color); border-radius: 3px;"><div class="progress-bar-fill" style="width: 90%; height: 100%; background: #F59E0B; border-radius: 3px;"></div></div>
              <span>90%</span>
            </div>
            <div class="breakdown-row" style="display: flex; align-items: center; gap: 12px; font-size: 13px;">
              <span>4 Stars</span>
              <div class="progress-bar-bg" style="flex: 1; height: 6px; background: var(--border-color); border-radius: 3px;"><div class="progress-bar-fill" style="width: 8%; height: 100%; background: #F59E0B; border-radius: 3px;"></div></div>
              <span>8%</span>
            </div>
            <div class="breakdown-row" style="display: flex; align-items: center; gap: 12px; font-size: 13px;">
              <span>3 Stars</span>
              <div class="progress-bar-bg" style="flex: 1; height: 6px; background: var(--border-color); border-radius: 3px;"><div class="progress-bar-fill" style="width: 2%; height: 100%; background: #F59E0B; border-radius: 3px;"></div></div>
              <span>2%</span>
            </div>
          </div>
        </div>

        <div class="reviews-list" style="display: flex; flex-direction: column; gap: 16px;">
          <div class="review-card" style="padding: 16px; border-bottom: 1px solid var(--border-color);">
            <div class="review-header" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span class="review-author" style="font-weight: 600; color: var(--dark-navy);">Aniket Rachalwar</span>
              <span class="review-date" style="font-size: 12px; color: var(--secondary-text);">July 15, 2026</span>
            </div>
            <div style="color: #F59E0B; margin-bottom: 8px; font-size: 14px;">★★★★★</div>
            <p style="font-size: 14px; color: var(--secondary-text); line-height: 1.6;">Ramesh was excellent! He arrived right on time, diagnosed the short circuit in my living room within ten minutes, and fixed it safely. Will definitely book again.</p>
          </div>

          <div class="review-card" style="padding: 16px;">
            <div class="review-header" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span class="review-author" style="font-weight: 600; color: var(--dark-navy);">Sunita Patil</span>
              <span class="review-date" style="font-size: 12px; color: var(--secondary-text);">June 28, 2026</span>
            </div>
            <div style="color: #F59E0B; margin-bottom: 8px; font-size: 14px;">★★★★★</div>
            <p style="font-size: 14px; color: var(--secondary-text); line-height: 1.6;">Very polite and highly professional. Did a clean job mounting the AC and cabling.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- RIGHT SIDEBAR (30%) - STICKY BOOKING CARD -->
    <aside class="sticky-booking-sidebar">
      <div class="booking-sidebar-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div class="sidebar-price-row">
          <div class="sidebar-price">
            Starting from
            <strong>₹299/hr</strong>
          </div>
        </div>

        <div class="availability-banner" style="display: flex; align-items: center; gap: 8px; background: rgba(34, 197, 94, 0.08); color: var(--success); padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; margin-bottom: 20px;">
          <i class="fa-solid fa-circle-check"></i>
          <span>Available Today (Pune)</span>
        </div>

        <button class="btn-primary-auth" style="width: 100%; margin-bottom: 12px; height: 50px;" onclick="location.href='booking.php'">
          <span>Book Now</span>
          <i class="fa-solid fa-calendar-days"></i>
        </button>
        
        <button class="btn-google-auth" id="chat-worker-btn" style="width: 100%; margin-bottom: 12px; height: 50px; border-color: var(--primary); color: var(--primary);">
          <i class="fa-regular fa-comment-dots"></i>
          <span>Chat with Ramesh</span>
        </button>

        <button class="btn-google-auth" id="call-worker-btn" style="width: 100%; height: 50px;">
          <i class="fa-solid fa-phone"></i>
          <span>Call Worker</span>
        </button>
      </div>
    </aside>

  </div>
</main>

<script src="worker-profile.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
