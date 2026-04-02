document.addEventListener("DOMContentLoaded", function () {

    const inputs = document.querySelectorAll(".requirement-input");

    inputs.forEach(input => {
        input.addEventListener("change", function () {

            const index = this.dataset.index;
            const file = this.files[0];

            if (!file) return;

            // Update file name
            const fileNameDisplay = document.getElementById(`fileName_${index}`);
            fileNameDisplay.textContent = file.name;

            // Preview container
            const previewContainer = document.getElementById(`preview_${index}`);

            // Clear old image
            previewContainer.innerHTML = "";

            // Create new image preview
            const img = document.createElement("img");
            img.src = URL.createObjectURL(file);
            img.style.width = "120px";
            img.style.borderRadius = "5px";

            previewContainer.appendChild(img);
        });
    });

});