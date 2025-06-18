let currentDate = new Date();
let currentWeekStart = new Date();

const courseId = window.courseId;
const isTeacherOrAdmin = window.isTeacherOrAdmin;

function updateLessonsInCalendar(lessons) {
    console.log("Полученные занятия:", lessons);

    document.querySelectorAll("tbody td:not(:first-child)").forEach((cell) => {
        cell.innerHTML = '<div class="text-gray-500 text-sm">Нет занятий</div>';
    });

    if (!lessons || Object.keys(lessons).length === 0) {
        console.log("Нет занятий для отображения");
        return;
    }

    document.querySelectorAll("tbody tr").forEach((row) => {
        const timeCell = row.querySelector("td:first-child");
        const timeText = timeCell.textContent.trim();
        const timeRange = timeText.split(" - ").join("-");

        row.querySelectorAll("td:not(:first-child)").forEach(
            (cell, dayIndex) => {
                const dateHeader = document.querySelector(
                    `th.day-header:nth-child(${dayIndex + 2})`
                );
                const date = dateHeader.dataset.date;

                if (lessons[date] && lessons[date][timeRange]) {
                    const lessonsAtTime = lessons[date][timeRange];
                    let html = '<div class="space-y-1">';

                    lessonsAtTime.forEach((lesson) => {
                        let groupInfo = "";
                        if (isTeacherOrAdmin && lesson.group) {
                            groupInfo = lesson.group.name || "";
                            if (groupInfo) {
                                groupInfo = `<br><span class="text-xs text-gray-300">${groupInfo}</span>`;
                            }
                        }

                        let classroom = "Не указано";
                        if (lesson.classroom) {
                            classroom =
                                typeof lesson.classroom === "object"
                                    ? lesson.classroom.number
                                    : lesson.classroom;
                        }

                        html += `
                    <a href="/course/${courseId}/schedule/lessons/${lesson.id}">
                        <div class="lesson-item p-1 mb-1 rounded" style="background-color: ${lesson.color}20">
                            <div class="text-sm font-medium">${lesson.title}</div>
                            <div class="text-xs text-gray-400">
                                ${classroom} | ${lesson.type}
                                ${groupInfo}
                            </div>
                        </div>
                    </a>`;
                    });

                    html += "</div>";
                    cell.innerHTML = html;
                }
            }
        );
    });
}

function updateWeekDisplay() {
    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(currentWeekStart.getDate() + 5);

    const options = { day: "numeric", month: "numeric", year: "numeric" };
    const startStr = currentWeekStart.toLocaleDateString("ru-RU", options);
    const endStr = weekEnd.toLocaleDateString("ru-RU", options);

    document.getElementById(
        "currentWeekRange"
    ).textContent = `${startStr} - ${endStr}`;
}

function updateCalendarDates() {
    const tempDate = new Date(currentWeekStart);

    document.querySelectorAll(".day-header").forEach((header, index) => {
        const dayDate = new Date(tempDate);
        dayDate.setDate(tempDate.getDate() + index);

        const dateStr = dayDate.toISOString().split("T")[0];
        const formattedDate = dayDate.toLocaleDateString("ru-RU", {
            day: "numeric",
            month: "numeric",
        });

        header.dataset.date = dateStr;

        const dateElement = header.querySelector("div.text-sm");
        if (dateElement) {
            dateElement.textContent = formattedDate;
        }
    });

    loadLessonsForWeek(currentWeekStart);
}

async function loadLessonsForWeek(startDate) {
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + 5);

    console.log(
        "Загрузка занятий с",
        startDate.toISOString().split("T")[0],
        "по",
        endDate.toISOString().split("T")[0]
    );

    try {
        const response = await fetch(
            `/course/${courseId}/schedule/lessons?start=${
                startDate.toISOString().split("T")[0]
            }&end=${endDate.toISOString().split("T")[0]}`
        );

        if (!response.ok) {
            const error = await response.json();
            console.error("Ошибка сервера:", error);
            throw new Error(error.error || "Ошибка загрузки данных");
        }

        const lessons = await response.json();
        console.log("Полученные данные:", lessons);
        updateLessonsInCalendar(lessons);
    } catch (error) {
        console.error("Ошибка:", error);
        alert("Ошибка загрузки расписания: " + error.message);
    }
}

function changeWeek(weeks) {
    currentWeekStart.setDate(currentWeekStart.getDate() + weeks * 7);
    updateWeekDisplay();
    updateCalendarDates();
}

function resetToCurrentWeek() {
    currentDate = new Date();
    initCalendar();
}

function initCalendar() {
    currentDate = new Date();
    currentWeekStart = new Date(currentDate);
    currentWeekStart.setDate(
        currentDate.getDate() -
            currentDate.getDay() +
            (currentDate.getDay() === 0 ? -6 : 1)
    );

    updateWeekDisplay();
    updateCalendarDates();
}

document.addEventListener("DOMContentLoaded", initCalendar);
