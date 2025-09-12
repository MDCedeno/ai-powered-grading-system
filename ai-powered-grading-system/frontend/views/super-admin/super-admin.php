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

<body data-role="super-admin">
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
          <li><a href="#dashboard">System Dashboard</a></li>
          <li class="active"><a href="#user-roles">User Role Management</a></li>
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
        <section id="dashboard" class="tab-section hidden">
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
        <section id="user-roles" class="tab-section active">
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
                <!-- User data will be loaded here dynamically -->
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

      <!-- Edit User Modal -->
      <div id="edit-user-modal" class="modal hidden">
        <div class="modal-content">
          <span class="close-modal">&times;</span>
          <h2>Edit User</h2>
          <form id="edit-user-form">
            <input type="hidden" id="edit-user-id" />
            <label for="edit-user-name">Name:</label>
            <input type="text" id="edit-user-name" required />
            <label for="edit-user-email">Email:</label>
            <input type="email" id="edit-user-email" required />
            <label for="edit-user-role">Role:</label>
            <select id="edit-user-role" required>
              <option value="1">Super Admin</option>
              <option value="2">MIS Admin</option>
              <option value="3">Professor</option>
              <option value="4">Student</option>
            </select>
            <button type="submit" class="btn-primary">Save Changes</button>
          </form>
        </div>
      </div>
    </main>
  </div>
  <!-- Tab Highlighting & Smooth Scroll -->
  <?php include '../../components/scroll.php'; ?>

  <script src="../../js/main.js"></script>
  <script>
    // Tab switching
    if (!window.navLinks) {
      window.navLinks = document.querySelectorAll('.sidebar-nav a');
      window.sections = document.querySelectorAll('.tab-section');

      window.navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const targetId = link.getAttribute('href').substring(1);
          window.sections.forEach(section => {
            section.classList.remove('active');
            section.classList.add('hidden');
          });
          const targetSection = document.getElementById(targetId);
          if (targetSection) {
            targetSection.classList.remove('hidden');
            targetSection.classList.add('active');
          }
          // Update nav active
          window.navLinks.forEach((l) => l.parentElement.classList.remove("active"));
          link.parentElement.classList.add("active");
        });
      });
    }

      // Load system stats for dashboard
      function loadStats() {
      fetch('/backend/routes/api.php?path=/api/superadmin/stats')
          .then(response => response.json())
          .then(data => {
            // Update Server Status
            const serverStatus = document.querySelector('.card:nth-child(1) p');
            serverStatus.textContent = data.server_status;
            serverStatus.className = data.server_status === 'Online' ? 'status-online' : 'status-offline';

            // Update Uptime
            const uptimeSpan = document.querySelector('.card:nth-child(1) span');
            uptimeSpan.textContent = 'Uptime: ' + data.uptime;

            // Update Active Users
            const activeUsers = document.querySelector('.card:nth-child(2) .metric');
            activeUsers.textContent = data.users;

            // Update Error Logs (24h)
            const errorLogs = document.querySelector('.card:nth-child(3) .metric');
            errorLogs.textContent = data.error_logs_24h;

            // Update Database Health
            const dbHealth = document.querySelector('.card:nth-child(4) p');
            dbHealth.textContent = data.db_health;
            dbHealth.className = data.db_health === 'Healthy' ? 'status-healthy' : 'status-unhealthy';

            // Update Last Backup
            const lastBackupSpan = document.querySelector('.card:nth-child(4) span');
            lastBackupSpan.textContent = 'Last Backup: ' + data.last_backup;
          })
          .catch(err => console.error('Failed to load stats:', err));
      }

      function loadLogs() {
        const search = document.querySelector('#audit-logs input[type="text"]').value;
        const status = document.querySelector('#audit-logs select:nth-of-type(1)').value;
        const sort = document.querySelector('#audit-logs select:nth-of-type(2)').value;
        const params = new URLSearchParams({
          search: search,
          status: status !== 'Filter by Status' ? status.toLowerCase() : '',
          sort: sort !== 'Sort by' ? sort.toLowerCase().replace(' first', '') : 'newest'
        });
      fetch(`/backend/routes/api.php?path=/api/superadmin/logs&${params}`)
          .then(response => response.json())
          .then(data => {
            const tbody = document.querySelector('#audit-logs table tbody');
            tbody.innerHTML = '';
            data.forEach(log => {
              const row = `<tr>
                <td>${log.timestamp}</td>
                <td>${log.user_id}</td>
                <td>${log.action}</td>
                <td><span class="status-tag ${log.status === 'success' ? 'success' : 'error'}">${log.status}</span></td>
              </tr>`;
              tbody.innerHTML += row;
            });
          })
          .catch(err => console.error('Failed to load logs:', err));
      }

    function getRoleName(role_id) {
      const roles = {
        1: 'Super Admin',
        2: 'MIS Admin',
        3: 'Professor',
        4: 'Student'
      };
      return roles[role_id] || 'Unknown';
    }

      function deactivateUser(userId) {
        fetch('/backend/routes/api.php?path=/api/superadmin/users/deactivate', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              user_id: userId
            })
          })
          .then(response => response.json())
          .then(data => {
            alert(data.success ? 'User deactivated' : 'Failed');
            loadUsers();
          });
      }

      function activateUser(userId) {
        fetch('/backend/routes/api.php?path=/api/superadmin/users/activate', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              user_id: userId
            })
          })
          .then(response => response.json())
          .then(data => {
            alert(data.success ? 'User activated' : 'Failed');
            loadUsers();
          });
      }

      function editUser(userId) {
        // Fetch user data and populate modal
        fetch(`/backend/routes/api.php?path=/api/superadmin/users`)
          .then(response => response.json())
          .then(data => {
            const user = data.find(u => u.id == userId);
            if (user) {
              document.getElementById('edit-user-id').value = user.id;
              document.getElementById('edit-user-name').value = user.name;
              document.getElementById('edit-user-email').value = user.email;
              document.getElementById('edit-user-role').value = user.role_id;
              document.getElementById('edit-user-modal').classList.remove('hidden');
            }
          });
      }

    // Close modal
    document.querySelector('.close-modal').addEventListener('click', () => {
      document.getElementById('edit-user-modal').classList.add('hidden');
    });

    // Edit user form submit
    document.getElementById('edit-user-form').addEventListener('submit', (e) => {
      e.preventDefault();
      const userId = document.getElementById('edit-user-id').value;
      const name = document.getElementById('edit-user-name').value;
      const email = document.getElementById('edit-user-email').value;
      const role = document.getElementById('edit-user-role').value;
      fetch(`/backend/routes/api.php?path=/api/superadmin/users/${userId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            name,
            email,
            role_id: role
          })
        })
        .then(response => response.json())
        .then(data => {
          alert(data.success ? 'User updated' : 'Failed');
          document.getElementById('edit-user-modal').classList.add('hidden');
          loadUsers();
        });
    });

      // Load AI config
      function loadAIConfig() {
        fetch('/backend/routes/api.php?path=/api/superadmin/ai-config')
          .then(response => response.json())
          .then(data => {
            const form = document.querySelector('#ai-config form');
            form.querySelector('input[type="checkbox"]:nth-of-type(1)').checked = data.enabled;
            // Update other fields if needed
          });
      }

      // Load system settings
      function loadSystemSettings() {
        fetch('/backend/routes/api.php?path=/api/superadmin/settings')
          .then(response => response.json())
          .then(data => {
            const form = document.querySelector('#settings form');
            form.querySelector('input[type="text"]').value = data.site_name;
            // Update other fields
          });
      }

    // Load data when sections are shown
    document.querySelector('a[href="#user-roles"]').addEventListener('click', loadUsers);
    document.querySelector('a[href="#audit-logs"]').addEventListener('click', loadLogs);
    document.querySelector('a[href="#ai-config"]').addEventListener('click', loadAIConfig);
    document.querySelector('a[href="#settings"]').addEventListener('click', loadSystemSettings);

    // Search functionality for users
    document.querySelector('#user-roles input[type="text"]').addEventListener('input', (e) => {
      loadUsers();
    });

    // Filter functionality for users
    document.querySelector('#user-roles select:nth-of-type(1)').addEventListener('change', (e) => {
      loadUsers();
    });

    // Sort functionality for users
    document.querySelector('#user-roles select:nth-of-type(2)').addEventListener('change', (e) => {
      loadUsers();
    });

    // Search functionality for logs
    document.querySelector('#audit-logs input[type="text"]').addEventListener('input', (e) => {
      loadLogs();
    });

    // Filter functionality for logs
    document.querySelector('#audit-logs select:nth-of-type(1)').addEventListener('change', (e) => {
      loadLogs();
    });

    // Sort functionality for logs
    document.querySelector('#audit-logs select:nth-of-type(2)').addEventListener('change', (e) => {
      loadLogs();
    });



    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        fetch(`../../backend/routes/api.php?path=/api/superadmin/users/${userId}`, {
            method: 'DELETE'
          })
          .then(response => response.json())
          .then(data => {
            alert(data.success ? 'User deleted' : 'Failed');
            loadUsers();
          });
      }
    }

    // Update loadUsers to include delete button
    function loadUsers() {
      const search = document.querySelector('#user-roles input[type="text"]').value;
      const role = document.querySelector('#user-roles select:nth-of-type(1)').value;
      const sort = document.querySelector('#user-roles select:nth-of-type(2)').value;
      const params = new URLSearchParams({
        search: search,
        role: role !== 'Filter by Role' ? role : '',
        sort: sort !== 'Sort by' ? sort.toLowerCase().replace(' (a-z)', '').replace('date created', 'date') : 'name'
      });
      fetch(`/backend/routes/api.php?path=/api/superadmin/users&${params}`)
        .then(response => response.json())
        .then(data => {
          const tbody = document.querySelector('#user-roles table tbody');
          tbody.innerHTML = '';
          data.forEach(user => {
            const roleName = getRoleName(user.role_id);
            const status = user.active ? 'Active' : 'Inactive';
            const statusClass = user.active ? 'active' : 'error';
            const row = `<tr>
              <td>${user.name}</td>
              <td>${user.email}</td>
              <td>${roleName}</td>
              <td><span class="status-tag ${statusClass}">${status}</span></td>
              <td>${user.created_at || 'N/A'}</td>
              <td>
                <button class="btn-icon" onclick="editUser(${user.id})">Edit</button>
                ${user.active ? `<button class="btn-icon danger" onclick="deactivateUser(${user.id})">Deactivate</button>` : `<button class="btn-icon" onclick="activateUser(${user.id})">Activate</button>`}
                <button class="btn-icon danger" onclick="deleteUser(${user.id})">Delete</button>
              </td>
            </tr>`;
            tbody.innerHTML += row;
          });
        })
        .catch(err => console.error('Failed to load users:', err));
    }

      // Backup database button
      document.querySelector('#database .card:nth-child(3) button').addEventListener('click', () => {
        fetch('/backend/routes/api.php?path=/api/superadmin/backup', {
            method: 'POST'
          })
          .then(response => response.json())
          .then(data => {
            alert(data.success ? 'Backup created: ' + data.file : 'Backup failed');
          });
      });

    // Export logs button
    const exportBtn = document.createElement('button');
    exportBtn.textContent = 'Export Logs';
    exportBtn.className = 'btn-primary';
    document.querySelector('#audit-logs .toolbar').appendChild(exportBtn);
    exportBtn.addEventListener('click', () => {
      fetch('/backend/routes/api.php?path=/api/superadmin/logs')
        .then(response => response.json())
        .then(data => {
          const csv = 'Timestamp,User,Action,Status\n' + data.map(log => `${log.timestamp},${log.user_id},${log.action},${log.status}`).join('\n');
          const blob = new Blob([csv], {
            type: 'text/csv'
          });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'audit_logs.csv';
          a.click();
          window.URL.revokeObjectURL(url);
        });
    });

    // Load dashboard stats on page load
    loadStats();

    // Load users on page load
    loadUsers();

    // Refresh user list every 5 seconds for better real-time update
    setInterval(() => {
      loadUsers();
    }, 5000);

    // Add manual refresh button for user list
    const userRolesToolbar = document.querySelector('#user-roles .toolbar');
    const refreshBtn = document.createElement('button');
    refreshBtn.textContent = 'Refresh Users';
    refreshBtn.className = 'btn-secondary';
    refreshBtn.style.marginLeft = '10px';
    refreshBtn.addEventListener('click', () => {
      loadUsers();
    });
    userRolesToolbar.appendChild(refreshBtn);

    // For AI config save
    document.querySelector('#ai-config form').addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const config = {
        enabled: formData.get('enable_ai') === 'on'
      };
      fetch('../../backend/routes/api.php?path=/api/superadmin/ai-config', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(config)
        })
        .then(response => response.json())
        .then(data => alert(data.success ? 'AI config saved' : 'Failed'));
    });

    // For settings save
    document.querySelector('#settings form').addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const settings = {
        site_name: formData.get('site_name')
      };
      fetch('../../backend/routes/api.php?path=/api/superadmin/settings', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(settings)
        })
        .then(response => response.json())
        .then(data => alert(data.success ? 'Settings saved' : 'Failed'));
    });
  </script>
</body>

</html>