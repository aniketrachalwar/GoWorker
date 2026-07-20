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
// DARK / LIGHT MODE
// ============================

const themeBtn = document.querySelector(".theme-btn");

themeBtn.addEventListener("click", () => {

    document.body.classList.toggle("dark-mode");

    if(document.body.classList.contains("dark-mode")){

        themeBtn.innerHTML =
        '<i class="fa-solid fa-moon"></i>';

    }

    else{

        themeBtn.innerHTML =
        '<i class="fa-regular fa-sun"></i>';

    }

});

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

loginBtn.addEventListener("click", () => {
    window.location.href = "login.html";
});


const signupBtn = document.querySelector(".signup-btn");

signupBtn.addEventListener("click", () => {
    window.location.href = "signup.html";
});
