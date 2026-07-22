// Auto-load Visual Effects Engine if not already present
if (!document.querySelector('script[src*="effects.js"]')) {
    const effectsScript = document.createElement('script');
    effectsScript.src = 'js/effects.js';
    document.head.appendChild(effectsScript);
}

// Auto-load Location Auto-Detection Engine if not already present
if (!document.querySelector('script[src*="location.js"]')) {
    const locScript = document.createElement('script');
    locScript.src = 'js/location.js';
    document.head.appendChild(locScript);
}

// Auto-load Phase 1 Engine if not already present
if (!document.querySelector('script[src*="phase1.js"]')) {
    const p1Script = document.createElement('script');
    p1Script.src = 'js/phase1.js';
    document.head.appendChild(p1Script);
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('GoWorker platform loaded.');

    // --- Mobile Slide-in Drawer Toggle ---
    const drawerToggle = document.getElementById('drawer-toggle') || document.querySelector('.hamburger-menu-btn');
    const drawerClose = document.getElementById('drawer-close') || document.querySelector('.mobile-sidebar-close-btn');
    const drawerOverlay = document.getElementById('drawer-overlay') || document.querySelector('.mobile-sidebar-overlay');
    const mobileDrawer = document.getElementById('mobile-drawer') || document.querySelector('.mobile-sidebar-menu');

    if (drawerToggle && mobileDrawer && drawerOverlay) {
        // Open drawer
        drawerToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            mobileDrawer.classList.add('open');
            drawerOverlay.classList.add('open');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });

        // Close drawer functions
        const closeMenu = (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            mobileDrawer.classList.remove('open');
            drawerOverlay.classList.remove('open');
            document.body.style.overflow = ''; // Restore scrolling
        };

        if (drawerClose) {
            drawerClose.addEventListener('click', closeMenu);
        }
        drawerOverlay.addEventListener('click', closeMenu);

        // Escape key accessibility to close mobile drawer
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileDrawer.classList.contains('open')) {
                closeMenu();
            }
        });
    }

    // --- Automatic System Theme Synchronizer ---
    const htmlElement = document.documentElement;
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)');

    function applySystemTheme(e) {
        const isDark = e.matches;
        if (isDark) {
            htmlElement.setAttribute('data-theme', 'dark');
        } else {
            htmlElement.setAttribute('data-theme', 'light');
        }
    }

    // Apply operating system default theme initially
    applySystemTheme(systemPrefersDark);

    // Keep theme in sync dynamically if OS preference changes
    if (systemPrefersDark.addEventListener) {
        systemPrefersDark.addEventListener('change', applySystemTheme);
    } else if (systemPrefersDark.addListener) {
        systemPrefersDark.addListener(applySystemTheme);
    }

    // --- Dynamic Intersection Observer Scroll Reveal for All Cards & Sections ---
    const scrollRevealTargets = document.querySelectorAll('.card, .worker-card, .service-card, .feature-card, .step, .category-card, .stat-card, .section, .fade-in-up-premium');
    if (scrollRevealTargets.length > 0) {
        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1
        };

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible', 'fade-up-scroll');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        scrollRevealTargets.forEach(element => {
            revealObserver.observe(element);
        });
    }

    // --- Interactive Ripple Effect for Primary Buttons ---
    const primaryButtons = document.querySelectorAll('.btn-primary, .signup-btn, button[type="submit"]');
    primaryButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            const circle = document.createElement('span');
            const diameter = Math.max(this.clientWidth, this.clientHeight);
            const radius = diameter / 2;
            const rect = this.getBoundingClientRect();

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${e.clientX - rect.left - radius}px`;
            circle.style.top = `${e.clientY - rect.top - radius}px`;
            circle.style.position = 'absolute';
            circle.style.borderRadius = '50%';
            circle.style.background = 'rgba(255, 255, 255, 0.4)';
            circle.style.transform = 'scale(0)';
            circle.style.animation = 'ripple 0.6s linear';
            circle.style.pointerEvents = 'none';

            const rippleStyle = document.getElementById('ripple-style');
            if (!rippleStyle) {
                const style = document.createElement('style');
                style.id = 'ripple-style';
                style.innerHTML = `@keyframes ripple { to { transform: scale(4); opacity: 0; } }`;
                document.head.appendChild(style);
            }

            const existingRipple = this.querySelector('span.ripple-effect');
            if (existingRipple) {
                existingRipple.remove();
            }

            circle.classList.add('ripple-effect');
            this.appendChild(circle);
        });
    });
});

