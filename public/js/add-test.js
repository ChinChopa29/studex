document.addEventListener("DOMContentLoaded", function () {
    const questionsContainer = document.getElementById("questionsContainer");
    const addQuestionBtn = document.getElementById("addQuestionBtn");
    const questionTemplate = document.getElementById("questionTemplate");
    const answerTemplate = document.getElementById("answerTemplate");
    const testForm = document.getElementById("testForm");

    let questionCounter = 0;

    function addQuestion() {
        const questionIndex = questionCounter++;
        const questionClone = questionTemplate.content.cloneNode(true);
        const questionElement = questionClone.querySelector(".question-item");

        questionElement.dataset.questionIndex = questionIndex;
        questionElement.querySelector(".question-number").textContent =
            questionIndex + 1;

        setupQuestionHandlers(questionElement, questionIndex);

        const answersContainer =
            questionElement.querySelector(".answers-container");
        addAnswerField(answersContainer, questionIndex, 0);
        addAnswerField(answersContainer, questionIndex, 1);

        answersContainer.querySelector('input[type="radio"]').checked = true;

        questionsContainer.appendChild(questionElement);
        return questionElement;
    }

    function setupQuestionHandlers(questionElement, questionIndex) {
        questionElement
            .querySelector(".delete-question")
            .addEventListener("click", function () {
                questionElement.remove();
                reindexQuestions();
            });

        questionElement
            .querySelector(".add-answer-btn")
            .addEventListener("click", function () {
                const answersContainer =
                    questionElement.querySelector(".answers-container");
                addAnswerField(
                    answersContainer,
                    questionIndex,
                    answersContainer.children.length
                );
            });

        const shuffleCheckbox = questionElement.querySelector(
            'input[name$="[shuffle_answers]"]'
        );
        shuffleCheckbox.name = `questions[${questionIndex}][shuffle_answers]`;
    }

    function addAnswerField(container, questionIndex, answerIndex) {
        const answerClone = answerTemplate.content.cloneNode(true);
        const answerElement = answerClone.querySelector(".answer-item");

        const radio = answerElement.querySelector('input[type="radio"]');
        radio.name = `questions[${questionIndex}][correct_answer]`;
        radio.value = answerIndex;

        const textInput = answerElement.querySelector('input[type="text"]');
        textInput.name = `questions[${questionIndex}][answers][${answerIndex}]`;
        textInput.required = true;

        answerElement
            .querySelector(".delete-answer")
            .addEventListener("click", function () {
                answerElement.remove();
                reindexAnswers(container, questionIndex);
            });

        container.appendChild(answerElement);
        return answerElement;
    }

    function reindexQuestions() {
        const questions = questionsContainer.querySelectorAll(".question-item");
        questionCounter = questions.length;

        questions.forEach((question, newIndex) => {
            const oldIndex = question.dataset.questionIndex;
            question.dataset.questionIndex = newIndex;
            question.querySelector(".question-number").textContent =
                newIndex + 1;

            updateQuestionFields(question, oldIndex, newIndex);
        });
    }

    function reindexAnswers(container, questionIndex) {
        const answers = container.querySelectorAll(".answer-item");

        answers.forEach((answer, newIndex) => {
            const radio = answer.querySelector('input[type="radio"]');
            radio.value = newIndex;

            const textInput = answer.querySelector('input[type="text"]');
            textInput.name = `questions[${questionIndex}][answers][${newIndex}]`;
        });
    }

    function updateQuestionFields(question, oldIndex, newIndex) {
        const textInput = question.querySelector('input[name$="[text]"]');
        textInput.name = `questions[${newIndex}][text]`;

        const shuffleInput = question.querySelector(
            'input[name$="[shuffle_answers]"]'
        );
        shuffleInput.name = `questions[${newIndex}][shuffle_answers]`;

        const radios = question.querySelectorAll(
            'input[name$="[correct_answer]"]'
        );
        radios.forEach((radio) => {
            radio.name = `questions[${newIndex}][correct_answer]`;
        });

        const answers = question.querySelectorAll(".answer-item");
        answers.forEach((answer, answerIndex) => {
            const textInput = answer.querySelector('input[type="text"]');
            textInput.name = `questions[${newIndex}][answers][${answerIndex}]`;
        });
    }

    testForm.addEventListener("submit", function (e) {
        let isValid = true;
        const questions = questionsContainer.querySelectorAll(".question-item");

        if (questions.length === 0) {
            alert("Добавьте хотя бы один вопрос!");
            isValid = false;
        }

        questions.forEach((question) => {
            const questionText = question
                .querySelector('input[name$="[text]"]')
                .value.trim();
            const answers = question.querySelectorAll(".answer-item");
            const hasCorrectAnswer =
                question.querySelector(
                    'input[name$="[correct_answer]"]:checked'
                ) !== null;

            if (!questionText) {
                alert("Заполните текст всех вопросов!");
                isValid = false;
            }

            if (answers.length < 2) {
                alert(
                    "Каждый вопрос должен содержать минимум 2 варианта ответа!"
                );
                isValid = false;
            }

            if (!hasCorrectAnswer) {
                alert("Для каждого вопроса выберите правильный ответ!");
                isValid = false;
            }

            answers.forEach((answer) => {
                const answerText = answer
                    .querySelector('input[type="text"]')
                    .value.trim();
                if (!answerText) {
                    alert("Заполните все варианты ответов!");
                    isValid = false;
                }
            });
        });

        if (!isValid) {
            e.preventDefault();
        }
    });

    addQuestionBtn.addEventListener("click", addQuestion);
    addQuestion();
});
