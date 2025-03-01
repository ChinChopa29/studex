document.getElementById("title").addEventListener("input", function () {
    let title = this.value.trim();
    let words = title.split(/\s+/);
    let acronym = words.map((word) => word.charAt(0).toUpperCase()).join("");
    document.getElementById("acronym").value = acronym;
});

document.getElementById("degree").addEventListener("change", function () {
    let degree = this.value;
    let durationSelect = document.getElementById("duration");

    durationSelect.innerHTML = "";

    let options = [];
    if (degree === "Бакалавриат") options = [3, 4];
    if (degree === "Магистратура") options = [1, 2];
    if (degree === "Аспирантура") options = [3, 4, 5];

    options.forEach((years) => {
        let option = document.createElement("option");
        option.value = years;
        if (years === 1) {
            option.textContent = years + " год";
        } else if (years === 5) {
            option.textContent = years + " лет";
        } else {
            option.textContent = years + " года";
        }

        durationSelect.appendChild(option);
    });
});
