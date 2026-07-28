document.addEventListener("DOMContentLoaded", () => {
    const workersList = document.getElementById("workers-list");
    const skeletons = document.getElementById("skeletons");
    const emptyState = document.getElementById("empty-state");
    const searchInputs = document.querySelectorAll(".marketplace-search-bar input");
    const searchBtn = document.querySelector(".marketplace-search-bar button");
    const categoryGroup = document.querySelector(".filter-group");
    const sortSelect = document.querySelector(".sort-select");
    const toolbarTitle = document.querySelector(".toolbar-left h3");

    let currentCategory = 0;
    let query = "";
    let location = "";

    // 1. Fetch and render workers
    function loadWorkers() {
        if (skeletons) skeletons.style.display = "grid";
        if (workersList) workersList.style.display = "none";
        if (emptyState) emptyState.style.display = "none";

        const url = `/api/workers/list?q=${encodeURIComponent(query)}&location=${encodeURIComponent(location)}&category=${currentCategory}`;
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (skeletons) skeletons.style.display = "none";
                if (workersList) workersList.style.display = "grid";

                if (data.status === 'success' && data.workers) {
                    let workers = data.workers;

                    // Update category filters list if not loaded yet
                    if (data.categories && categoryGroup && categoryGroup.childElementCount <= 2) {
                        renderCategoryFilters(data.categories);
                    }

                    // Handle Sorting
                    if (sortSelect) {
                        const val = sortSelect.value;
                        if (val.includes("Rating")) {
                            workers.sort((a, b) => parseFloat(b.rating_avg) - parseFloat(a.rating_avg));
                        } else if (val.includes("Experience")) {
                            workers.sort((a, b) => parseInt(b.experience_years) - parseInt(a.experience_years));
                        } else if (val.includes("Price")) {
                            workers.sort((a, b) => parseFloat(a.hourly_rate) - parseFloat(b.hourly_rate));
                        }
                    }

                    // Render cards
                    renderWorkerCards(workers);
                } else {
                    showEmpty();
                }
            })
            .catch(err => {
                console.error("Load workers error:", err);
                if (skeletons) skeletons.style.display = "none";
                showEmpty();
            });
    }

    function renderWorkerCards(workers) {
        if (!workersList) return;
        workersList.innerHTML = "";

        if (workers.length === 0) {
            showEmpty();
            return;
        }

        if (toolbarTitle) {
            toolbarTitle.textContent = `${workers.length} Professionals found`;
        }

        workers.forEach(w => {
            const card = document.createElement("article");
            card.className = "worker-card";

            const avatarUrl = w.profile_picture || 'images/avatar_placeholder.png';
            const rate = parseFloat(w.hourly_rate || '299.00').toFixed(0);
            const verified = w.verification_status === 'Verified' ? `<div class="verified-badge"><i class="fa-solid fa-check"></i></div>` : '';

            card.innerHTML = `
                <div class="worker-card-header" style="cursor: pointer;" onclick="location.href='/worker-profile.html?id=${w.id}'">
                  <div class="worker-avatar-container">
                    <img class="worker-avatar" src="${avatarUrl}" alt="${escapeHTML(w.worker_name)}">
                    ${verified}
                  </div>
                  <div class="worker-meta">
                    <h4>${escapeHTML(w.worker_name)}</h4>
                    <p>${escapeHTML(w.category_name)}</p>
                    <div class="rating-badge">
                      <i class="fa-solid fa-star"></i> ${w.rating_avg} <span>(${w.rating_count} reviews)</span>
                    </div>
                  </div>
                </div>
                
                <div class="worker-stats-row">
                  <div class="worker-stat">
                    <strong>${w.experience_years} Years</strong>
                    <span>Experience</span>
                  </div>
                  <div class="worker-stat">
                    <strong>${escapeHTML(w.location)}</strong>
                    <span>Location</span>
                  </div>
                </div>

                <div class="worker-skills">
                  ${(w.skills || '').split(',').map(s => `<span class="skill-tag">${escapeHTML(s.trim())}</span>`).join('')}
                </div>

                <div class="worker-card-footer">
                  <div class="price-tag">
                    Starting from
                    <strong>₹${rate}/hr</strong>
                  </div>
                  <div class="action-group">
                    <button class="btn-icon btn-chat-msg" data-uid="${w.user_id}" data-name="${escapeHTML(w.worker_name)}" data-avatar="${avatarUrl}" data-prof="${escapeHTML(w.category_name)}"><i class="fa-regular fa-comment-dots"></i></button>
                    <button class="btn-book" onclick="location.href='booking.html?id=${w.id}'">Book Now</button>
                  </div>
                </div>
            `;
            workersList.appendChild(card);
        });

        // Chat message click handlers
        document.querySelectorAll(".btn-chat-msg").forEach(btn => {
            btn.addEventListener("click", () => {
                const uid = btn.getAttribute("data-uid");
                const name = btn.getAttribute("data-name");
                const avatar = btn.getAttribute("data-avatar");
                const prof = btn.getAttribute("data-prof");
                window.location.href = `/messages?worker_id=${uid}&name=${encodeURIComponent(name)}&profession=${encodeURIComponent(prof)}&avatar=${encodeURIComponent(avatar)}`;
            });
        });
    }

    function renderCategoryFilters(categories) {
        if (!categoryGroup) return;
        categoryGroup.innerHTML = `
            <label class="checkbox-label"><input type="radio" name="cat_filter" value="0" checked> All Categories</label>
        `;
        categories.forEach(cat => {
            const label = document.createElement("label");
            label.className = "checkbox-label";
            label.innerHTML = `<input type="radio" name="cat_filter" value="${cat.id}"> ${escapeHTML(cat.name)}`;
            categoryGroup.appendChild(label);
        });

        // Add filter change handlers
        categoryGroup.querySelectorAll("input").forEach(radio => {
            radio.addEventListener("change", () => {
                currentCategory = parseInt(radio.value);
                loadWorkers();
            });
        });
    }

    function showEmpty() {
        if (workersList) workersList.innerHTML = "";
        if (emptyState) emptyState.style.display = "block";
        if (toolbarTitle) toolbarTitle.textContent = "0 Professionals found";
    }

    function escapeHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    // Bind Search Action
    if (searchBtn && searchInputs.length >= 2) {
        searchBtn.addEventListener("click", () => {
            query = searchInputs[0].value.trim();
            location = searchInputs[1].value.trim();
            loadWorkers();
        });
    }

    // Sort Select trigger
    if (sortSelect) {
        sortSelect.addEventListener("change", loadWorkers);
    }

    // Initial Trigger load
    loadWorkers();
});
