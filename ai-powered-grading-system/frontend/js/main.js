// Main JavaScript for AI-Powered Grading System

document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const tabSections = document.querySelectorAll('.tab-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);

            // Remove active class from all links and sections
            navLinks.forEach(l => l.parentElement.classList.remove('active'));
            tabSections.forEach(section => section.classList.add('hidden'));

            // Add active class to clicked link and show target section
            this.parentElement.classList.add('active');
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    // Load data for dashboards
    loadDashboardData();

    // Professor grade entry functionality
    if (document.getElementById('grade-form')) {
        setupGradeForm();
    }

    // Add event listeners for additional buttons and forms
    setupAdditionalEventHandlers();
});

function loadDashboardData() {
    const role = document.body.getAttribute('data-role');
    if (role === 'super-admin') {
        loadSuperAdminData();
    } else if (role === 'admin') {
        loadAdminData();
    } else if (role === 'professor') {
        loadProfessorData();
    } else if (role === 'student') {
        loadStudentData();
    }
}

function loadSuperAdminData() {
    // Removed to avoid conflict with inline script in super-admin.php
}

function loadLogs() {
    // Removed to avoid conflict with inline script in super-admin.php
}

function loadUsers() {
    // Removed to avoid conflict with inline script in super-admin.php
}

function loadSystemStats() {
    // Removed to avoid conflict with inline script in super-admin.php
}

