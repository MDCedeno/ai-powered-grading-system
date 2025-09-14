<?php 
session_start();
include '../../components/header.php';
?>

<body data-role="professor">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <span class="plmun">PLMUN</span><span class="portal">Portal</span>
                <p class="role-label">Professor</p>
            </div>
            <nav class="sidebar-nav">
                <span class="nav-section-title">General</span>
                <ul>
                    <li class="active"><a href="#dashboard">Dashboard</a></li>
                    <li><a href="#class-management">Class Management</a></li>
                    <li><a href="#student-performance">Student Performance</a></li>
                    <li><a href="#ai-quizzes">AI Quizzes</a></li>
                    <li><a href="#announcements">Announcements</a></li>
                    <li><a href="#settings">Settings</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../login.php" class="logout">Log Out</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1>Professor Dashboard</h1>
                <div class="user-profile">
                    <span>
                        <?php echo $_SESSION['user_name'] ?? 'Guest'; ?>
                        (<?php echo $_SESSION['role'] ?? 'Role'; ?>)
                    </span>
                    <img src="../../assets/images/Thug.jpg" alt="User Avatar" />
                </div>
            </header>

            <div class="content-body">
                <!-- ================= DASHBOARD ================= -->
                <section id="dashboard" class="tab-section active">
                    <div class="cards-container">
                        <div class="card">
                            <h4>Subjects Handled</h4>
                            <p class="metric">5</p>
                            <span>Current semester</span>
                        </div>
                        <div class="card">
                            <h4>Classes Today</h4>
                            <p class="metric">2</p>
                            <span>Updated live</span>
                        </div>
                        <div class="card">
                            <h4>Pending Grade Submissions</h4>
                            <p class="metric error">3</p>
                            <span>Due soon</span>
                        </div>
                        <div class="card">
                            <h4>Announcements</h4>
                            <p class="status-online">2 New</p>
                            <span>Unread updates</span>
                        </div>
                    </div>
                </section>

                <!-- ================= CLASS MANAGEMENT ================= -->
                <section id="class-management" class="tab-section hidden">
                    <h2>Class Management</h2>
                    <div class="toolbar">
                        <input type="text" placeholder="Search class..." />
                        <button class="btn-primary">Add Class</button>
                    </div>
                    <div class="user-table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Class Code</th>
                                    <th>Subject</th>
                                    <th>Schedule</th>
                                    <th>Enrolled Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>IT-401</td>
                                    <td>Web Systems</td>
                                    <td>MWF 1:00-2:30</td>
                                    <td>45</td>
                                    <td>
                                        <button class="btn-icon">View</button>
                                        <button class="btn-icon danger">Remove</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ================= STUDENT PERFORMANCE ================= -->
                <section id="student-performance" class="tab-section hidden">
                    <h2>Student Performance</h2>

                    <!-- ======== GRADE ENTRY ======== -->
                    <div class="sub-section grade-entry">
                        <h3>Grade Entry</h3>
                        <div class="toolbar">
                            <input type="text" placeholder="Search student..." />
                            <select>
                                <option>Filter by Section</option>
                                <option>BSIT 4A</option>
                                <option>BSIT 4B</option>
                            </select>
                            <button class="btn-primary">Load Class</button>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Midterm Quiz</th>
                                    <th>Midterm Exam</th>
                                    <th>Final Quiz</th>
                                    <th>Final Exam</th>
                                    <th>Final Grade</th>
                                    <th>GPA</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="grades-tbody">
                                <!-- Grades will be loaded dynamically -->
                            </tbody>
                        </table>

                        <div class="btn-group">
                            <button class="btn-primary">Save Draft</button>
                            <button class="btn-primary">Submit Grades</button>
                        </div>
                    </div>

                    <!-- ======== PERFORMANCE ANALYTICS ======== -->
                    <div class="sub-section performance-analytics">
                        <h3>Performance Analytics</h3>
                        <canvas id="performanceChart" width="400" height="150"></canvas>

                        <table>
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Average</th>
                                    <th>Pass Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Web Systems</td>
                                    <td>89%</td>
                                    <td><span class="status-tag done">92%</span></td>
                                </tr>
                                <tr>
                                    <td>Database</td>
                                    <td>82%</td>
                                    <td><span class="status-tag pending">78%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>


                <!-- ================= AI QUIZZES ================= -->
                <section id="ai-quizzes" class="tab-section hidden">
                    <h2>AI Quiz Generator</h2>
                    <form>
                        <label>
                            Select Subject:
                            <select>
                                <option>Web Systems</option>
                                <option>Database Systems</option>
                            </select>
                        </label>
                        <button class="btn-primary">Generate Quiz</button>
                    </form>
                </section>

                <!-- ================= ANNOUNCEMENTS ================= -->
                <section id="announcements" class="tab-section hidden">
                    <h2>Announcements</h2>
                    <form>
                        <label>
                            Message:
                            <textarea rows="4" placeholder="Write announcement..."></textarea>
                        </label>
                        <button class="btn-primary">Post Announcement</button>
                    </form>
                </section>

                <!-- ================= SETTINGS ================= -->
                <section id="settings" class="tab-section hidden">
                    <h2>Settings</h2>
                    <form class="settings-form">
                        <label>
                            Display Name:
                            <input type="text" value="Prof. Juan Dela Cruz" />
                        </label>
                        <label>
                            Email:
                            <input type="text" value="prof.juan@plmun.edu" />
                        </label>
                        <label>
                            Change Password:
                            <input type="password" placeholder="New password" />
                        </label>
                        <button class="btn-primary">Save Changes</button>
                    </form>
                </section>
            </div>
        </main>
    </div>
    <!-- Chart.js Script for Analytics -->
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Prelim', 'Midterm', 'Final'],
                datasets: [{
                    label: 'Class Average',
                    data: [82, 85, 88],
                    backgroundColor: ['#217589', '#f3b642', '#4d808d'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
    <!-- Tab Highlighting & Smooth Scroll -->
    <?php include '../../components/scroll.php'; ?>

    <script src="../../js/main.js"></script>
    <script>
        // Tab switching
        const navLinks = document.querySelectorAll('.sidebar-nav a');
        const sections = document.querySelectorAll('.tab-section');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                sections.forEach(section => {
                    section.classList.remove('active');
                    section.classList.add('hidden');
                });
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                    targetSection.classList.add('active');
                }
            });
        });

        // Load courses for class management
        function loadCourses() {
            fetch('../../backend/router.php/api/professor/courses')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#class-management table tbody');
                    tbody.innerHTML = '';
                    data.forEach(course => {
                        const row = `<tr>
                            <td>${course.code}</td>
                            <td>${course.name}</td>
                            <td>${course.schedule}</td>
                            <td>${course.enrolled || 'N/A'}</td>
                            <td>
                                <button class="btn-icon">View</button>
                                <button class="btn-icon danger">Remove</button>
                            </td>
                        </tr>`;
                        tbody.innerHTML += row;
                    });
                });
        }

        // Load grades for student performance
        function loadGrades() {
            fetch('../../backend/router.php/api/professor/grades')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('grades-tbody');
                    tbody.innerHTML = '';
                    data.forEach(grade => {
                        const row = `<tr data-student-id="${grade.student_id}" data-course-id="${grade.course_id}">
                            <td>${grade.student_id}</td>
                            <td>${grade.student_name || 'N/A'}</td>
                            <td><input type="number" class="midterm-quiz" value="${grade.midterm_quizzes || ''}" /></td>
                            <td><input type="number" class="midterm-exam" value="${grade.midterm_exam || ''}" /></td>
                            <td><input type="number" class="final-quiz" value="${grade.final_quizzes || ''}" /></td>
                            <td><input type="number" class="final-exam" value="${grade.final_exam || ''}" /></td>
                            <td>${grade.final_grade || ''}</td>
                            <td>${grade.gpa || ''}</td>
                            <td><span class="status-tag ${grade.final_grade ? 'done' : 'pending'}">${grade.final_grade ? 'Done' : 'Pending'}</span></td>
                        </tr>`;
                        tbody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error loading grades:', error));
        }

        // Submit grades
        document.querySelector('.btn-group .btn-primary:last-child').addEventListener('click', () => {
            const rows = document.querySelectorAll('#grades-tbody tr');
            rows.forEach(row => {
                const studentId = row.dataset.studentId;
                const courseId = row.dataset.courseId;
                const midtermQuiz = row.querySelector('.midterm-quiz').value;
                const midtermExam = row.querySelector('.midterm-exam').value;
                const finalQuiz = row.querySelector('.final-quiz').value;
                const finalExam = row.querySelector('.final-exam').value;

                if (midtermQuiz && midtermExam && finalQuiz && finalExam) {
                    fetch('../../backend/router.php/api/professor/grades', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            student_id: studentId,
                            course_id: courseId,
                            midterm_quizzes: parseFloat(midtermQuiz),
                            midterm_exam: parseFloat(midtermExam),
                            final_quizzes: parseFloat(finalQuiz),
                            final_exam: parseFloat(finalExam)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Grade submitted successfully');
                            loadGrades(); // Reload to show computed grades
                        } else {
                            alert('Error submitting grade');
                        }
                    })
                    .catch(error => console.error('Error submitting grade:', error));
                }
            });
        });

        // AI Quiz generation
        document.querySelector('#ai-quizzes form').addEventListener('submit', (e) => {
            e.preventDefault();
            const topic = document.querySelector('#ai-quizzes select').value;
            fetch('http://localhost:5000/quiz', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ topic, num_questions: 5 })
            })
            .then(response => response.json())
            .then(data => {
                alert('Quiz generated: ' + JSON.stringify(data.questions));
            })
            .catch(error => console.error('Error:', error));
        });

        // Announcements post
        document.querySelector('#announcements form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Announcement posted (mock).');
        });

        // Settings save
        document.querySelector('#settings form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Settings saved (mock).');
        });

        // Load data when sections are shown
        document.querySelector('a[href="#class-management"]').addEventListener('click', loadCourses);
        document.querySelector('a[href="#student-performance"]').addEventListener('click', loadGrades);
    </script>
</body>

</html>
