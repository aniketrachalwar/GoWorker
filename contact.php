<?php
/**
 * GoWorker - Contact Us Page
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ================= HERO ================= -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: var(--white); padding: 80px 0; text-align: center;">
  <div class="container">
    <h1 style="color: var(--white); font-size: 38px; margin-bottom: 12px;"><?php echo e(__('contact_support')); ?></h1>
    <p style="color: rgba(255,255,255,0.85); font-size: 16px; max-width: 600px; margin: 0 auto;"><?php echo e(__('contact_desc')); ?></p>
  </div>
</section>

<!-- ================= CONTACT FORM ================= -->
<main class="container" style="margin-top: 60px;">
  <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; align-items: start;">
    
    <!-- Left side contact info card -->
    <section class="card" style="padding: 40px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--white); box-shadow: var(--shadow-sm);">
      <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 24px;"><?php echo e(__('direct_connection')); ?></h2>
      
      <div style="display: flex; gap: 16px; margin-bottom: 24px; align-items: start;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa-solid fa-phone"></i></div>
        <div>
          <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 4px;"><?php echo e(__('phone_support')); ?></h4>
          <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">+91 98765 43210 (Mon-Sat, 9AM - 6PM)</p>
        </div>
      </div>

      <div style="display: flex; gap: 16px; margin-bottom: 24px; align-items: start;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa-solid fa-envelope"></i></div>
        <div>
          <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 4px;"><?php echo e(__('email_desk')); ?></h4>
          <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">support@goworker.com</p>
        </div>
      </div>

      <div style="display: flex; gap: 16px; align-items: start;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; justify-content: center; align-items: center; font-size: 18px;"><i class="fa-solid fa-location-dot"></i></div>
        <div>
          <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 4px;"><?php echo e(__('head_office')); ?></h4>
          <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">102, Pride Silicon Plaza, Senapati Bapat Road, Pune, Maharashtra - 411016</p>
        </div>
      </div>
    </section>

    <!-- Right side form card -->
    <section class="card" style="padding: 40px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--white); box-shadow: var(--shadow-sm);">
      <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 24px;"><?php echo e(__('send_message')); ?></h2>
      <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Message sent successfully! Our customer support desk will reach back to you shortly.'); this.reset();">
        <div class="form-group" style="margin-bottom: 20px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;"><?php echo e(__('full_name')); ?></label>
          <input type="text" class="form-input" style="padding-left: 16px;" placeholder="<?php echo e(__('fullname_placeholder')); ?>" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;"><?php echo e(__('email_address')); ?></label>
          <input type="email" class="form-input" style="padding-left: 16px;" placeholder="<?php echo e(__('email_address_placeholder')); ?>" required>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;"><?php echo e(__('send_message')); ?></label>
          <textarea class="form-input" style="padding-left: 16px; padding-top: 12px; min-height: 120px; resize: vertical;" placeholder="Type your concerns here..." required></textarea>
        </div>

        <button type="submit" class="btn-primary-auth" style="width: 100%; height: 50px;">
          <span><?php echo e(__('submit_message')); ?></span>
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </form>
    </section>
    
  </div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
