<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PLMUN Portal</title>
  <link rel="stylesheet" href="../../assets/css/student.css" />
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
        <p class="role-label">Student</p>
      </div>
      <nav class="sidebar-nav">
        <span class="nav-section-title">General</span>
        <ul>
          <li class="active"><a href="#dashboard">Dashboard</a></li>
          <li><a href="#grades">Grade Breakdown</a></li>
          <li><a href="#insights">Performance Insights</a></li>
          <li><a href="#progress">Progress Tracker</a></li>
          <li><a href="#notifications">Notifications</a></li>
          <li><a href="#quizzes">AI Quizzes</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../login.php" class="logout">Log Out</a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="main-header">
        <h1>Student Dashboard</h1>
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
          <h2>Visual Grade Summary</h2>
          <div class="cards-container">
            <div class="card">
              <h4>Current GPA</h4>
              <p class="metric">3.45</p>
              <span>Updated this semester</span>
            </div>
            <div class="card">
              <h4>Subjects Passed</h4>
              <p class="metric">24</p>
              <span>Overall</span>
            </div>
            <div class="card">
              <h4>At-Risk Subjects</h4>
              <p class="metric error">2</p>
              <span>Needs improvement</span>
            </div>
          </div>
        </section>

        <!-- ================= GRADE BREAKDOWN ================= -->
        <section id="grades" class="tab-section hidden">
          <h2>Grade Breakdown</h2>

          <!-- Toolbar -->
          <div class="toolbar">
            <input type="text" placeholder="Search subject or activity..." />
            <select>
              <option>Filter by Subject</option>
              <option>IT 101</option>
              <option>CS 201</option>
              <option>Math 201</option>
            </select>
            <button class="btn-primary">Apply</button>
          </div>

          <!-- Table -->
          <div class="user-table-container">
            <table>
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Activity</th>
                  <th>Score</th>
                  <th>Weight</th>
                  <th>Final Grade</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>IT 101</td>
                  <td>Midterm Exam</td>
                  <td>85/100</td>
                  <td>40%</td>
                  <td>34/40</td>
                  <td><span class="status-tag done">Done</span></td>
                </tr>
                <tr>
                  <td>CS 201</td>
                  <td>Programming Quiz</td>
                  <td>18/25</td>
                  <td>10%</td>
                  <td>7/10</td>
                  <td><span class="status-tag pending">Pending</span></td>
                </tr>
                <tr>
                  <td>Math 201</td>
                  <td>Assignment 2</td>
                  <td>12/20</td>
                  <td>5%</td>
                  <td>3/5</td>
                  <td><span class="status-tag late">Late</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ================= PERFORMANCE INSIGHTS ================= -->
        <section id="insights" class="tab-section hidden">
          <h2>Performance Insights</h2>

          <div class="insights-layout">
            <!-- Insight Cards -->
            <div class="insight-cards">
              <div class="insight-card focus">
                <h4>Focus Area</h4>
                <p>Mathematics</p>
              </div>
              <div class="insight-card improve">
                <h4>Needs Improvement</h4>
                <p>Programming Logic</p>
              </div>
              <div class="insight-card strength">
                <h4>Strength</h4>
                <p>Database Systems</p>
              </div>
            </div>

            <!-- Radar Chart -->
            <div class="chart-container">
              <canvas id="insightsChart"></canvas>
            </div>
          </div>
        </section>

        <!-- ================= PROGRESS TRACKER ================= -->
        <section id="progress" class="tab-section hidden">
          <h2>Progress Tracker</h2>

          <!-- Chart Container -->
          <div class="progress-chart">
            <canvas id="progressChart" width="400" height="180"></canvas>
          </div>

          <!-- Summary Table -->
          <table>
            <thead>
              <tr>
                <th>Semester</th>
                <th>Your GPA</th>
                <th>Class Average</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1st Year - Sem 1</td>
                <td>3.20</td>
                <td>3.10</td>
              </tr>
              <tr>
                <td>1st Year - Sem 2</td>
                <td>3.35</td>
                <td>3.18</td>
              </tr>
              <tr>
                <td>2nd Year - Sem 1</td>
                <td>3.40</td>
                <td>3.22</td>
              </tr>
              <tr>
                <td>2nd Year - Sem 2</td>
                <td>3.45</td>
                <td>3.25</td>
              </tr>
            </tbody>
          </table>
        </section>

        <!-- ================= NOTIFICATIONS ================= -->
        <section id="notifications" class="tab-section hidden">
          <h2>Notifications</h2>
          <ul class="notifications-list">
            <li>📢 New feedback from Prof. Garcia on IT 101.</li>
            <li>⚠️ Midterm results available for Math 201.</li>
          </ul>
        </section>

        <!-- ================= AI QUIZZES ================= -->
        <section id="quizzes" class="tab-section hidden">
          <h2>AI Quizzes</h2>
          <p>Select a quiz assigned by your professor:</p>

          <div class="quiz-list">
            <table>
              <thead>
                <tr>
                  <th>Quiz Title</th>
                  <th>Subject</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Midterm Review Quiz</td>
                  <td>IT 101</td>
                  <td><span class="status-tag active">In Progress</span></td>
                  <td><a href="quiz.php?id=1" class="btn-primary">Continue</a></td>
                </tr>
                <tr>
                  <td>Programming Basics Quiz</td>
                  <td>CS 201</td>
                  <td><span class="status-tag done">Done</span></td>
                  <td><a href="quiz-results.php?id=2" class="btn-primary">View Results</a></td>
                </tr>
                <tr>
                  <td>Database Quiz</td>
                  <td>IT 202</td>
                  <td><span class="status-tag pending">Pending</span></td>
                  <td><a href="quiz.php?id=3" class="btn-primary">Take Quiz</a></td>
                </tr>
                <tr>
                  <td>Networking Quiz</td>
                  <td>CS 301</td>
                  <td><span class="status-tag late">Late</span></td>
                  <td><a href="#" class="btn-primary disabled">Locked</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const insightsCtx = document.getElementById('insightsChart').getContext('2d');
    new Chart(insightsCtx, {
      type: 'radar',
      data: {
        labels: ['Math', 'Programming Logic', 'Database', 'Networking', 'Web Dev'],
        datasets: [{
          label: 'Your Performance',
          data: [70, 65, 85, 75, 90],
          backgroundColor: 'rgba(33, 117, 137, 0.2)', // PLMUN blue transparent
          borderColor: '#217589',
          pointBackgroundColor: '#f3b642', // Portal Yellow points
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        scales: {
          r: {
            angleLines: {
              color: '#e5e7eb'
            },
            suggestedMin: 0,
            suggestedMax: 100,
            ticks: {
              stepSize: 20
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  </script>

  <!-- Chart.js for Progress Tracker -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctxProgress = document.getElementById('progressChart').getContext('2d');
    new Chart(ctxProgress, {
      type: 'line',
      data: {
        labels: ['Y1-Sem1', 'Y1-Sem2', 'Y2-Sem1', 'Y2-Sem2'],
        datasets: [{
            label: 'Your GPA',
            data: [3.20, 3.35, 3.40, 3.45],
            borderColor: '#217589', // PLMUN Blue
            backgroundColor: 'rgba(33,117,137,0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 2
          },
          {
            label: 'Class Average',
            data: [3.10, 3.18, 3.22, 3.25],
            borderColor: '#f3b642', // Portal Yellow
            backgroundColor: 'rgba(243,182,66,0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: {
              color: '#4d808d',
              font: {
                size: 13
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            min: 2.5,
            max: 4.0,
            ticks: {
              color: '#4d808d'
            }
          },
          x: {
            ticks: {
              color: '#4d808d'
            }
          }
        }
      }
    });
  </script>
  <!-- Tab Highlighting & Smooth Scroll -->
  <?php include '../../components/scroll.php'; ?>

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

    // Load grades
    function loadGrades() {
      fetch('../../../backend/routes/api.php/api/student/grades')
        .then(response => response.json())
        .then(data => {
          const tbody = document.querySelector('#grades table tbody');
          tbody.innerHTML = '';
          data.forEach(grade => {
            const row = `<tr>
              <td>${grade.subject}</td>
              <td>${grade.activity}</td>
              <td>${grade.score}</td>
              <td>${grade.weight}</td>
              <td>${grade.final}</td>
              <td><span class="status-tag done">Done</span></td>
            </tr>`;
            tbody.innerHTML += row;
          });
        });
    }

    // Load quizzes
    function loadQuizzes() {
      fetch('../../../backend/routes/api.php/api/student/quizzes')
        .then(response => response.json())
        .then(data => {
          const tbody = document.querySelector('#quizzes table tbody');
          tbody.innerHTML = '';
          data.forEach(quiz => {
            const row = `<tr>
              <td>${quiz.title}</td>
              <td>${quiz.subject}</td>
              <td><span class="status-tag ${quiz.status.toLowerCase()}">${quiz.status}</span></td>
              <td><a href="quiz.php?id=${quiz.id}" class="btn-primary">${quiz.action}</a></td>
            </tr>`;
            tbody.innerHTML += row;
          });
        });
    }

    // Load notifications
    function loadNotifications() {
      fetch('../../../backend/routes/api.php/api/student/notifications')
        .then(response => response.json())
        .then(data => {
          const ul = document.querySelector('#notifications ul');
          ul.innerHTML = '';
          data.forEach(notif => {
            const li = `<li>${notif.message}</li>`;
            ul.innerHTML += li;
          });
        });
    }

    // Load data when sections are shown
    document.querySelector('a[href="#grades"]').addEventListener('click', loadGrades);
    document.querySelector('a[href="#quizzes"]').addEventListener('click', loadQuizzes);
    document.querySelector('a[href="#notifications"]').addEventListener('click', loadNotifications);
  </script>
</body>

</html>
