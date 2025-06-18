document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("studentSearch");

    searchInput.addEventListener("input", function () {
        const searchTerm = this.value.trim().toLowerCase();
        const studentRows = document.querySelectorAll(".student-row");

        studentRows.forEach((row) => {
            const studentName = row.getAttribute("data-name").toLowerCase();
            if (studentName.includes(searchTerm)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

    searchInput.dispatchEvent(new Event("input"));
});
