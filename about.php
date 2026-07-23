<?php
/**
 * GoWorker - About Us Page
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ================= ABOUT HERO ================= -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: var(--white); padding: 80px 0; text-align: center;">
  <div class="container">
    <h1 style="color: var(--white); font-size: 38px; margin-bottom: 12px;"><?php echo e(__('about_goworker')); ?></h1>
    <p style="color: rgba(255,255,255,0.85); font-size: 16px; max-width: 600px; margin: 0 auto;"><?php echo e(__('about_tagline')); ?></p>
  </div>
</section>

<!-- ================= ABOUT BODY ================= -->
<main class="container" style="margin-top: 60px;">
  <div class="card" style="padding: 40px; margin-bottom: 40px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--white);">
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;"><?php echo e(__('our_mission')); ?></h2>
    <p style="font-size: 15px; color: var(--secondary-text); line-height: 1.7; margin-bottom: 24px;">
      <?php echo e(__('mission_desc')); ?>
    </p>
    
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;"><?php echo e(__('why_choose_us')); ?></h2>
    <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 15px; color: var(--secondary-text); padding-left: 0;">
      <li><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> <strong><?php echo e(__('verified_partners')); ?></strong> <?php echo e(__('verified_partners_desc')); ?></li>
      <li><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> <strong><?php echo e(__('transparent_rates')); ?></strong> <?php echo e(__('transparent_rates_desc')); ?></li>
      <li><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> <strong><?php echo e(__('secure_settlements')); ?></strong> <?php echo e(__('secure_settlements_desc')); ?></li>
    </ul>
  </div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