function loadAdminData() {
    // Load students, professors, courses, grades
    fetch('../../backend/routes/api.php?path=/api/admin/students')
        .then(response => response.json())
        .then(data => {
            // Populate student table
            const studentTable = document.querySelector('#student-records table tbody');
            if (studentTable) {
                studentTable.innerHTML = data.map(student => `
                    <tr>
                        <td>${student.id}</td>
                        <td>${student.name}</td>
                        <td>${student.program}</td>
                        <td><span class="status-tag active">Active</span></td>
                        <td>${student.created_at || 'N/A'}</td>
                        <td>
                            <button class="btn-icon">Edit</button>
                            <button class="btn-icon danger">Remove</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            alert('Failed to load students.');
        });

    fetch('../../backend/routes/api.php?path=/api/admin/professors')
        .then(response => response.json())
        .then(data => {
            // Populate professor table
            const professorTable = document.querySelector('#professor-records table tbody');
            if (professorTable) {
                professorTable.innerHTML = data.map(professor => `
                    <tr>
                        <td>${professor.id}</td>
                        <td>${professor.name}</td>
                        <td>Computer Studies</td>
                        <td><span class="status-tag active">Active</span></td>
                        <td>${professor.created_at || 'N/A'}</td>
                        <td>
                            <button class="btn-icon">Edit</button>
                            <button class="btn-icon danger">Remove</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading professors:', error);
            alert('Failed to load professors.');
        });

    loadAdminLogs();
    loadAdminStats();
}

function loadAdminLogs() {
    fetch('../../backend/routes/api.php?path=/api/admin/audit-logs')
        .then(response => response.json())
        .then(data => {
            const logTable = document.querySelector('#audit-logs table tbody');
            if (logTable) {
                logTable.innerHTML = data.map(log => `
                    <tr>
                        <td>${log.timestamp}</td>
                        <td>${log.user_id}</td>
                        <td>${log.action}</td>
                        <td><span class="status-tag ${log.status === 'success' ? 'success' : 'error'}">${log.status}</span></td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading admin logs:', error);
            alert('Failed to load audit logs.');
        });
}

function loadAdminStats() {
    // Load stats for admin dashboard
    fetch('../../backend/routes/api.php?path=/api/admin/students')
        .then(response => response.json())
        .then(data => {
            const studentCount = document.querySelector('.card:nth-child(1) p');
            if (studentCount) studentCount.textContent = `${data.length} Students`;
        })
        .catch(error => console.error('Error loading student stats:', error));

    fetch('../../backend/routes/api.php?path=/api/admin/professors')
        .then(response => response.json())
        .then(data => {
            const professorCount = document.querySelector('.card:nth-child(2) p');
            if (professorCount) professorCount.textContent = `${data.length} Professors`;
        })
        .catch(error => console.error('Error loading professor stats:', error));

    fetch('../../backend/routes/api.php?path=/api/admin/courses')
        .then(response => response.json())
        .then(data => {
            const courseCount = document.querySelector('.card:nth-child(3) p');
            if (courseCount) courseCount.textContent = `${data.length} Courses`;
        })
        .catch(error => console.error('Error loading course stats:', error));

    fetch('../../backend/routes/api.php?path=/api/admin/grades')
        .then(response => response.json())
        .then(data => {
            const gradeCount = document.querySelector('.card:nth-child(4) p');
            if (gradeCount) gradeCount.textContent = `${data.length} Grades`;
        })
        .catch(error => console.error('Error loading grade stats:', error));
}

function loadProfessorData() {
    // Load courses for class management
    fetch('../../backend/routes/api.php?path=/api/professor/courses')
        .then(response => response.json())
        .then(data => {
            // Populate courses table
            const courseTable = document.querySelector('#class-management table tbody');
            if (courseTable) {
                courseTable.innerHTML = data.map(course => `
                    <tr>
                        <td>${course.code}</td>
                        <td>${course.name}</td>
                        <td>${course.schedule}</td>
                        <td>${course.enrolled || 'N/A'}</td>
                        <td>
                            <button class="btn-icon">View</button>
                            <button class="btn-icon danger">Remove</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            alert('Failed to load courses.');
        });

    // Load students and grades
    fetch('../../backend/routes/api.php?path=/api/professor/students')
        .then(response => response.json())
        .then(data => {
            // Populate student list
            const studentList = document.getElementById('student-list');
            if (studentList) {
                studentList.innerHTML = data.map(student => `
                    <option value="${student.id}">${student.name}</option>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            alert('Failed to load students.');
        });

    loadGrades();
}

function loadStudentData() {
    // Load student's grades and courses
    fetch('../../backend/routes/api.php?path=/api/student/grades')
        .then(response => response.json())
        .then(data => {
            // Populate grades table
            const gradesTable = document.querySelector('#grades-table tbody');
            if (gradesTable) {
                gradesTable.innerHTML = data.map(grade => `
                    <tr>
                        <td>${grade.course_name}</td>
                        <td>${grade.midterm_grade}</td>
                        <td>${grade.final_grade}</td>
                        <td>${grade.gpa}</td>
                    </tr>
                `).join('');
            }

            // Update dashboard cards with stats
            if (data.length > 0) {
                const gpaCard = document.querySelector('.card:nth-child(1) p');
                if (gpaCard) {
                    const avgGpa = data.reduce((sum, grade) => sum + parseFloat(grade.gpa || 0), 0) / data.length;
                    gpaCard.textContent = `${avgGpa.toFixed(2)} GPA`;
                }

                const passedCard = document.querySelector('.card:nth-child(2) p');
                if (passedCard) {
                    const passedCount = data.filter(grade => parseFloat(grade.gpa || 0) >= 2.0).length;
                    passedCard.textContent = `${passedCount} Passed`;
                }

                const atRiskCard = document.querySelector('.card:nth-child(3) p');
                if (atRiskCard) {
                    const atRiskCount = data.filter(grade => parseFloat(grade.gpa || 0) < 2.0).length;
                    atRiskCard.textContent = `${atRiskCount} At Risk`;
                }
            }
        })
        .catch(error => {
            console.error('Error loading grades:', error);
            alert('Failed to load grades.');
        });

    // Load notifications
    fetch('../../backend/routes/api.php?path=/api/student/notifications')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.querySelector('#notifications ul');
            if (notificationsList) {
                notificationsList.innerHTML = data.map(notification => `
                    <li>${notification.message}</li>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });

    // Load quizzes
    fetch('../../backend/routes/api.php?path=/api/student/quizzes')
        .then(response => response.json())
        .then(data => {
            const quizzesTable = document.querySelector('#quizzes table tbody');
            if (quizzesTable) {
                quizzesTable.innerHTML = data.map(quiz => `
                    <tr>
                        <td>${quiz.title}</td>
                        <td>${quiz.subject}</td>
                        <td><span class="status-tag ${quiz.status.toLowerCase()}">${quiz.status}</span></td>
                        <td><a href="quiz.php?id=${quiz.id}" class="btn-primary">${quiz.action}</a></td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading quizzes:', error);
        });
}

function setupGradeForm() {
    const form = document.getElementById('grade-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('../../backend/routes/api.php?path=/api/professor/grades', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Grade submitted successfully');
                loadGrades();
            } else {
                alert('Error submitting grade');
            }
        })
        .catch(error => {
            console.error('Error submitting grade:', error);
            alert('Error submitting grade');
        });
    });
}

function loadGrades() {
    fetch('../../backend/routes/api.php?path=/api/professor/grades')
        .then(response => response.json())
        .then(data => {
            const gradesTable = document.querySelector('#grades-table tbody');
            if (gradesTable) {
                gradesTable.innerHTML = data.map(grade => `
                    <tr>
                        <td>${grade.student_name}</td>
                        <td>${grade.course_name}</td>
                        <td>${grade.midterm_quizzes}</td>
                        <td>${grade.midterm_exam}</td>
                        <td>${grade.final_quizzes}</td>
                        <td>${grade.final_exam}</td>
                        <td>${grade.midterm_grade}</td>
                        <td>${grade.final_grade}</td>
                        <td>${grade.gpa}</td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading grades:', error);
            alert('Failed to load grades.');
        });
}

function deactivateUser(userId) {
    fetch('../../backend/routes/api.php?path=/api/superadmin/users/deactivate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('User deactivated');
            loadSuperAdminData();
        } else {
            alert('Error deactivating user');
        }
    })
    .catch(error => {
        console.error('Error deactivating user:', error);
        alert('Error deactivating user');
    });
}

function setupAdditionalEventHandlers() {
    // Add event listeners for buttons and forms that were missing

    // Example: Report generation button in admin dashboard
    const reportBtn = document.querySelector('#department-reports button');
    if (reportBtn) {
        reportBtn.addEventListener('click', () => {
            alert('Report generation is not yet implemented.');
        });
    }

    // Example: Save settings forms
    const settingsForms = document.querySelectorAll('.settings-form');
    settingsForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Settings saved (mock).');
        });
    });

    // Example: Edit user button placeholder
    const editButtons = document.querySelectorAll('.btn-icon');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            alert('Edit functionality is not yet implemented.');
        });
    });

    // Example: Announcement post form
    const announcementForm = document.querySelector('#announcements form');
    if (announcementForm) {
        announcementForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Announcement posted (mock).');
        });
    }

    // Example: AI config save form
    const aiConfigForm = document.querySelector('#ai-config form');
    if (aiConfigForm) {
        aiConfigForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('AI config saved (mock).');
        });
    }
}
