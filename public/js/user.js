document.addEventListener('DOMContentLoaded', () => {

    const userModal = document.getElementById('userModal');
    const editUserModal = document.getElementById('editUserModal');

    window.openUserModal = function() {
        userModal.style.display = 'block';
    }
    window.closeUserModal = function() {
        userModal.style.display = 'none';
    }

    window.closeEditUserModal = function() {
        editUserModal.style.display = 'none';
    }

    // --- EDIT USER ---
    const editButtons = document.querySelectorAll('.edit-user-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            document.getElementById('userId').value = id;
            document.getElementById('editUserFirstName').value = button.dataset.first_name;
            document.getElementById('editUserLastName').value = button.dataset.last_name;
            document.getElementById('editUserEmail').value = button.dataset.email;
            document.getElementById('editUserRole').value = button.dataset.role;
            document.getElementById('editUserStatus').value = button.dataset.status;

            // Update the form action to point to the correct user API endpoint
            document.getElementById('editUserForm').action = `/admin/users/${id}`;

            editUserModal.style.display = 'block';
        });
    });

    // --- DELETE USER ---
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.dataset.id;
            if (!confirm('Are you sure you want to delete this user?')) return;

            try {
                const res = await fetch(`/api/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                        // Add 'Authorization' if needed: 'Authorization': 'Bearer YOUR_TOKEN'
                    }
                });

                const data = await res.json();

                if (res.ok) {
                    alert(data.message);
                    const row = document.getElementById(`user-row-${id}`);
                    if (row) row.remove();
                } else {
                    alert(data.message || 'Failed to delete user.');
                }
            } catch (err) {
                console.error(err);
                alert('Something went wrong while deleting.');
            }
        });
    });

    // --- CLOSE MODALS WHEN CLICKING OUTSIDE ---
    window.onclick = function(event) {
        if (event.target === userModal) userModal.style.display = 'none';
        if (event.target === editUserModal) editUserModal.style.display = 'none';
    }
});