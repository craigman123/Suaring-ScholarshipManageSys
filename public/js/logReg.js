document.addEventListener("DOMContentLoaded", function() {

    const loginContainer = document.querySelector(".login-container");
    const registerContainer = document.querySelector(".register-container");

    const showRegisterBtn = document.getElementById("showRegister");
    const showLoginBtn = document.getElementById("showLogin");

    showRegisterBtn.addEventListener("click", function() {
        loginContainer.classList.add("hidden");
        registerContainer.classList.remove("hidden");
    });

    showLoginBtn.addEventListener("click", function() {
        registerContainer.classList.add("hidden");
        loginContainer.classList.remove("hidden");
    });

});

document.addEventListener("DOMContentLoaded", function() {
    const alerts = document.querySelectorAll('.alert.show');
    alerts.forEach(alert => {
   
        setTimeout(() => {
            alert.style.opacity = 0; 
         
            setTimeout(() => alert.remove(), 500);
        }, 3000); 
    });
});

window.addEventListener("pageshow", function(event) {
        if (event.persisted || (window.performance &&
                 window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
            window.location.reload();
        }
    });