document.addEventListener('DOMContentLoaded', function () {

    const previewContainer = document.getElementById('previewContainer');
    const addBtn = document.getElementById('addRequirementBtn');

    let extraIndex = 1000;

    // ✅ Preview EXISTING inputs
    document.querySelectorAll('.requirement-input').forEach(input => {
        input.addEventListener('change', function () {
            showPreview(input);
        });
    });

    // ✅ Add Requirement (auto open file picker)
    addBtn.addEventListener('click', function () {

        const input = document.createElement('input');
        input.type = 'file';
        input.name = `requirements[${extraIndex}]`;
        input.accept = 'image/*';
        input.style.display = 'none';

        document.body.appendChild(input);

        input.click();

        input.addEventListener('change', function () {
            if (input.files.length > 0) {
                showPreview(input);
                extraIndex++;
            }
        });
    });

    // ✅ Preview function
    function showPreview(input) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = function (e) {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '255px';
            img.style.height = '255px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #ccc';

            // ❌ Remove button
            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = ' <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24" ><path d="M17 6V4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v2H2v2h2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8h2V6zM9 4h6v2H9zm9 16H6V8h12z"></path><path d="M14.29 10.29 12 12.59l-2.29-2.3-1.42 1.42 2.3 2.29-2.3 2.29 1.42 1.42 2.29-2.3 2.29 2.3 1.42-1.42-2.3-2.29 2.3-2.29z"></path></svg> ';
            removeBtn.style.width = '30px';
            removeBtn.style.height = '30px';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '0';
            removeBtn.style.right = '0';
            removeBtn.style.background = 'red';
            removeBtn.style.color = 'white';
            removeBtn.style.border = 'none';
            removeBtn.style.cursor = 'pointer';

            removeBtn.onclick = function () {
                wrapper.remove();
                input.value = ''; 
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            previewContainer.appendChild(wrapper);
        };

        reader.readAsDataURL(file);
    }

});

document.addEventListener('DOMContentLoaded', () => {
    const submitBtn = document.querySelector('.submit-btn');
    const notifyContainer = document.querySelector('.notify-container');

    function showNotification(message, type = 'error') {
        const notif = document.createElement('div');
        notif.classList.add('notify');
        notif.textContent = message;

        // Add to container
        notifyContainer.appendChild(notif);

        // Remove after 3 seconds
        setTimeout(() => {
            notif.remove();
        }, 3000);
    }

    submitBtn.addEventListener('click', (e) => {
        const fileInput = document.querySelector('input[type="file"]');
        if (!fileInput || !fileInput.files.length) {
            e.preventDefault(); // Prevent form submission
            showNotification("Please add a file before submitting");
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