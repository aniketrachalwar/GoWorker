// ============================
// LANGUAGE DROPDOWN
// ============================

const languageBtn = document.querySelector(".language-btn");
const dropdown = document.querySelector(".dropdown-content");

languageBtn.addEventListener("click", (e) => {
    e.stopPropagation();

    dropdown.style.display =
        dropdown.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", () => {
    dropdown.style.display = "none";
});

// ============================
// SYSTEM DARK / LIGHT MODE DETECTION
// ============================
const systemThemeQuery = window.matchMedia("(prefers-color-scheme: dark)");

function applySystemTheme(e) {
    if (e.matches) {
        document.body.classList.add("dark-mode");
    } else {
        document.body.classList.remove("dark-mode");
    }
}

// Initial detection
applySystemTheme(systemThemeQuery);

// Listen for system theme preference changes
systemThemeQuery.addEventListener("change", applySystemTheme);

// ============================
// SEARCH BUTTON
// ============================

const searchBtn = document.querySelector(".search-box button");

searchBtn.addEventListener("click", () => {

    const category =
        document.querySelector(".search-box select").value;

    const location =
        document.querySelector(".search-box input").value;

    if(location === ""){

        alert("Please enter your location.");

        return;

    }

    alert(
        `Searching for ${category} near ${location}`
    );

});

// ============================
// SMOOTH SCROLL
// ============================

document.querySelectorAll('a[href^="#"]').forEach(anchor=>{

    anchor.addEventListener("click",function(e){

        e.preventDefault();

        const target=document.querySelector(this.getAttribute("href"));

        if(target){

            target.scrollIntoView({

                behavior:"smooth"

            });

        }

    });

});

// ============================
// HERO IMAGE PARALLAX
// ============================

const heroImage = document.querySelector(".hero-right img");

document.addEventListener("mousemove",(e)=>{

    let x=(window.innerWidth/2-e.pageX)/40;

    let y=(window.innerHeight/2-e.pageY)/40;

    heroImage.style.transform=
    `translate(${x}px,${y}px)`;

});

// ============================
// FADE-IN ANIMATION
// ============================

const observer=new IntersectionObserver(entries=>{

    entries.forEach(entry=>{

        if(entry.isIntersecting){

            entry.target.classList.add("show");

        }

    });

});

document.querySelectorAll(".card,.step,.stat,.feature").forEach(el=>{

    el.classList.add("hidden");

    observer.observe(el);

});

const loginBtn = document.querySelector(".login-btn");

if (loginBtn) {
    loginBtn.addEventListener("click", (e) => {
        // Only redirect if there is no inline onclick handler already defined
        if (!loginBtn.getAttribute("onclick")) {
            window.location.href = "login.html";
        }
    });
}


const signupBtn = document.querySelector(".signup-btn");

if (signupBtn) {
    signupBtn.addEventListener("click", (e) => {
        // Only redirect if there is no inline onclick handler already defined
        if (!signupBtn.getAttribute("onclick")) {
            window.location.href = "signup.html";
        }
    });
}

// --- DYNAMIC SESSION NAVBAR TRIGGER ---
document.addEventListener("DOMContentLoaded", () => {
    const sessionStr = localStorage.getItem("user_session");
    if (sessionStr) {
        const session = JSON.parse(sessionStr);
        const navRight = document.querySelector(".nav-right");
        if (navRight) {
            navRight.innerHTML = `
                <button class="login-btn" onclick="location.href='profile.html'"><i class="fa-regular fa-user"></i> My Profile</button>
                <button class="signup-btn" id="nav-logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            `;
            const logoutBtn = document.getElementById("nav-logout-btn");
            if (logoutBtn) {
                logoutBtn.addEventListener("click", () => {
                    localStorage.removeItem("user_session");
                    alert("Logged out successfully!");
                    window.location.reload();
                });
            }
        }
    }
});
