<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PLMUN Portal</title>
  <link rel="stylesheet" href="../../assets/css/misadmin.css" />
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
        <p class="role-label">MIS Admin</p>
      </div>
      <nav class="sidebar-nav">
        <span class="nav-section-title">General</span>
        <ul>
          <li class="active"><a href="#dashboard">Dashboard</a></li>
          <li><a href="#student-records">Student Records</a></li>
          <li><a href="#professor-records">Professor Records</a></li>
          <li><a href="#department-reports">Department Reports</a></li>
          <li><a href="#audit-logs">Audit Logs</a></li>
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
        <h1>Dashboard</h1>
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
              <h4>Total Students</h4>
              <p class="metric">5,320</p>
              <span>Updated today</span>
            </div>
            <div class="card">
              <h4>Total Professors</h4>
              <p class="metric">320</p>
              <span>Active faculty members</span>
            </div>
            <div class="card">
              <h4>Pending Requests</h4>
              <p class="metric error">12</p>
              <span>Approval needed</span>
            </div>
            <div class="card">
              <h4>System Status</h4>
              <p class="status-online">Operational</p>
              <span>All services running</span>
            </div>
          </div>
        </section>

        <!-- ================= STUDENT RECORDS ================= -->
        <section id="student-records" class="tab-section hidden">
          <h2>Student Records</h2>
          <div class="toolbar">
            <input type="text" placeholder="Search student..." />
            <select>
              <option>Filter by Year</option>
              <option>1st Year</option>
              <option>2nd Year</option>
              <option>3rd Year</option>
              <option>4th Year</option>
            </select>
            <button class="btn-primary">Add Student</button>
          </div>

          <div class="user-table-container">
            <table>
              <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Name</th>
                  <th>Course</th>
                  <th>Status</th>
                  <th>Date Enrolled</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2025-00123</td>
                  <td>Maria Santos</td>
                  <td>BSIT</td>
                  <td><span class="status-tag active">Active</span></td>
                  <td>2025-08-20</td>
                  <td>
                    <button class="btn-icon">Edit</button>
                    <button class="btn-icon danger">Remove</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ================= PROFESSOR RECORDS ================= -->
        <section id="professor-records" class="tab-section hidden">
          <h2>Professor Records</h2>
          <div class="toolbar">
            <input type="text" placeholder="Search professor..." />
            <button class="btn-primary">Add Professor</button>
          </div>
          <div class="user-table-container">
            <table>
              <thead>
                <tr>
                  <th>Employee ID</th>
                  <th>Name</th>
                  <th>Department</th>
                  <th>Status</th>
                  <th>Date Hired</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>EMP-056</td>
                  <td>Juan Dela Cruz</td>
                  <td>Computer Studies</td>
                  <td><span class="status-tag active">Active</span></td>
                  <td>2020-01-12</td>
                  <td>
                    <button class="btn-icon">Edit</button>
                    <button class="btn-icon danger">Remove</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ================= DEPARTMENT REPORTS ================= -->
        <section id="department-reports" class="tab-section hidden">
          <h2>Department Reports</h2>
          <p>Generate and view department-level reports for analysis.</p>
          <button class="btn-primary">Generate Report</button>
        </section>

        <!-- ================= AUDIT LOGS ================= -->
        <section id="audit-logs" class="tab-section hidden">
          <h2>Audit Logs</h2>
          <div class="user-table-container">
            <table>
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>User</th>
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2025-09-01 21:05:33</td>
                  <td>mis_admin@plmun.edu</td>
                  <td>Updated student record</td>
                  <td><span class="status-tag success">Success</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ================= SETTINGS ================= -->
        <section id="settings" class="tab-section hidden">
          <h2>Settings</h2>
          <form class="settings-form">
            <label>
              Default Department:
              <select>
                <option>Computer Studies</option>
                <option>Business Administration</option>
                <option>Engineering</option>
              </select>
            </label>
            <label>
              Default Password Reset:
              <input type="text" value="resetme2025" />
            </label>
            <button class="btn-primary">Save Settings</button>
          </form>
        </section>
      </div>
    </main>
  </div>
  <!-- Tab Highlighting & Smooth Scroll -->
  <? include '../../components/scroll.php'; ?>
</body>
</html>