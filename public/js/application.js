document.addEventListener('DOMContentLoaded', function () {

    // Preview function for one image per requirement
    function showPreview(input) {
        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const index = input.id.split('_')[1];
        const previewContainer = document.getElementById(`preview_${index}`);
        previewContainer.innerHTML = ''; // clear previous preview

        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.style.display = 'inline-block';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.width = '150px';
        img.style.height = '150px';
        img.style.objectFit = 'cover';
        img.style.border = '1px solid #ccc';
        img.style.borderRadius = '8px';

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '✕';
        removeBtn.style.position = 'absolute';
        removeBtn.style.top = '0';
        removeBtn.style.right = '0';
        removeBtn.style.width = '25px';
        removeBtn.style.height = '25px';
        removeBtn.style.background = 'red';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '50%';
        removeBtn.style.cursor = 'pointer';
        removeBtn.onclick = function () {
            wrapper.remove();
            input.value = ''; // clear the file input
            document.getElementById(`fileName_${index}`).textContent = 'No file chosen';
        };

        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        previewContainer.appendChild(wrapper);

        // Update file name
        document.getElementById(`fileName_${index}`).textContent = file.name;
        document.getElementById(`error_${index}`).style.display = 'none';
    }

    // Attach change event to all inputs
    document.querySelectorAll('.requirement-input').forEach(input => {
        input.addEventListener('change', function () {
            showPreview(input);
        });
    });

    // Form validation: ensure all required images are selected
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let valid = true;
        document.querySelectorAll('.requirement-input[required]').forEach(input => {
            const index = input.id.split('_')[1];
            if (!input.files.length) {
                document.getElementById(`error_${index}`).style.display = 'inline';
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Please upload all required images before submitting!');
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('scholarshipForm');
    const notifyContainer = document.querySelector('.notify-container');

    function showNotification(message, type = 'error') {
        const notif = document.createElement('div');
        notif.classList.add('notify', type);
        notif.textContent = message;
        notifyContainer.appendChild(notif);

        setTimeout(() => notif.remove(), 3000);
    }

    form.addEventListener('submit', (e) => {
        const fileInput = form.querySelector('input[type="file"]');

        if (!fileInput || !fileInput.files.length) {
            e.preventDefault(); // Prevent submission
            showNotification("Please add a file before submitting", 'error');
        } else {
            e.preventDefault(); // Temporarily prevent to show success message first

            // Simulate form submission delay (replace with actual fetch/AJAX if needed)
            setTimeout(() => {
                showNotification("Application submitted successfully!", 'success');

                setTimeout(() => {
                    window.location.href = '/student_scholarships'; 
                }, 1000); 
            }, 100);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {

    // Update file name when a file is chosen
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const index = this.id.split('_')[1];
            const fileNameSpan = document.getElementById(`fileName_${index}`);
            const errorSpan = document.getElementById(`error_${index}`);
            if(this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                errorSpan.style.display = 'none';
            } else {
                fileNameSpan.textContent = 'No file chosen';
            }
        });
    });

    // Form submit validation: ensure all files selected
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let valid = true;

        fileInputs.forEach(input => {
            const index = input.id.split('_')[1];
            const errorSpan = document.getElementById(`error_${index}`);
            if(input.files.length === 0) {
                errorSpan.style.display = 'block';
                valid = false;
            } else {
                errorSpan.style.display = 'none';
            }
        });

        if(!valid) {
            e.preventDefault(); // prevent submission if any missing
            alert("Please upload all required files before submitting!");
        }
    });
});

function openModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}