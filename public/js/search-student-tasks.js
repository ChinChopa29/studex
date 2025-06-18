document.addEventListener("DOMContentLoaded", function () {
    const studentSearch = document.getElementById("studentSearch");
    const groupFilter = document.getElementById("groupFilter");
    const statusFilter = document.getElementById("statusFilter");

    function filterStudents() {
        const searchText = studentSearch.value.toLowerCase();
        const groupId = groupFilter.value;
        const status = statusFilter.value;
        let anyVisible = false;

        document
            .querySelectorAll("[data-group-container]")
            .forEach((groupContainer) => {
                let groupHasVisibleStudents = false;
                const groupIdValue = groupContainer.getAttribute(
                    "data-group-container"
                );

                groupContainer
                    .querySelectorAll(".student-row")
                    .forEach((row) => {
                        const studentName = row
                            .querySelector("td:first-child")
                            .textContent.toLowerCase();
                        const rowStatus = row.getAttribute("data-status");

                        const matchesSearch = studentName.includes(searchText);
                        const matchesGroup =
                            !groupId || groupId === `group-${groupIdValue}`;
                        const matchesStatus =
                            !status ||
                            (status === "checked" &&
                                rowStatus.includes("Проверено")) ||
                            (status === "waiting" &&
                                rowStatus.includes("Ожидание")) ||
                            (status === "not_submitted" &&
                                rowStatus.includes("Не сдано"));

                        if (matchesSearch && matchesGroup && matchesStatus) {
                            row.style.display = "";
                            groupHasVisibleStudents = true;
                            anyVisible = true;
                        } else {
                            row.style.display = "none";
                        }
                    });

                if (groupHasVisibleStudents) {
                    groupContainer.style.display = "";
                } else {
                    groupContainer.style.display = "none";
                }
            });

        const noResultsMessage = document.getElementById("no-results-message");
        if (noResultsMessage) {
            noResultsMessage.style.display = anyVisible ? "none" : "block";
        }
    }

    [studentSearch, groupFilter, statusFilter].forEach((element) => {
        element.addEventListener("input", filterStudents);
        element.addEventListener("change", filterStudents);
    });

    filterStudents();
});
