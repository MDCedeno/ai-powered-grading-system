<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PLMUN Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../assets/css/professor.css" />
    <!-- PLMUN Logo -->
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="../../assets/images/apple-touch-icon.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="../../assets/images/favicon-32x32.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="../../assets/images/favicon-16x16.png" />
    <link rel="manifest" href="../../assets/images/site.webmanifest" />
</head>

<body>
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
                                    <th>Quiz</th>
                                    <th>Exam</th>
                                    <th>Project</th>
                                    <th>Final Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025-001</td>
                                    <td>Maria Santos</td>
                                    <td><input type="number" value="85" /></td>
                                    <td><input type="number" value="90" /></td>
                                    <td><input type="number" value="88" /></td>
                                    <td>88</td>
                                    <td><span class="status-tag pending">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>2025-002</td>
                                    <td>Jose Cruz</td>
                                    <td><input type="number" value="92" /></td>
                                    <td><input type="number" value="89" /></td>
                                    <td><input type="number" value="95" /></td>
                                    <td>92</td>
                                    <td><span class="status-tag done">Done</span></td>
                                </tr>
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
</body>

</html>