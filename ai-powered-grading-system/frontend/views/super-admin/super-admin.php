<?php
session_start();
include '../../components/header.php';
?>

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
              <tbody id="recent-activity">
                <!-- Rows will be dynamically inserted here -->
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
            <input type="text" id="user-search" placeholder="Search by name or email..." />
            <input type="text" id="user-id-search" placeholder="Search by User ID..." />
            <select id="role-filter">
              <option>Filter by Role</option>
              <option>Super Admin</option>
              <option>MIS Admin</option>
              <option>Professor</option>
              <option>Student</option>
            </select>
            <select id="status-filter">
              <option>Filter by Status</option>
              <option>Active</option>
              <option>Inactive</option>
            </select>
          </div>

          <div class="user-table-container">
            <table>
              <thead>
                <tr>
                  <th>User ID</th>
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
          <!-- Hidden Edit User Panel -->
          <div id="edit-user-panel" class="hidden-panel">
            <h2>Edit User</h2>
            <form id="edit-user-form">
              <input type="hidden" id="edit-user-id" />

              <label for="edit-user-name">Name:</label>
              <input type="text" id="edit-user-name" placeholder="Enter full name" required />

              <label for="edit-user-email">Email:</label>
              <input type="email" id="edit-user-email" placeholder="Enter email address" required />

              <label for="edit-user-role">Role:</label>
              <select id="edit-user-role" required>
                <option value="1">Super Admin</option>
                <option value="2">MIS Admin</option>
                <option value="3">Professor</option>
                <option value="4">Student</option>
              </select>

              <div class="form-actions">
                <button type="submit" class="btn-primary">Save Changes</button>
                <button type="button" id="close-edit-panel" class="btn-secondary">Cancel</button>
              </div>
            </form>
          </div>
        </section>

        <!-- ================= DATABASE MANAGEMENT ================= -->
        <section id="database" class="tab-section hidden">
          <h2>Database Management</h2>
          <div class="cards-container">
            <div class="card">
              <h4>Database Size</h4>
              <p class="metric" id="db-size">Loading...</p>
              <span id="db-size-update">Updated just now</span>
            </div>
            <div class="card">
              <h4>Database Health</h4>
              <p class="status-healthy" id="db-health">Loading...</p>
              <span id="db-health-message"></span>
              <div class="db-health-progress" id="db-health-progress">
                <div class="progress-fill healthy" id="progress-fill"></div>
                <div class="threshold-marker" style="left: 33.33%;"><span class="threshold-label">1GB</span></div>
                <div class="threshold-marker" style="left: 66.67%;"><span class="threshold-label">2GB</span></div>
                <div class="threshold-marker" style="left: 100%;"><span class="threshold-label">3GB</span></div>
              </div>
            </div>
            <div class="card auto-backup-card">
              <h4>Last Backup</h4>
              <p class="status-healthy" id="last-backup">Loading...</p>
              <div class="auto-backup-controls">
                <div class="backup-controls-row">
                  <label class="auto-backup-toggle">
                    <input type="checkbox" id="auto-backup-toggle" />
                    <span class="toggle-slider"></span>
                    Auto-backup
                  </label>
                  <button id="manual-backup-btn" class="btn-primary">Backup Now</button>
                </div>
                <button id="auto-backup-interval-btn" class="btn-secondary">Auto-backup Interval</button>
              </div>
            </div>
            <div class="card restore-point-card">
              <h4>Restore Point</h4>
              <div class="backup-files-list" id="backup-files-list">
                <p class="loading">Loading backup files...</p>
              </div>
              <div class="restore-controls">
                <button id="restore-btn" class="btn-primary" disabled>Restore</button>
                <button id="refresh-backups-btn" class="btn-secondary">Refresh</button>
              </div>
            </div>
          </div>
        </section>

        <!-- ================= AUDIT LOGS ================= -->
        <section id="audit-logs" class="tab-section hidden">
          <h2>Audit Logs</h2>

          <!-- CSV Management Section -->
          <div id="csv-management-section" class="csv-management-section">
            <div class="csv-controls">
              <h3>CSV Export & Management</h3>
              <div class="csv-buttons">
                <button id="toggle-export-options-btn" class="btn-secondary">Export Options</button>
                <button id="export-csv-btn" class="btn-primary">Export to CSV</button>
                <button id="refresh-csv-files-btn" class="btn-secondary">Refresh CSV Files</button>
                <button id="csv-settings-btn" class="btn-secondary">CSV Settings</button>
              </div>
            </div>

            <!-- CSV Export Options -->
            <div id="csv-export-options" class="csv-options-panel hidden">
              <h4>Export Options</h4>
              <div class="export-filters">
                <div class="filter-group">
                  <label for="export-start-date">Start Date:</label>
                  <input type="date" id="export-start-date" />
                </div>
                <div class="filter-group">
                  <label for="export-end-date">End Date:</label>
                  <input type="date" id="export-end-date" />
                </div>
                <div class="filter-group">
                  <label for="export-log-type">Log Type:</label>
                  <select id="export-log-type">
                    <option value="">All Types</option>
                    <option value="authentication">Authentication</option>
                    <option value="permission_change">Permission Change</option>
                    <option value="sensitive_data_access">Sensitive Data Access</option>
                    <option value="data_modification">Data Modification</option>
                    <option value="system_action">System Action</option>
                    <option value="failed_operation">Failed Operation</option>
                    <option value="account_lifecycle">Account Lifecycle</option>
                  </select>
                </div>
                <div class="filter-group">
                  <label for="export-status">Status:</label>
                  <select id="export-status">
                    <option value="">All Status</option>
                    <option value="Success">Success</option>
                    <option value="Failed">Failed</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- CSV Files Management -->
            <div id="csv-files-management" class="csv-files-panel">
              <h4>CSV Log Files</h4>
              <div class="csv-files-list" id="csv-files-list">
                <p class="loading">Loading CSV files...</p>
              </div>
            </div>

            <!-- CSV Statistics -->
            <div id="csv-statistics" class="csv-stats-panel">
              <h4>CSV Statistics</h4>
              <div class="stats-grid" id="csv-stats-grid">
                <div class="stat-item">
                  <span class="stat-label">Total Files:</span>
                  <span class="stat-value" id="total-files">0</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Total Records:</span>
                  <span class="stat-value" id="total-records">0</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Total Size:</span>
                  <span class="stat-value" id="total-size">0 MB</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Oldest File:</span>
                  <span class="stat-value" id="oldest-file">-</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Newest File:</span>
                  <span class="stat-value" id="newest-file">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Original Audit Logs Section -->
          <div class="audit-logs-section">
            <div class="toolbar">
              <input type="text" id="audit-search" placeholder="Search logs..." />
              <select id="audit-status-filter">
                <option>Filter by Status</option>
                <option>Success</option>
                <option>Failed</option>
              </select>
              <select id="audit-log-type-filter">
                <option>Filter by Log Type</option>
                <option>authentication</option>
                <option>permission_change</option>
                <option>sensitive_data_access</option>
                <option>data_modification</option>
                <option>system_action</option>
                <option>failed_operation</option>
                <option>account_lifecycle</option>
              </select>
              <select id="audit-log-level-filter">
                <option>Filter by Log Level</option>
                <option>INFO</option>
                <option>WARNING</option>
                <option>ERROR</option>
                <option>SECURITY</option>
              </select>
              <!-- Removed audit-sort-filter as per user request -->
            </div>
            <div class="user-table-container">
              <table>
                <thead>
                  <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Log Type</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Failure Reason</th>
                  </tr>
                </thead>
                <tbody id="audit-logs-table">
                  <!-- Data will be loaded dynamically -->
                </tbody>
              </table>
            </div>
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

          <!-- General Settings -->
          <div class="settings-section">
            <h3>General Settings</h3>
            <form class="settings-form" id="general-settings-form">
              <div class="form-row">
                <label>
                  System Name:
                  <input type="text" name="system_name" id="system-name" value="PLMUN Portal" />
                </label>
                <label>
                  Theme Color:
                  <input type="color" name="theme_color" id="theme-color" value="#217589" />
                </label>
              </div>
              <div class="form-row">
                <label>
                  Default Password Reset:
                  <input type="text" name="default_password_reset" id="default-password" value="changeme123" />
                </label>
                <label>
                  Session Timeout (minutes):
                  <input type="number" name="session_timeout" id="session-timeout" min="5" max="480" value="60" />
                </label>
              </div>
              <button type="submit" class="btn-primary">Update General Settings</button>
            </form>
          </div>

          <!-- Grading Scales Configuration -->
          <div class="settings-section">
            <h3>Grading Scales Configuration</h3>
            <div class="grading-scales-container">
              <div class="grading-scale-controls">
                <button id="add-grading-scale-btn" class="btn-secondary">Add New Grading Scale</button>
                <button id="refresh-grading-scales-btn" class="btn-secondary">Refresh Scales</button>
              </div>
              <div class="grading-scales-list" id="grading-scales-list">
                <p class="loading">Loading grading scales...</p>
              </div>
            </div>
          </div>

          <!-- Security Policies Configuration -->
          <div class="settings-section">
            <h3>Security Policies</h3>
            <form class="settings-form" id="security-policies-form">
              <div class="policy-section">
                <h4>Password Requirements</h4>
                <div class="form-row">
                  <label>
                    <input type="checkbox" name="password_min_length_enabled" id="password-min-length-enabled" checked />
                    Minimum Length:
                    <input type="number" name="password_min_length" id="password-min-length" min="6" max="32" value="8" />
                  </label>
                  <label>
                    <input type="checkbox" name="password_uppercase_required" id="password-uppercase-required" />
                    Require Uppercase Letters
                  </label>
                </div>
                <div class="form-row">
                  <label>
                    <input type="checkbox" name="password_lowercase_required" id="password-lowercase-required" />
                    Require Lowercase Letters
                  </label>
                  <label>
                    <input type="checkbox" name="password_numbers_required" id="password-numbers-required" />
                    Require Numbers
                  </label>
                </div>
                <div class="form-row">
                  <label>
                    <input type="checkbox" name="password_special_chars_required" id="password-special-chars-required" />
                    Require Special Characters
                  </label>
                  <label>
                    Password History (prevent reuse of last N passwords):
                    <input type="number" name="password_history_count" id="password-history-count" min="0" max="10" value="3" />
                  </label>
                </div>
              </div>

              <div class="policy-section">
                <h4>Account Security</h4>
                <div class="form-row">
                  <label>
                    Maximum Login Attempts:
                    <input type="number" name="max_login_attempts" id="max-login-attempts" min="3" max="10" value="5" />
                  </label>
                  <label>
                    Account Lockout Duration (minutes):
                    <input type="number" name="lockout_duration" id="lockout-duration" min="5" max="1440" value="30" />
                  </label>
                </div>
                <div class="form-row">
                  <label>
                    Password Expiration (days, 0 = never):
                    <input type="number" name="password_expiration_days" id="password-expiration-days" min="0" max="365" value="90" />
                  </label>
                  <label>
                    Two-Factor Authentication:
                    <select name="two_factor_required" id="two-factor-required">
                      <option value="disabled">Disabled</option>
                      <option value="optional">Optional</option>
                      <option value="required">Required for All</option>
                      <option value="required-admin">Required for Admins Only</option>
                    </select>
                  </label>
                </div>
              </div>

              <button type="submit" class="btn-primary">Update Security Policies</button>
            </form>
          </div>

          <!-- Encryption Methods Verification -->
          <div class="settings-section">
            <h3>Encryption & Security Status</h3>
            <div class="encryption-status-container">
              <div class="encryption-status-grid" id="encryption-status-grid">
                <div class="status-card">
                  <h4>Database Encryption</h4>
                  <div class="status-indicator" id="db-encryption-status">
                    <span class="status-icon">🔒</span>
                    <span class="status-text">Checking...</span>
                  </div>
                  <p class="status-details" id="db-encryption-details">Loading encryption details...</p>
                </div>

                <div class="status-card">
                  <h4>File Storage Encryption</h4>
                  <div class="status-indicator" id="file-encryption-status">
                    <span class="status-icon">📁</span>
                    <span class="status-text">Checking...</span>
                  </div>
                  <p class="status-details" id="file-encryption-details">Loading encryption details...</p>
                </div>

                <div class="status-card">
                  <h4>SSL/TLS Certificate</h4>
                  <div class="status-indicator" id="ssl-status">
                    <span class="status-icon">🔐</span>
                    <span class="status-text">Checking...</span>
                  </div>
                  <p class="status-details" id="ssl-details">Loading SSL certificate details...</p>
                </div>

                <div class="status-card">
                  <h4>API Security</h4>
                  <div class="status-indicator" id="api-security-status">
                    <span class="status-icon">🌐</span>
                    <span class="status-text">Checking...</span>
                  </div>
                  <p class="status-details" id="api-security-details">Loading API security details...</p>
                </div>
              </div>

              <div class="encryption-actions">
                <button id="refresh-encryption-status-btn" class="btn-secondary">Refresh Status</button>
                <button id="view-encryption-logs-btn" class="btn-secondary">View Security Logs</button>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Edit User Modal -->
  <div id="edit-user-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <h3>Edit User</h3>
      <form id="edit-user-form">
        <input type="hidden" id="edit-user-id" />
        <label for="edit-name">Name:</label>
        <input type="text" id="edit-name" required />
        <label for="edit-email">Email:</label>
        <input type="email" id="edit-email" required />
        <label for="edit-role">Role:</label>
        <select id="edit-role" required>
          <option value="1">Super Admin</option>
          <option value="2">MIS Admin</option>
          <option value="3">Professor</option>
          <option value="4">Student</option>
        </select>
        <button type="submit" class="btn-primary">Save</button>
        <button type="button" id="cancel-edit" class="btn-secondary">Cancel</button>
      </form>
    </div>
  </div>



  <!-- Main JavaScript for AI-Powered Grading System -->
  <script src="../../js/main.js"></script>
  <script src="../../js/superadmin.js"></script>

</body>

</html>