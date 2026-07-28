/**
 * GoWorker Clean & Refined Script Handler
 * Lightweight, 0.4s Smooth Page Fade-In & CSS Micro-Interactions
 * Zero Canvas, Zero Particle Engine, Zero Heavy RAF Loops.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initPageFadeIn();
    });

    // --- 1. SMOOTH PAGE FADE-IN (0.4s) ---
    function initPageFadeIn() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.4s ease-in-out';
        requestAnimationFrame(() => {
            document.body.style.opacity = '1';
        });
    }
})();
