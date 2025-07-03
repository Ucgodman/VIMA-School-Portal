// Fetch all questions
function fetchQuestions() {
    fetch('modules/manage_question_backend.php?action=fetch')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderQuestions(data.data);
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Error fetching questions:', error));
}

// Fetch a specific question by ID
function fetchQuestionById(id) {
    fetch(`modules/manage_question_backend.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateQuestionForm(data.data);
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Error fetching question:', error));
}

// Render questions in the UI
function renderQuestions(questions) {
    const tableBody = document.getElementById('questionsTableBody');
    tableBody.innerHTML = ''; // Clear existing rows

    questions.forEach(question => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${question.id}</td>
            <td>${question.question}</td>
            <td>${question.marks}</td>
            <td>${question.class_name}</td>
            <td>${question.subject_name}</td>
            <td>
                <button onclick="editQuestion(${question.id})">Edit</button>
                <button onclick="deleteQuestion(${question.id})">Delete</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Populate the form for editing a question
function populateQuestionForm(question) {
    document.getElementById('questionId').value = question.id;
    document.getElementById('questionText').value = question.question;
    document.getElementById('marks').value = question.marks;
    // Populate other fields as needed
}

// Example edit button handler
function editQuestion(id) {
    fetchQuestionById(id);
}

// Example delete button handler
function deleteQuestion(id) {
    if (confirm('Are you sure you want to delete this question?')) {
        fetch('modules/manage_question_backend.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Question deleted successfully');
                fetchQuestions();
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Error deleting question:', error));
    }
}

// Initialize the page
document.addEventListener('DOMContentLoaded', fetchQuestions);