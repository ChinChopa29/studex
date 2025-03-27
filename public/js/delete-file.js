document.addEventListener("DOMContentLoaded", function () {
    const deletedFilesInput = document.getElementById("deletedFilesInput");
    let deletedFiles = [];

    document.querySelectorAll(".delete-file").forEach((button) => {
        button.addEventListener("click", function () {
            const fileId = this.getAttribute("data-file-id");
            const listItem = document.getElementById(`file-${fileId}`);

            if (
                confirm("Вы уверены, что хотите удалить этот файл из списка?")
            ) {
                deletedFiles.push(fileId);
                deletedFilesInput.value = deletedFiles.join(",");
                listItem.remove();
            }
        });
    });
});
