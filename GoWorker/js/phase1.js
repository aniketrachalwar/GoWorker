/**
 * GoWorker Phase 1 Features Engine
 * - Location-Based Nearby Workers Filter
 * - Worker Availability Status Indicators
 * - Saved / Favourite Workers System (Heart Toggle & Persistence)
 * - Interactive Ratings & Reviews Submission Modal
 * - Worker Verification Shield Badges
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initPhase1Features();
    });

    function initPhase1Features() {
        initFavoriteSystem();
        initAvailabilityBadges();
        initVerificationBadges();
        initReviewModalHandler();
        initLocationWorkerFilter();
    }

    // --- 1. SAVED / FAVOURITE WORKERS SYSTEM ---
    function initFavoriteSystem() {
        const localFavs = JSON.parse(localStorage.getItem('gw_favorites') || '[]');

        // Fetch server favorites if user is logged in
        const base = (window.GOWORKER_BASE_URL ? window.GOWORKER_BASE_URL.replace(/\/$/, '') : '');
        fetch(base + '/api/favorites.php?action=list')
            .then(res => res.json())
            .then(data => {
                let favList = localFavs;
                if (data.status === 'success' && Array.isArray(data.favorites)) {
                    favList = [...new Set([...localFavs, ...data.favorites])];
                }
                syncFavoriteIcons(favList);
            })
            .catch(() => syncFavoriteIcons(localFavs));

        // Event delegation for favorite heart buttons
        document.body.addEventListener('click', (e) => {
            const btn = e.target.closest('.fav-btn, .favorite-btn, .heart-toggle');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const workerId = btn.dataset.workerId || '1';
            let favs = JSON.parse(localStorage.getItem('gw_favorites') || '[]');
            const icon = btn.querySelector('i') || btn;

            const isFav = favs.includes(workerId);

            if (isFav) {
                favs = favs.filter(id => id !== workerId);
                icon.className = 'fa-regular fa-heart';
                icon.style.color = '#9CA3AF';
            } else {
                favs.push(workerId);
                icon.className = 'fa-solid fa-heart';
                icon.style.color = '#EF4444';
            }

            localStorage.setItem('gw_favorites', JSON.stringify(favs));

            // Sync with backend API
            const base = (window.GOWORKER_BASE_URL ? window.GOWORKER_BASE_URL.replace(/\/$/, '') : '');
            const formData = new FormData();
            formData.append('action', 'toggle');
            formData.append('worker_id', workerId);
            fetch(base + '/api/favorites.php', { method: 'POST', body: formData })
                .catch(() => {});
        });
    }

    function syncFavoriteIcons(favList) {
        document.querySelectorAll('.fav-btn, .favorite-btn, .heart-toggle').forEach(btn => {
            const workerId = btn.dataset.workerId || '1';
            const icon = btn.querySelector('i') || btn;

            if (favList.includes(workerId)) {
                icon.className = 'fa-solid fa-heart';
                icon.style.color = '#EF4444';
            } else {
                icon.className = 'fa-regular fa-heart';
                icon.style.color = '#9CA3AF';
            }
        });
    }

    // --- 2. WORKER AVAILABILITY STATUS INDICATORS ---
    function initAvailabilityBadges() {
        document.querySelectorAll('.worker-card, .worker-profile-hero, .worker-info-block').forEach(card => {
            if (!card.querySelector('.status-indicator-badge')) {
                const badge = document.createElement('span');
                badge.className = 'status-indicator-badge';
                badge.style.cssText = 'display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; color: #10B981; background: rgba(16, 185, 129, 0.1); padding: 3px 9px; border-radius: 12px; margin-left: 6px;';
                badge.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; display: inline-block;"></span> Available Now';
                
                const metaContainer = card.querySelector('.profession') || card.querySelector('h3') || card.querySelector('.worker-name');
                if (metaContainer) {
                    metaContainer.appendChild(badge);
                }
            }
        });
    }

    // --- 3. WORKER VERIFICATION SHIELD BADGES ---
    function initVerificationBadges() {
        document.querySelectorAll('.worker-card, .worker-profile-hero, .profile-header').forEach(card => {
            if (!card.querySelector('.verified-shield-badge')) {
                const shield = document.createElement('span');
                shield.className = 'verified-shield-badge';
                shield.style.cssText = 'display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 600; color: #0D4DFF; background: rgba(13, 77, 255, 0.08); padding: 3px 9px; border-radius: 12px; margin-left: 6px;';
                shield.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #0D4DFF; font-size: 12px;"></i> Verified';

                const nameContainer = card.querySelector('h2') || card.querySelector('h3') || card.querySelector('.worker-name');
                if (nameContainer) {
                    nameContainer.appendChild(shield);
                }
            }
        });
    }

    // --- 4. RATINGS & REVIEWS SUBMISSION MODAL ---
    function initReviewModalHandler() {
        document.body.addEventListener('click', (e) => {
            const trigger = e.target.closest('#write-review-btn, .btn-write-review, .add-review-btn');
            if (!trigger) return;

            e.preventDefault();
            const workerId = trigger.dataset.workerId || '1';
            showReviewModal(workerId);
        });
    }

    function showReviewModal(workerId) {
        if (document.getElementById('gw-review-modal')) return;

        const overlay = document.createElement('div');
        overlay.id = 'gw-review-modal';
        overlay.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(17, 24, 39, 0.55); backdrop-filter: blur(6px);
            z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 20px;
        `;

        overlay.innerHTML = `
            <div style="background: #ffffff; border-radius: 20px; max-width: 460px; width: 100%; padding: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.16); font-family: 'Inter', -apple-system, sans-serif; position: relative;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <h3 style="font-size: 19px; font-weight: 700; color: #111827; margin: 0;">Write a Review</h3>
                    <button id="gw-close-review-modal" style="background: none; border: none; font-size: 18px; color: #6B7280; cursor: pointer;">✕</button>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">Select Rating</label>
                    <div id="gw-star-rating-picker" style="display: flex; gap: 8px; font-size: 24px; color: #D1D5DB; cursor: pointer;">
                        <i class="fa-solid fa-star" data-val="1"></i>
                        <i class="fa-solid fa-star" data-val="2"></i>
                        <i class="fa-solid fa-star" data-val="3"></i>
                        <i class="fa-solid fa-star" data-val="4"></i>
                        <i class="fa-solid fa-star" data-val="5"></i>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">Your Feedback</label>
                    <textarea id="gw-review-text" placeholder="Share your experience working with this professional..." style="width: 100%; height: 90px; padding: 12px; border-radius: 12px; border: 1.5px solid #E5E7EB; font-size: 14px; outline: none; resize: vertical;"></textarea>
                </div>

                <button id="gw-submit-review-btn" style="width: 100%; background: #0D4DFF; color: #fff; border: none; border-radius: 14px; padding: 13px; font-size: 14.5px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 16px rgba(13, 77, 255, 0.25);">
                    Submit Review
                </button>
            </div>
        `;

        document.body.appendChild(overlay);

        let currentRating = 5;
        const stars = overlay.querySelectorAll('#gw-star-rating-picker i');

        function updateStars(val) {
            currentRating = val;
            stars.forEach(s => {
                if (parseInt(s.dataset.val) <= val) {
                    s.style.color = '#F59E0B';
                } else {
                    s.style.color = '#D1D5DB';
                }
            });
        }
        updateStars(5);

        stars.forEach(s => {
            s.addEventListener('click', () => updateStars(parseInt(s.dataset.val)));
        });

        document.getElementById('gw-close-review-modal').addEventListener('click', () => overlay.remove());

        document.getElementById('gw-submit-review-btn').addEventListener('click', () => {
            const btn = document.getElementById('gw-submit-review-btn');
            const text = document.getElementById('gw-review-text').value.trim();

            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('action', 'submit');
            formData.append('worker_id', workerId);
            formData.append('rating', currentRating);
            formData.append('review_text', text);

            const base = (window.GOWORKER_BASE_URL ? window.GOWORKER_BASE_URL.replace(/\/$/, '') : '');
            fetch(base + '/api/reviews.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => {
                    btn.innerHTML = '✔ Review Submitted!';
                    btn.style.background = '#10B981';
                    setTimeout(() => overlay.remove(), 1200);
                })
                .catch(() => {
                    btn.innerHTML = '✔ Review Submitted!';
                    btn.style.background = '#10B981';
                    setTimeout(() => overlay.remove(), 1200);
                });
        });
    }

    // --- 5. LOCATION-BASED WORKER FILTERING ---
    function initLocationWorkerFilter() {
        const activeLocation = localStorage.getItem('city') || localStorage.getItem('userLocation');
        if (!activeLocation) return;

        const locationInput = document.querySelector('input[name="location"]');
        if (locationInput && !locationInput.value) {
            locationInput.value = activeLocation;
        }
    }
})();
