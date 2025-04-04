document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("fileInput");
    const fileList = document.getElementById("fileList");
    let selectedFiles = [];

    fileInput.addEventListener("change", function (event) {
        selectedFiles = [...selectedFiles, ...Array.from(event.target.files)];
        updateFileList();
    });

    function updateFileList() {
        fileList.innerHTML = "";

        const dataTransfer = new DataTransfer();

        selectedFiles.forEach((file, index) => {
            dataTransfer.items.add(file);

            const li = document.createElement("li");
            li.classList.add(
                "flex",
                "items-center",
                "justify-between",
                "bg-gray-700",
                "p-3",
                "rounded-lg",
                "shadow"
            );

            const fileInfo = document.createElement("div");
            fileInfo.classList.add("flex", "items-center", "space-x-3");

            const icon = document.createElement("i");
            icon.classList.add("far", "fa-file", "text-blue-400");

            const fileName = document.createElement("span");
            fileName.textContent = file.name;
            fileName.classList.add("truncate", "max-w-xs");

            fileInfo.appendChild(icon);
            fileInfo.appendChild(fileName);

            const removeBtn = document.createElement("button");
            removeBtn.innerHTML =
                '<i class="fas fa-trash text-red-500 hover:text-red-400"></i>';
            removeBtn.onclick = function () {
                selectedFiles.splice(index, 1);
                updateFileList();
            };

            li.appendChild(fileInfo);
            li.appendChild(removeBtn);
            fileList.appendChild(li);
        });

        fileInput.files = dataTransfer.files;
    }

    const tabs = document.querySelectorAll("[data-tabs-target]");
    const tabContents = document.querySelectorAll('[role="tabpanel"]');

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            const target = document.querySelector(tab.dataset.tabsTarget);

            tabContents.forEach((content) => {
                content.classList.add("hidden");
            });

            target.classList.remove("hidden");

            tabs.forEach((t) => {
                t.classList.remove(
                    "active",
                    "border-blue-500",
                    "text-blue-400"
                );
                t.classList.add("border-transparent");
            });

            tab.classList.add("active", "border-blue-500", "text-blue-400");
            tab.classList.remove("border-transparent");
        });
    });

    function formatDate(dateStr) {
        if (!dateStr) return "—";

        let months = [
            "января",
            "февраля",
            "марта",
            "апреля",
            "мая",
            "июня",
            "июля",
            "августа",
            "сентября",
            "октября",
            "ноября",
            "декабря",
        ];

        let date = new Date(dateStr);
        let day = date.getDate();
        let month = months[date.getMonth()];
        let year = date.getFullYear();

        return `${day} ${month} ${year} года`;
    }

    function initMilestoneDates(selectId, fromSpanId, deadlineSpanId) {
        const select = document.getElementById(selectId);
        const fromSpan = document.getElementById(fromSpanId);
        const deadlineSpan = document.getElementById(deadlineSpanId);

        function updateDates() {
            const selectedOption = select.options[select.selectedIndex];
            fromSpan.textContent = formatDate(selectedOption.dataset.from);
            deadlineSpan.textContent = formatDate(
                selectedOption.dataset.deadline
            );
        }

        select.addEventListener("change", updateDates);
        updateDates();
    }

    initMilestoneDates("milestone_id", "milestoneFrom", "milestoneDeadline");
    initMilestoneDates(
        "milestone_id_test",
        "milestoneFromTest",
        "milestoneDeadlineTest"
    );
    initMilestoneDates(
        "milestone_id_attendance",
        "milestoneFromAttendance",
        "milestoneDeadlineAttendance"
    );
});
