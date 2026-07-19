<?php
/**
 * Shared Footer
 */
?>
<footer>
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-briefcase"></i> Go<span>Worker</span>
            </a>
            <p>Your trusted marketplace for local services. Connect with verified service workers in your neighborhood, negotiate pricing, and get work done easily.</p>
        </div>
        
        <div>
            <h4 class="footer-heading">Quick Links</h4>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="find-workers.php">Find Workers</a></li>
                <li><a href="become-worker.php">Become a Worker</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            </ul>
        </div>
        
        <div>
            <h4 class="footer-heading">Popular Services</h4>
            <ul class="footer-links">
                <li><a href="find-workers.php?category=1">Electricians</a></li>
                <li><a href="find-workers.php?category=2">Plumbers</a></li>
                <li><a href="find-workers.php?category=3">Carpenters</a></li>
                <li><a href="find-workers.php?category=5">Cleaners</a></li>
                <li><a href="find-workers.php?category=6">Appliance Repair</a></li>
            </ul>
        </div>
        
        <div>
            <h4 class="footer-heading">Contact Us</h4>
            <ul class="footer-links" style="color: var(--text-muted); font-size: 0.95rem;">
                <li style="margin-bottom: 0.75rem;"><i class="fa-solid fa-location-dot" style="margin-right: 0.5rem; color: var(--primary);"></i> 123 College Campus, City</li>
                <li style="margin-bottom: 0.75rem;"><i class="fa-solid fa-phone" style="margin-right: 0.5rem; color: var(--primary);"></i> +1 (234) 567-8900</li>
                <li style="margin-bottom: 0.75rem;"><i class="fa-solid fa-envelope" style="margin-right: 0.5rem; color: var(--primary);"></i> support@goworker.com</li>
            </ul>
        </div>
    </div>
    
    <div class="container footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> GoWorker. All rights reserved. Developed as a college project.</p>
        <p>Phase 1 Foundation</p>
    </div>
</footer>

<!-- Global JS -->
<script src="js/main.js"></script>
</body>
</html>
