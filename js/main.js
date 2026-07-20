// GoWorker Client-side Interactive Logic

document.addEventListener('DOMContentLoaded', () => {
    console.log('GoWorker platform loaded.');

    // --- Mobile Slide-in Drawer Toggle ---
    const drawerToggle = document.getElementById('drawer-toggle');
    const drawerClose = document.getElementById('drawer-close');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const mobileDrawer = document.getElementById('mobile-drawer');

    if (drawerToggle && mobileDrawer && drawerOverlay) {
        // Open drawer
        drawerToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileDrawer.classList.add('open');
            drawerOverlay.classList.add('open');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });

        // Close drawer functions
        const closeMenu = () => {
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

    // --- Dynamic Intersection Observer Scroll Reveal ---
    const fadeElements = document.querySelectorAll('.fade-in-up-premium');
    if (fadeElements.length > 0) {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Trigger only once
                }
            });
        }, observerOptions);

        fadeElements.forEach(element => {
            observer.observe(element);
        });
    }
});
