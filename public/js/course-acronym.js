document.addEventListener("DOMContentLoaded", function () {
    const courseNameInput = document.querySelector("input[name='name']");
    const semesterInput = document.querySelector("input[name='semester']");
    const courseCodeInput = document.querySelector("input[name='code']");
    const codeCheckResult = document.getElementById("code-check-result");

    let isManualInput = false;

    function generateCourseCode() {
        if (isManualInput) return;

        let courseName = courseNameInput.value.trim();
        let semester = semesterInput.value.trim();

        if (!courseName || !semester) return;

        let acronym = courseName
            .split(" ")
            .map((word) => word[0])
            .join("")
            .toUpperCase();

        let courseCode = `${acronym}${semester}`;
        courseCodeInput.value = courseCode;

        checkCourseCodeExistence(courseCode);
    }

    function checkCourseCodeExistence(courseCode) {
        if (!courseCode.trim()) return;

        fetch(
            `/admin/courses/search-code?code=${encodeURIComponent(courseCode)}`
        )
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Ошибка HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.error) {
                    codeCheckResult.innerHTML = `<span class="text-red-500">${data.error}</span>`;
                    return;
                }

                if (data.length > 0) {
                    let listHTML =
                        '<span class="text-red-500">Данный курс уже существует: </span><ul>';
                    data.forEach((course) => {
                        listHTML += `<li><a href="/admin/courses/${course.id}" class="text-blue-500 hover:text-blue-700">${course.name}</a></li>`;
                    });
                    listHTML += "</ul>";
                    codeCheckResult.innerHTML = listHTML;
                } else {
                    codeCheckResult.innerHTML =
                        '<span class="text-green-500">Код свободен!</span>';
                }
            })
            .catch((error) => {
                console.error("Ошибка при проверке кода:", error);
                codeCheckResult.innerHTML =
                    '<span class="text-red-500">Ошибка при проверке кода. Попробуйте снова.</span>';
            });
    }

    courseCodeInput.addEventListener("input", function () {
        isManualInput = true;
        checkCourseCodeExistence(courseCodeInput.value);
    });

    courseNameInput.addEventListener("input", () => {
        isManualInput = false;
        generateCourseCode();
    });

    semesterInput.addEventListener("input", () => {
        isManualInput = false;
        generateCourseCode();
    });

    setTimeout(generateCourseCode, 500);
});

document.addEventListener("DOMContentLoaded", function () {
    const degreeSelect = document.getElementById("degree");
    const programSelect = document.getElementById("education_program_id");

    const originalOptions = Array.from(
        programSelect.querySelectorAll("option")
    );

    function filterPrograms() {
        let selectedDegree = degreeSelect.value.trim();

        programSelect.innerHTML = "";

        originalOptions.forEach((option) => {
            let optionDegree = option.getAttribute("data-degree")?.trim();

            if (
                !optionDegree ||
                selectedDegree === "" ||
                optionDegree === selectedDegree
            ) {
                programSelect.appendChild(option);
            }
        });

        $(programSelect).val("").trigger("change.select2");
    }

    degreeSelect.addEventListener("change", filterPrograms);
    filterPrograms();
});
