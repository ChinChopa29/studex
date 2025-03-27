document.addEventListener("DOMContentLoaded", function () {
    const newFileInput = document.getElementById("fileInput");
    const newFileList = document.getElementById("newFileList");
    let selectedFiles = [];

    newFileInput.addEventListener("change", function (event) {
        // Добавляем новые файлы к уже выбранным
        selectedFiles = [...selectedFiles, ...Array.from(event.target.files)];
        updateFileList();
    });

    function updateFileList() {
        newFileList.innerHTML = "";
        
        // Создаем новый DataTransfer для обновления input.files
        const dataTransfer = new DataTransfer();
        
        selectedFiles.forEach((file, index) => {
            // Добавляем файл в DataTransfer
            dataTransfer.items.add(file);
            
            // Создаем элемент списка
            const li = document.createElement("li");
            li.classList.add(
                "flex",
                "items-center",
                "justify-between",
                "bg-gray-800",
                "p-2",
                "rounded-lg",
                "shadow"
            );

            const fileName = document.createElement("span");
            fileName.textContent = file.name;

            const removeBtn = document.createElement("button");
            removeBtn.innerHTML =
                '<i class="fa fa-trash text-red-500 hover:text-red-700"></i>';
            removeBtn.classList.add("ml-3");
            removeBtn.onclick = function () {
                selectedFiles.splice(index, 1);
                updateFileList();
            };

            li.appendChild(fileName);
            li.appendChild(removeBtn);
            newFileList.appendChild(li);
        });

        // Обновляем files в input элементе
        newFileInput.files = dataTransfer.files;
    }
});