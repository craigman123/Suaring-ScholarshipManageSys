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
    });
});

window.addEventListener('DOMContentLoaded', () => {
    const posterInput = document.getElementById('editFilePoster');
    const posterHeader = posterInput.closest('.file-container-edit').querySelector('.file-header-edit');

    // Function to set the poster preview
    const setPosterPreview = (src) => {
        if (!src) {
            posterHeader.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>UPLOAD POSTER</p>
            `;
        } else {
            posterHeader.innerHTML = `<img src="${src}" style="width:101%; height:100%; object-fit:cover; border-radius:10px;">`;
        }
    };

    // Prefill modal when clicking edit
    document.querySelectorAll('.edit-scholarship-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const form = document.getElementById('editScholarshipForm');
            form.action = `/admin/scholarships/${id}`;

            document.getElementById('scholarshipId').value = button.dataset.id;
            document.getElementById('scholarshipTitle').value = button.dataset.title;
            document.getElementById('scholarshipDescription').value = button.dataset.description;
            document.getElementById('scholarshipDeadline').value = button.dataset.deadline;
            const statusSelect = document.getElementById('scholarshipStatus');
            if (statusSelect) {
                statusSelect.value = button.dataset.status; 
            }
            document.getElementById('scholarshipRequirement').value = button.dataset.requirement || '';

            setPosterPreview(button.dataset.poster || null);

            posterInput.value = '';

            openEditModal();
        });
    });

    function openEditModal() {
        document.getElementById('editScholarshipModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editScholarshipModal').style.display = 'none';
    }

    // Update preview on selecting a new file
    posterInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => setPosterPreview(e.target.result);
        reader.readAsDataURL(file);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.dataset.id;

            if (!confirm("Are you sure you want to delete this scholarship?")) return;

            try {
                const res = await fetch(`/api/admin/scholarships/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                        // Add 'Authorization' here if needed
                    }
                });

                const data = await res.json();

                if (res.ok) {
                    alert(data.message);
                    const row = document.getElementById(`scholarship-row-${id}`);
                    if (row) row.remove();
                } else {
                    alert(data.message || "Failed to delete scholarship.");
                }
            } catch (err) {
                console.error(err);
                alert("Something went wrong while deleting.");
            }
        });
    });
});
