const loginCard = document.getElementById("loginCard");
const registerCard = document.getElementById("registerCard");
const forgotPasswordCard = document.getElementById("forgotPasswordCard");

function resetCards() {
    loginCard.classList.remove("active", "hidden");
    registerCard.classList.remove("active", "hidden");
    forgotPasswordCard.classList.remove("active", "hidden");
}

function showRegister() {
    resetCards();
    loginCard.classList.add("hidden");
    registerCard.classList.add("active");
}

function showLogin() {
    resetCards();
    loginCard.classList.add("active");
}

function showForgotPassword() {
    resetCards();
    forgotPasswordCard.classList.add("active");
}

// Set initial card visibility
loginCard.classList.add("active");
