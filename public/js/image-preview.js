function previewImage(event) {
    let reader = new FileReader();
    reader.onload = function () {
        let output = document.getElementById("imagePreview");
        output.src = reader.result;
        document
            .getElementById("imagePreviewContainer")
            .classList.remove("hidden");
    };
    reader.readAsDataURL(event.target.files[0]);
}

function clearImage() {
    document.getElementById("image").value = "";
    document.getElementById("imagePreviewContainer").classList.add("hidden");
}
