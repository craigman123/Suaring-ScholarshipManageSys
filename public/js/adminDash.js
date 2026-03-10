function showAlert(message, type = 'success', duration = 3000) {
    const container = document.getElementById('alert-container');
    if (!container) return;

    const alert = document.createElement('div');
    alert.textContent = message;
    alert.className = `alert alert-${type} show`;
    container.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = 0;
        setTimeout(() => alert.remove(), 300);
    }, duration);
}

document.addEventListener("DOMContentLoaded", function() {
    showAlert('Welcome to the admin dashboard!', 'success');
});