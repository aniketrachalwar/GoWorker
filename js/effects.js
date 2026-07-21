/**
 * GoWorker Advanced Visual Effects Engine
 * 60FPS Interactive Particles, Cursor Trail, Spotlight, Magnetic Buttons & Parallax
 * Zero structural, layout, backend, or functional modifications.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initPageFadeIn();
        initCanvasEffects();
        initCardSpotlight();
        initMagneticButtons();
        initMouseParallax();
        initFloatingBlobs();
    });

    // --- 1. PAGE FADE TRANSITION ---
    function initPageFadeIn() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.3s ease-in-out';
        requestAnimationFrame(() => {
            document.body.style.opacity = '1';
        });
    }

    // --- 2. CANVAS PARTICLES & CURSOR TRAIL ---
    function initCanvasEffects() {
        let canvas = document.getElementById('effects-canvas');
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = 'effects-canvas';
            canvas.style.position = 'fixed';
            canvas.style.top = '0';
            canvas.style.left = '0';
            canvas.style.width = '100vw';
            canvas.style.height = '100vh';
            canvas.style.pointerEvents = 'none';
            canvas.style.zIndex = '9999';
            document.body.appendChild(canvas);
        }

        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        const mouse = { x: -1000, y: -1000, active: false };
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
            mouse.active = true;
            spawnTrailParticle(e.clientX, e.clientY);
        });

        window.addEventListener('mouseleave', () => {
            mouse.active = false;
        });

        // Floating Particles Config
        const particleCount = 70;
        const colors = ['#0D4DFF', '#4A7BFF', '#8AB4FF'];
        const particles = [];

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.6,
                vy: (Math.random() - 0.5) * 0.6,
                radius: Math.random() * 2.5 + 1.2,
                color: colors[Math.floor(Math.random() * colors.length)],
                alpha: Math.random() * 0.4 + 0.2,
                baseAlpha: Math.random() * 0.4 + 0.2
            });
        }

        // Trail Particles Config
        const trailParticles = [];

        function spawnTrailParticle(x, y) {
            if (Math.random() > 0.4) return; // limit density for ultra-smooth 60fps
            trailParticles.push({
                x: x + (Math.random() - 0.5) * 6,
                y: y + (Math.random() - 0.5) * 6,
                vx: (Math.random() - 0.5) * 0.8,
                vy: (Math.random() - 0.5) * 0.8,
                radius: Math.random() * 3 + 2,
                color: colors[Math.floor(Math.random() * colors.length)],
                alpha: 0.75,
                decay: Math.random() * 0.03 + 0.02
            });
        }

        // Main Animation Loop
        function animate() {
            ctx.clearRect(0, 0, width, height);

            // Render Floating Background Particles
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];

                // Continuous drift
                p.x += p.vx;
                p.y += p.vy;

                // Screen bounce
                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                // Mouse attraction physics
                if (mouse.active) {
                    const dx = mouse.x - p.x;
                    const dy = mouse.y - p.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    const maxDist = 160;

                    if (dist < maxDist) {
                        const force = (1 - dist / maxDist) * 0.35;
                        p.x += (dx / dist) * force * 1.5;
                        p.y += (dy / dist) * force * 1.5;
                        p.alpha = Math.min(0.8, p.baseAlpha + force * 0.5);
                    } else {
                        p.alpha += (p.baseAlpha - p.alpha) * 0.05;
                    }
                }

                ctx.save();
                ctx.globalAlpha = p.alpha;
                ctx.fillStyle = p.color;
                ctx.shadowBlur = 8;
                ctx.shadowColor = p.color;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }

            // Render Mouse Trail Particles
            for (let i = trailParticles.length - 1; i >= 0; i--) {
                const tp = trailParticles[i];
                tp.x += tp.vx;
                tp.y += tp.vy;
                tp.alpha -= tp.decay;
                tp.radius = Math.max(0, tp.radius - 0.05);

                if (tp.alpha <= 0 || tp.radius <= 0) {
                    trailParticles.splice(i, 1);
                    continue;
                }

                ctx.save();
                ctx.globalAlpha = tp.alpha;
                ctx.fillStyle = tp.color;
                ctx.shadowBlur = 10;
                ctx.shadowColor = tp.color;
                ctx.beginPath();
                ctx.arc(tp.x, tp.y, tp.radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }

            requestAnimationFrame(animate);
        }

        requestAnimationFrame(animate);
    }

    // --- 3. CARD SPOTLIGHT GRADIENT TRACKER ---
    function initCardSpotlight() {
        const cards = document.querySelectorAll('.card, .worker-card, .service-card, .feature-card, .stat-card, .category-card, .dashboard-card, .step, .profile-card, .summary-card, .notif-card');

        cards.forEach(card => {
            card.classList.add('spotlight-card');
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    }

    // --- 4. MAGNETIC BUTTON ATTRACTOR ---
    function initMagneticButtons() {
        const buttons = document.querySelectorAll('.btn-primary, .signup-btn, .login-btn, .btn-secondary, .btn-outline, .btn');

        buttons.forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const btnCenterX = rect.left + rect.width / 2;
                const btnCenterY = rect.top + rect.height / 2;

                const distanceX = e.clientX - btnCenterX;
                const distanceY = e.clientY - btnCenterY;

                btn.style.transform = `translate3d(${distanceX * 0.22}px, ${distanceY * 0.22}px, 0) scale(1.02)`;
                btn.style.transition = 'transform 0.1s ease-out';
            });

            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translate3d(0, 0, 0) scale(1)';
                btn.style.transition = 'transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            });
        });
    }

    // --- 5. SUBTLE MOUSE PARALLAX ON IMAGES ---
    function initMouseParallax() {
        const parallaxTargets = document.querySelectorAll('.hero-right img, .avatar-img, .worker-card img, .illustration-img, .hero-content');

        if (parallaxTargets.length === 0) return;

        let mouseX = 0;
        let mouseY = 0;
        let targetX = 0;
        let targetY = 0;

        window.addEventListener('mousemove', (e) => {
            targetX = (e.clientX / window.innerWidth - 0.5) * 16;
            targetY = (e.clientY / window.innerHeight - 0.5) * 16;
        });

        function renderParallax() {
            mouseX += (targetX - mouseX) * 0.08;
            mouseY += (targetY - mouseY) * 0.08;

            parallaxTargets.forEach(target => {
                target.style.transform = `translate3d(${mouseX}px, ${mouseY}px, 0)`;
                target.style.willChange = 'transform';
            });

            requestAnimationFrame(renderParallax);
        }

        requestAnimationFrame(renderParallax);
    }

    // --- 6. FLOATING BACKGROUND BLOBS ---
    function initFloatingBlobs() {
        if (document.getElementById('ambient-blobs')) return;

        const blobContainer = document.createElement('div');
        blobContainer.id = 'ambient-blobs';
        blobContainer.style.position = 'fixed';
        blobContainer.style.top = '0';
        blobContainer.style.left = '0';
        blobContainer.style.width = '100vw';
        blobContainer.style.height = '100vh';
        blobContainer.style.pointerEvents = 'none';
        blobContainer.style.zIndex = '-1';
        blobContainer.style.overflow = 'hidden';

        const blob1 = document.createElement('div');
        blob1.className = 'ambient-blob blob-1';

        const blob2 = document.createElement('div');
        blob2.className = 'ambient-blob blob-2';

        blobContainer.appendChild(blob1);
        blobContainer.appendChild(blob2);
        document.body.appendChild(blobContainer);
    }
})();
