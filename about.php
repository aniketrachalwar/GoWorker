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
    <h1 style="color: var(--white); font-size: 38px; margin-bottom: 12px;">About GoWorker</h1>
    <p style="color: rgba(255,255,255,0.85); font-size: 16px; max-width: 600px; margin: 0 auto;">We connect skilled professionals with local house owners to complete household tasks seamlessly.</p>
  </div>
</section>

<!-- ================= ABOUT BODY ================= -->
<main class="container" style="margin-top: 60px;">
  <div class="card" style="padding: 40px; margin-bottom: 40px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--white);">
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;">Our Mission</h2>
    <p style="font-size: 15px; color: var(--secondary-text); line-height: 1.7; margin-bottom: 24px;">
      At GoWorker, our mission is to empower local skilled professionals by providing them with a reliable source of job requests and earnings, while offering homeowners a hassle-free, secure, and instant method to hire verified specialists.
    </p>
    
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;">Why Choose Us?</h2>
    <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 15px; color: var(--secondary-text); padding-left: 0;">
      <li><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> <strong>100% Verified Partners:</strong> Every worker goes through background check and ID verification.</li>
      <li><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> <strong>Transparent Rates:</strong> Discuss service charges hourly or fixed rate directly before booking.</li>
      <li><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> <strong>Secure Settlements:</strong> Digital transactions and safe wallet payout settlements.</li>
    </ul>
  </div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
