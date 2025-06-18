document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("filter-date");
    const timeInput = document.getElementById("filter-time");
    const classroomInput = document.getElementById("filter-classroom");
    const titleInput = document.getElementById("filter-title");

    const lessonRows = document.querySelectorAll(".lesson-row");

    function filterLessons() {
        console.log("Фильтрация запущена");
        const dateVal = dateInput.value.trim().toLowerCase();
        const timeVal = timeInput.value.trim().toLowerCase();
        const classroomVal = classroomInput.value.trim().toLowerCase();
        const titleVal = titleInput.value.trim().toLowerCase();

        lessonRows.forEach((row) => {
            const date = row.getAttribute("data-date").toLowerCase();
            const time = row.getAttribute("data-time").toLowerCase();
            const classroom = row.getAttribute("data-classroom").toLowerCase();
            const title = row.getAttribute("data-title").toLowerCase();

            const matchesDate = date.includes(dateVal);
            const matchesTime = time.includes(timeVal);
            const matchesClassroom = classroom.includes(classroomVal);
            const matchesTitle = title.includes(titleVal);

            if (
                matchesDate &&
                matchesTime &&
                matchesClassroom &&
                matchesTitle
            ) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    [dateInput, timeInput, classroomInput, titleInput].forEach((input) => {
        input.addEventListener("input", filterLessons);
    });

    filterLessons();
});
