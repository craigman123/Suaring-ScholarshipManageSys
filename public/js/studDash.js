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
    showAlert("Logged In Successful!", "success");
});

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('.modal-content');
    const deleteBtns = document.querySelectorAll('.delete-btn');
    const cancelBtn = document.getElementById('cancelBtn');
    const deleteForm = document.getElementById('deleteForm');
    const overlay = modal.querySelector('.modal-overlay');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const url = btn.dataset.url; 
            deleteForm.action = url;

            modal.classList.add('show');
        });
    });

    const closeModal = () => {
        modal.classList.remove('show');
    };

    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
    modalContent.addEventListener('click', e => e.stopPropagation());
});