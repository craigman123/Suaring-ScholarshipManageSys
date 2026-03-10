document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('scholarshipModal');

    window.openModal = function() {
        modal.style.display = 'flex';
    }

    window.closeModal = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) modal.style.display = 'none';
    }
});

function openEditModal(scholarshipId) {
    const btn = document.querySelector(`.edit-btn[data-id='${scholarshipId}']`);

    const modal = document.getElementById('editScholarshipModal');
    modal.style.display = 'block';

    document.getElementById('scholarshipId').value = btn.dataset.id;
    document.getElementById('scholarshipPoster').value = btn.dataset.poster;
    document.getElementById('scholarshipTitle').value = btn.dataset.title;
    document.getElementById('scholarshipDescription').value = btn.dataset.description;
    document.getElementById('scholarshipDeadline').value = btn.dataset.deadline;
    document.getElementById('scholarshipRequirement').value = btn.dataset.requirement;

    document.getElementById('editScholarshipForm').action = `/admin/scholarships/${btn.dataset.id}`;
}

function closeEditModal() {
    document.getElementById('editScholarshipModal').style.display = 'none';
}

window.addEventListener('DOMContentLoaded', () => {

    // Poster
    const posterInput = document.getElementById('fileposter');
    const posterHeader = posterInput.closest('.file-container').querySelector('.file-header');

    posterInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            posterHeader.innerHTML = `<img src="${e.target.result}" style="width:101%; height:100%; object-fit:cover; border-radius:10px;">`;
        };
        reader.readAsDataURL(file);
        BorderPoster(document.getElementById('borderposter'));
    });
});
