document.addEventListener("DOMContentLoaded", function () {
    const filterElements = ["milestone", "task", "status", "deadline"];

    filterElements.forEach((filterId) => {
        document
            .getElementById(`filter-${filterId}`)
            .addEventListener("input", filterTable);
    });

    function filterTable() {
        const milestoneFilter = document
            .getElementById("filter-milestone")
            .value.toLowerCase();
        const taskFilter = document
            .getElementById("filter-task")
            .value.toLowerCase();
        const statusFilter = document.getElementById("filter-status").value;
        const deadlineFilter = document
            .getElementById("filter-deadline")
            .value.toLowerCase();

        const rows = document.querySelectorAll(".task-row");

        rows.forEach((row) => {
            const milestone = row.getAttribute("data-milestone").toLowerCase();
            const task = row.getAttribute("data-task").toLowerCase();
            const status = row.getAttribute("data-status");
            const deadline = row.getAttribute("data-deadline").toLowerCase();

            const matchesMilestone = milestone.includes(milestoneFilter);
            const matchesTask = task.includes(taskFilter);
            const matchesStatus =
                statusFilter === "" || status === statusFilter;
            const matchesDeadline = deadline.includes(deadlineFilter);

            if (
                matchesMilestone &&
                matchesTask &&
                matchesStatus &&
                matchesDeadline
            ) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
});
