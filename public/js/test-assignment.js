document.addEventListener("DOMContentLoaded", function () {
    const testForm = document.getElementById("testForm");
    const questionBlocks = document.querySelectorAll(".question-block");

    testForm.addEventListener("submit", function (e) {
        let allAnswered = true;

        questionBlocks.forEach((block) => {
            const questionId = block
                .querySelector('input[type="radio"]')
                .name.match(/\[(\d+)\]/)[1];
            const answered =
                block.querySelector(
                    `input[name="answers[${questionId}]"]:checked`
                ) !== null;

            if (!answered) {
                allAnswered = false;
                block.classList.add("border-red-500");
            } else {
                block.classList.remove("border-red-500");
            }
        });

        if (!allAnswered) {
            e.preventDefault();
            alert("Пожалуйста, ответьте на все вопросы перед отправкой теста!");
        } else if (
            !confirm(
                "Вы уверены, что хотите отправить ответы? После отправки изменить их будет нельзя."
            )
        ) {
            e.preventDefault();
        }
    });
});
