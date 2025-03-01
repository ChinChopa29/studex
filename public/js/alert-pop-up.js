document.addEventListener("DOMContentLoaded", () => {
    const alertDiv = document.getElementById("alert-div");

    if (alertDiv) {
        setTimeout(() => {
            alertDiv.style.transition = "opacity 0.5s ease";
            alertDiv.style.opacity = "0";

            setTimeout(() => {
                alertDiv.remove();
            }, 500);
        }, 3000);
    }
});
