<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PLMUN Portal</title>
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
                    <p>Upload and manage grades, or view student progress analytics.</p>
                    <button class="btn-primary">Upload Grades</button>
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
    <!-- Tab Highlighting & Smooth Scroll -->
    <?php include '../../components/scroll.php'; ?>
</body>

</html>