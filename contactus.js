const form = document.querySelector("form");

form.addEventListener("submit", (e) => {

    e.preventDefault();

    alert("Thank you! Your message has been sent successfully.");

    form.reset();

});