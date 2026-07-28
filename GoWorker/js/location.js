/**
 * GoWorker Urban Company Style Location System
 * Features:
 * - GPS Auto-Detection via Geolocation & OpenStreetMap Nominatim Reverse Geocoding
 * - Urban Company Permission Modal ("Enable your location to find nearby workers and services")
 * - "Use Current Location" GPS trigger
 * - Searchable City Selector Modal with Popular Indian Cities (Mumbai, Pune, Delhi, Bengaluru, etc.)
 * - Header Location Selector Trigger ("📍 City ▾") for updating city anytime
 * - Auto-Fill across Homepage, Find Workers, Dashboard, and Booking Forms
 */

(function () {
    'use strict';

    const STORAGE_CITY_KEY = 'city';
    const STORAGE_STATE_KEY = 'state';
    const STORAGE_COUNTRY_KEY = 'country';
    const STORAGE_LAT_KEY = 'latitude';
    const STORAGE_LON_KEY = 'longitude';
    const STORAGE_LOC_KEY = 'userLocation';
    const STORAGE_CHOICE_KEY = 'locationChoiceMade';

    const POPULAR_CITIES = [
        { name: 'Mumbai', state: 'Maharashtra', lat: 19.0760, lon: 72.8777 },
        { name: 'Pune', state: 'Maharashtra', lat: 18.5204, lon: 73.8567 },
        { name: 'Delhi', state: 'Delhi', lat: 28.6139, lon: 77.2090 },
        { name: 'Bengaluru', state: 'Karnataka', lat: 12.9716, lon: 77.5946 },
        { name: 'Hyderabad', state: 'Telangana', lat: 17.3850, lon: 78.4867 },
        { name: 'Chennai', state: 'Tamil Nadu', lat: 13.0827, lon: 80.2707 },
        { name: 'Kolkata', state: 'West Bengal', lat: 22.5726, lon: 88.3639 },
        { name: 'Ahmedabad', state: 'Gujarat', lat: 23.0225, lon: 72.5714 },
        { name: 'Surat', state: 'Gujarat', lat: 21.1702, lon: 72.8311 },
        { name: 'Nagpur', state: 'Maharashtra', lat: 21.1458, lon: 79.0882 }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        initUrbanLocationSystem();
    });

    function initUrbanLocationSystem() {
        injectLocationStyles();
        setupHeaderLocationPill();

        const savedCity = localStorage.getItem(STORAGE_CITY_KEY) || localStorage.getItem(STORAGE_LOC_KEY);

        if (savedCity) {
            updateHeaderCityDisplay(savedCity);
            autoFillLocationInputs(savedCity);
        }

        bindLocationInputEvents();

        const choiceMade = localStorage.getItem(STORAGE_CHOICE_KEY);
        if (!savedCity && !choiceMade) {
            setTimeout(showPermissionModal, 1000);
        }
    }

    // --- Inject Styles ---
    function injectLocationStyles() {
        if (document.getElementById('gw-uc-loc-styles')) return;
        const style = document.createElement('style');
        style.id = 'gw-uc-loc-styles';
        style.innerHTML = `
            @keyframes gwModalFadeUp {
                from { opacity: 0; transform: translateY(24px) scale(0.98); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            .header-loc-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                background: rgba(13, 77, 255, 0.08);
                border: 1px solid rgba(13, 77, 255, 0.18);
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                color: #0D4DFF;
                cursor: pointer;
                transition: all 0.25s ease;
                user-select: none;
            }
            .header-loc-pill:hover {
                background: #0D4DFF;
                color: #ffffff;
                box-shadow: 0 4px 14px rgba(13, 77, 255, 0.25);
            }
            .gw-uc-overlay {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(17, 24, 39, 0.55);
                backdrop-filter: blur(6px);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .gw-uc-card {
                background: #ffffff;
                border-radius: 20px;
                max-width: 440px;
                width: 100%;
                padding: 28px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.16);
                animation: gwModalFadeUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                position: relative;
            }
            .gw-btn-primary-uc {
                width: 100%;
                background: #0D4DFF;
                color: #ffffff;
                border: none;
                border-radius: 14px;
                padding: 13px 20px;
                font-size: 14.5px;
                font-weight: 600;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                box-shadow: 0 4px 16px rgba(13, 77, 255, 0.25);
            }
            .gw-btn-primary-uc:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(13, 77, 255, 0.35);
            }
            .gw-btn-secondary-uc {
                width: 100%;
                background: #F8FAFF;
                color: #111827;
                border: 1.5px solid #E5E7EB;
                border-radius: 14px;
                padding: 13px 20px;
                font-size: 14.5px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 10px;
                transition: background-color 0.2s ease, border-color 0.2s ease;
            }
            .gw-btn-secondary-uc:hover {
                background: #EFF4FF;
                border-color: #0D4DFF;
                color: #0D4DFF;
            }
            .gw-city-chip {
                display: inline-flex;
                align-items: center;
                padding: 8px 14px;
                background: #F3F4F6;
                border: 1px solid #E5E7EB;
                border-radius: 12px;
                font-size: 13px;
                font-weight: 600;
                color: #374151;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .gw-city-chip:hover {
                background: #0D4DFF;
                color: #ffffff;
                border-color: #0D4DFF;
            }
        `;
        document.head.appendChild(style);
    }

    // --- Header Location Display / Change Location Trigger ---
    function setupHeaderLocationPill() {
        const logoDiv = document.querySelector('.navbar .logo') || document.querySelector('.logo');
        if (logoDiv && !document.getElementById('header-location-trigger')) {
            const pill = document.createElement('div');
            pill.id = 'header-location-trigger';
            pill.className = 'header-loc-pill';
            pill.title = 'Click to change location';

            const current = localStorage.getItem(STORAGE_CITY_KEY) || localStorage.getItem(STORAGE_LOC_KEY) || 'Select Location';

            pill.innerHTML = `
                <i class="fa-solid fa-location-dot"></i>
                <span id="header-city-display">${current}</span>
                <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
            `;

            // Insert next to logo
            logoDiv.after(pill);

            pill.addEventListener('click', showCitySelectorModal);
        }
    }

    function updateHeaderCityDisplay(cityName) {
        const span = document.getElementById('header-city-display');
        if (span) {
            span.textContent = cityName;
        }
    }

    // --- 1. Initial Permission Modal (Urban Company Style) ---
    function showPermissionModal() {
        if (document.getElementById('gw-permission-modal')) return;

        const overlay = document.createElement('div');
        overlay.id = 'gw-permission-modal';
        overlay.className = 'gw-uc-overlay';

        overlay.innerHTML = `
            <div class="gw-uc-card">
                <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(13, 77, 255, 0.10); color: #0D4DFF; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
                    <i class="fa-solid fa-location-crosshairs" style="font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">Select Location</h3>
                <p style="font-size: 14px; color: #4B5563; margin: 0 0 24px 0; line-height: 1.5;">Enable your location to find nearby workers and services.</p>

                <button id="gw-use-current-btn" class="gw-btn-primary-uc">
                    <i class="fa-solid fa-crosshairs"></i> Use Current Location
                </button>
                <button id="gw-select-manual-btn" class="gw-btn-secondary-uc">
                    Select Manually
                </button>
            </div>
        `;

        document.body.appendChild(overlay);

        document.getElementById('gw-use-current-btn').addEventListener('click', handleUseCurrentLocation);
        document.getElementById('gw-select-manual-btn').addEventListener('click', () => {
            overlay.remove();
            showCitySelectorModal();
        });
    }

    // --- 2. "Use Current Location" GPS & Reverse Geocoding ---
    function handleUseCurrentLocation() {
        const btn = document.getElementById('gw-use-current-btn');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting location...';
            btn.disabled = true;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                    .then(res => res.json())
                    .then(data => {
                        let city = '';
                        let state = '';
                        let country = 'India';

                        if (data && data.address) {
                            city = data.address.city || data.address.town || data.address.village || data.address.suburb || data.address.county || data.address.state_district || '';
                            state = data.address.state || '';
                            country = data.address.country || 'India';
                        }

                        let formatted = city;
                        if (city && state && city.toLowerCase() !== state.toLowerCase()) {
                            formatted = `${city}, ${state}`;
                        } else if (!city && state) {
                            formatted = state;
                        }

                        if (!formatted) formatted = 'Pune, Maharashtra';

                        saveLocationToStorage({
                            city: city || formatted,
                            state: state,
                            country: country,
                            latitude: lat,
                            longitude: lon,
                            userLocation: formatted
                        });

                        removeModal('gw-permission-modal');
                    })
                    .catch(err => {
                        console.warn('Geocoding query fallback:', err);
                        saveLocationToStorage({
                            city: 'Pune',
                            state: 'Maharashtra',
                            country: 'India',
                            latitude: lat,
                            longitude: lon,
                            userLocation: 'Pune, Maharashtra'
                        });
                        removeModal('gw-permission-modal');
                    });
            },
            (err) => {
                console.warn('GPS position access denied:', err);
                removeModal('gw-permission-modal');
                showCitySelectorModal();
            },
            { timeout: 10000, maximumAge: 60000 }
        );
    }

    // --- 3. Searchable City Selector Modal ---
    function showCitySelectorModal() {
        if (document.getElementById('gw-city-modal')) return;

        const overlay = document.createElement('div');
        overlay.id = 'gw-city-modal';
        overlay.className = 'gw-uc-overlay';

        let popularChipsHtml = POPULAR_CITIES.map(c => `
            <button class="gw-city-chip" data-name="${c.name}" data-state="${c.state}" data-lat="${c.lat}" data-lon="${c.lon}">
                ${c.name}
            </button>
        `).join('');

        overlay.innerHTML = `
            <div class="gw-uc-card" style="max-width: 480px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <h3 style="font-size: 19px; font-weight: 700; color: #111827; margin: 0;">Select Your City</h3>
                    <button id="gw-close-city-modal" style="background: none; border: none; font-size: 18px; color: #6B7280; cursor: pointer;">✕</button>
                </div>

                <!-- Search input -->
                <div style="position: relative; margin-bottom: 20px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF;"></i>
                    <input type="text" id="gw-city-search-input" placeholder="Search city..." style="width: 100%; padding: 12px 14px 12px 40px; border-radius: 12px; border: 1.5px solid #E5E7EB; font-size: 14px; outline: none;">
                </div>

                <div style="font-size: 12px; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Popular Cities</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px; max-height: 220px; overflow-y: auto; margin-bottom: 20px;">
                    ${popularChipsHtml}
                </div>

                <button id="gw-use-gps-trigger" class="gw-btn-primary-uc" style="background: rgba(13, 77, 255, 0.08); color: #0D4DFF; border: 1px solid rgba(13, 77, 255, 0.2); box-shadow: none;">
                    <i class="fa-solid fa-crosshairs"></i> Auto-Detect My Location
                </button>
            </div>
        `;

        document.body.appendChild(overlay);

        document.getElementById('gw-close-city-modal').addEventListener('click', () => removeModal('gw-city-modal'));
        document.getElementById('gw-use-gps-trigger').addEventListener('click', () => {
            removeModal('gw-city-modal');
            showPermissionModal();
        });

        // City chip click events
        document.querySelectorAll('#gw-city-modal .gw-city-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const name = chip.dataset.name;
                const state = chip.dataset.state;
                const lat = chip.dataset.lat;
                const lon = chip.dataset.lon;

                saveLocationToStorage({
                    city: name,
                    state: state,
                    country: 'India',
                    latitude: lat,
                    longitude: lon,
                    userLocation: `${name}, ${state}`
                });

                removeModal('gw-city-modal');
            });
        });

        // Live city search filtering
        const searchInput = document.getElementById('gw-city-search-input');
        if (searchInput) {
            searchInput.focus();
            searchInput.addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase().trim();
                document.querySelectorAll('#gw-city-modal .gw-city-chip').forEach(chip => {
                    const cityName = chip.dataset.name.toLowerCase();
                    if (cityName.includes(q)) {
                        chip.style.display = 'inline-flex';
                    } else {
                        chip.style.display = 'none';
                    }
                });
            });
        }
    }

    // --- Save & Update Helper ---
    function saveLocationToStorage(data) {
        localStorage.setItem(STORAGE_CITY_KEY, data.city);
        localStorage.setItem(STORAGE_STATE_KEY, data.state);
        localStorage.setItem(STORAGE_COUNTRY_KEY, data.country);
        localStorage.setItem(STORAGE_LAT_KEY, data.latitude);
        localStorage.setItem(STORAGE_LON_KEY, data.longitude);
        localStorage.setItem(STORAGE_LOC_KEY, data.userLocation);
        localStorage.setItem(STORAGE_CHOICE_KEY, 'done');

        updateHeaderCityDisplay(data.city);
        autoFillLocationInputs(data.userLocation);
    }

    function autoFillLocationInputs(locationText) {
        if (!locationText) return;
        const selectors = [
            'input[name="location"]',
            '#location-input',
            '#location-select',
            '.location-field',
            '.location-input',
            'input[placeholder*="Location" i]',
            'input[placeholder*="location" i]',
            'input[placeholder*="City" i]'
        ];

        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(input => {
                input.value = locationText;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    function bindLocationInputEvents() {
        const selectors = [
            'input[name="location"]',
            '#location-input',
            '#location-select',
            '.location-field',
            '.location-input'
        ];

        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(input => {
                input.addEventListener('change', (e) => {
                    const val = e.target.value.trim();
                    if (val) {
                        localStorage.setItem(STORAGE_CITY_KEY, val);
                        localStorage.setItem(STORAGE_LOC_KEY, val);
                        localStorage.setItem(STORAGE_CHOICE_KEY, 'manual');
                        updateHeaderCityDisplay(val);
                    }
                });
            });
        });
    }

    function removeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.opacity = '0';
            el.style.transition = 'opacity 0.25s ease';
            setTimeout(() => el.remove(), 250);
        }
    }
})();
