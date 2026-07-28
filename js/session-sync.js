(function() {
    // List of page paths that require user to be logged in
    const restrictedPages = [
        '/customer-dashboard',
        '/worker-dashboard',
        '/profile',
        '/booking-history',
        '/booking',
        '/chat',
        '/messages'
    ];

    const currentPath = window.location.pathname.toLowerCase();
    const isRestricted = restrictedPages.some(page => currentPath.includes(page));

    // Fetch user status from JWT session
    fetch('/api/auth/me')
        .then(response => {
            if (!response.ok) {
                throw new Error('Unauthorized');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.user) {
                // Sync session into localStorage so existing mockup JS runs properly
                localStorage.setItem("user_session", JSON.stringify(data.user));
                
                // Add header user name updates dynamically
                document.addEventListener("DOMContentLoaded", () => {
                    const headerNames = document.querySelectorAll(".user-name, .profile-name, .nav-user-name");
                    headerNames.forEach(el => {
                        el.textContent = data.user.full_name;
                    });
                    
                    const headerAvatars = document.querySelectorAll(".user-avatar, .nav-user-avatar");
                    if (data.user.avatar) {
                        headerAvatars.forEach(el => {
                            el.src = data.user.avatar;
                        });
                    }
                });
            } else {
                handleUnauthorized();
            }
        })
        .catch(err => {
            handleUnauthorized();
        });

    function handleUnauthorized() {
        localStorage.removeItem("user_session");
        if (isRestricted) {
            alert("Your session has expired or you are not logged in. Redirecting to login...");
            window.location.href = "/login";
        }
    }
})();
