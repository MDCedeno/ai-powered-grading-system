<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PLMUN Portal</title>
  <link rel="stylesheet" href="../../assets/css/superadmin.css" />
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
        <p class="role-label">Super Admin</p>
      </div>
      <nav class="sidebar-nav">
        <span class="nav-section-title">General</span>
        <ul>
          <li class="active"><a href="#dashboard">System Dashboard</a></li>
          <li><a href="#user-roles">User Role Management</a></li>
          <li><a href="#database">Database Management</a></li>
          <li><a href="#audit-logs">Audit Logs</a></li>
          <li><a href="#ai-config">AI Module Config</a></li>
          <li><a href="#settings">System Settings</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../login.php" class="logout">Log Out</a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="main-header">
        <h1>System Dashboard</h1>
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
          <!-- Dashboard Cards -->
          <div class="cards-container">
            <div class="card">
              <h4>Server Status</h4>
              <p class="status-online">Online</p>
              <span>Uptime: 99.98%</span>
            </div>
            <div class="card">
              <h4>Active Users</h4>
              <p class="metric">1,204</p>
              <span>Across all roles</span>
            </div>
            <div class="card">
              <h4>Error Logs (24h)</h4>
              <p class="metric error">5</p>
              <span>Critical errors need attention</span>
            </div>
            <div class="card">
              <h4>Database Health</h4>
              <p class="status-healthy">Healthy</p>
              <span>Last Backup: 1h ago</span>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="activity-log-widget">
            <h3>Recent System Activity</h3>
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
                  <td>2025-09-01 19:50:12</td>
                  <td>admin_mis@plmun.edu</td>
                  <td>Generated department report</td>
                  <td><span class="status-tag success">Success</span></td>
                </tr>
              </tbody>
            </table>
            <p class="note">
              View detailed logs under <a href="#audit-logs">Audit Logs</a>.
            </p>
          </div>
        </section>

        <!-- ================= USER ROLE MANAGEMENT ================= -->
        <section id="user-roles" class="tab-section hidden">
          <h2>User Role Management</h2>
          <div class="toolbar">
            <input
              type="text"
              placeholder="Search by name, email, or ID..." />
            <select>
              <option>Filter by Role</option>
              <option>Super Admin</option>
              <option>Admin</option>
              <option>Professor</option>
              <option>Student</option>
            </select>
            <select>
              <option>Sort by</option>
              <option>Name (A-Z)</option>
              <option>Date Created</option>
            </select>
            <button class="btn-primary">Add New User</button>
          </div>

          <div class="user-table-container">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Date Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Jose Rivas</td>
                  <td>joserivas@gmail.com</td>
                  <td>Student</td>
                  <td><span class="status-tag active">Active</span></td>
                  <td>2025-08-15</td>
                  <td>
                    <button class="btn-icon">Edit</button>
                    <button class="btn-icon danger">Deactivate</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ================= DATABASE MANAGEMENT ================= -->
        <section id="database" class="tab-section hidden">
          <h2>Database Management</h2>
          <div class="cards-container">
            <div class="card">
              <h4>Database Size</h4>
              <p class="metric">1.2 GB</p>
              <span>Updated just now</span>
            </div>
            <div class="card">
              <h4>Last Backup</h4>
              <p class="status-healthy">1 hour ago</p>
              <span>Auto-backup enabled</span>
            </div>
            <div class="card">
              <h4>Restore Point</h4>
              <button class="btn-primary">Restore</button>
            </div>
          </div>
        </section>

        <!-- ================= AUDIT LOGS ================= -->
        <section id="audit-logs" class="tab-section hidden">
          <h2>Audit Logs</h2>
          <div class="toolbar">
            <input type="text" placeholder="Search logs..." />
            <select>
              <option>Filter by Status</option>
              <option>Success</option>
              <option>Failed</option>
            </select>
            <select>
              <option>Sort by</option>
              <option>Newest First</option>
              <option>Oldest First</option>
            </select>
          </div>
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
                  <td>prof_garcia@plmun.edu</td>
                  <td>Logged in</td>
                  <td><span class="status-tag success">Success</span></td>
                </tr>
                <tr>
                  <td>2025-09-01 20:44:12</td>
                  <td>admin_mis@plmun.edu</td>
                  <td>Failed login attempt</td>
                  <td><span class="status-tag error">Failed</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ================= AI MODULE CONFIG ================= -->
        <section id="ai-config" class="tab-section hidden">
          <h2>AI Module Configuration</h2>
          <form class="ai-config-form">
            <label>
              <input type="checkbox" checked />
              Enable AI Grading
            </label>
            <label>
              <input type="checkbox" checked />
              Enable AI Quiz Generator
            </label>
            <label>
              <input type="checkbox" />
              Enable AI Analytics Insights
            </label>
            <button class="btn-primary">Save Changes</button>
          </form>
        </section>

        <!-- ================= SYSTEM SETTINGS ================= -->
        <section id="settings" class="tab-section hidden">
          <h2>System Settings</h2>
          <form class="settings-form">
            <label>
              System Name:
              <input type="text" value="PLMUN Portal" />
            </label>
            <label>
              Theme Color:
              <input type="color" value="#217589" />
            </label>
            <label>
              Default Password Reset:
              <input type="text" value="changeme123" />
            </label>
            <button class="btn-primary">Update Settings</button>
          </form>
        </section>
      </div>
    </main>
  </div>
  <!-- Tab Highlighting & Smooth Scroll -->
  <?php include '../../components/scroll.php'; ?>
</body>

</html>