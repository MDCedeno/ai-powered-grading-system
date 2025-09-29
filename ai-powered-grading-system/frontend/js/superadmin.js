// Super Admin JavaScript for real-time data display, sorting, search

// Data storage
let usersData = [];
let auditLogsData = [];
let statsData = {};
let recentActivityData = [];

// Current sort state
let userSort = { column: null, ascending: true };
let auditLogSort = { column: null, ascending: true };

// Utility: Case-insensitive partial match
function matchesSearch(text, search) {
  if (!text) return false;
  return text.toLowerCase().includes(search.toLowerCase());
}

// Get role name from role_id
function getRoleName(roleId) {
  const roles = {
    1: "Super Admin",
    2: "MIS Admin",
    3: "Professor",
    4: "Student",
  };
  return roles[roleId] || "Unknown";
}

// Render table rows for users
function renderUsersTable(data) {
  const tbody = document.querySelector("#user-roles table tbody");
  if (!tbody) return;
  tbody.innerHTML = "";
  if (data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7">No users found</td></tr>';
    return;
  }
  data.forEach((user) => {
    const roleName = getRoleName(user.role_id);
    const status = user.active == 1 ? "Active" : "Inactive";
    const actionButtonLabel = user.active == 1 ? "Deactivate" : "Activate";
    const actionButtonClass =
      user.active == 1 ? "danger deactivate-btn" : "success activate-btn";
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${user.id}</td>
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${roleName}</td>
      <td><span class="status-tag ${status.toLowerCase()}">${status}</span></td>
      <td>${user.created_at || "N/A"}</td>
      <td>
        <button class="btn-icon edit-btn" data-id="${user.id}">Edit</button>
        <button class="btn-icon ${actionButtonClass}" data-id="${
      user.id
    }">${actionButtonLabel}</button>
      </td>
    `;
    tbody.appendChild(row);
  });

  // Add event listeners to buttons
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const userId = e.target.dataset.id;
      openEditPanel(userId);
    });
  });
  document.querySelectorAll(".deactivate-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const userId = e.target.dataset.id;
      deactivateUser(userId);
    });
  });
  document.querySelectorAll(".activate-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const userId = e.target.dataset.id;
      activateUser(userId);
    });
  });
}

// Render table rows for audit logs
function renderAuditLogsTable(data) {
  const tbody = document.querySelector("#audit-logs-table");
  if (!tbody) return;
  tbody.innerHTML = "";
  if (data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7">No audit logs found</td></tr>';
    return;
  }
  data.forEach((log) => {
    const statusClass = log.status === 'Success' ? 'success' : 'error';
    const failureReason = log.failure_reason ? log.failure_reason : '-';
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${log.timestamp}</td>
      <td>${log.user}</td>
      <td>${log.log_type}</td>
      <td>${log.action}</td>
      <td>${log.details || '-'}</td>
      <td><span class="status-tag ${statusClass}">${log.status}</span></td>
      <td>${failureReason}</td>
    `;
    tbody.appendChild(row);
  });
}

// Sort data array by column
function sortData(data, column, ascending) {
  return data.slice().sort((a, b) => {
    let valA = a[column] || "";
    let valB = b[column] || "";
    if (typeof valA === "string") valA = valA.toLowerCase();
    if (typeof valB === "string") valB = valB.toLowerCase();
    if (valA < valB) return ascending ? -1 : 1;
    if (valA > valB) return ascending ? 1 : -1;
    return 0;
  });
}

// Filter data
function filterUsers(search, userId, role, status) {
  let filtered = usersData;
  if (search) {
    filtered = filtered.filter(
      (u) =>
        matchesSearch(u.name, search) ||
        matchesSearch(u.email, search) ||
        matchesSearch(getRoleName(u.role_id), search) ||
        matchesSearch(u.active == 1 ? "Active" : "Inactive", search)
    );
  }
  if (userId) {
    filtered = filtered.filter((u) => u.id.toString().includes(userId));
  }
  if (role && role !== "Filter by Role") {
    const roleId = Object.keys({
      1: "Super Admin",
      2: "MIS Admin",
      3: "Professor",
      4: "Student",
    }).find((k) => getRoleName(k) === role);
    filtered = filtered.filter((u) => u.role_id == roleId);
  }
  if (status && status !== "Filter by Status") {
    const isActive = status === "Active" ? 1 : 0;
    filtered = filtered.filter((u) => u.active == isActive);
  }
  return filtered;
}

function filterAuditLogs(search, status, logType, logLevel) {
  let filtered = auditLogsData;
  if (search) {
    filtered = filtered.filter(
      (l) =>
        matchesSearch(l.user, search) ||
        matchesSearch(l.action, search) ||
        matchesSearch(l.details, search) ||
        matchesSearch(l.log_type, search) ||
        matchesSearch(l.status, search) ||
        matchesSearch(l.failure_reason, search)
    );
  }
  if (status && status !== "Filter by Status") {
    filtered = filtered.filter((l) => l.status === status);
  }
  if (logType && logType !== "Filter by Log Type") {
    filtered = filtered.filter((l) => l.log_type === logType);
  }
  if (logLevel && logLevel !== "Filter by Log Level") {
    filtered = filtered.filter((l) => l.log_level === logLevel);
  }
  return filtered;
}

// Load data using XMLHttpRequest
function loadUsers() {
  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "../../../backend/router.php/api/superadmin/users?limit=10",
    true
  );
  xhr.onload = function () {
    if (xhr.status === 200) {
      usersData = JSON.parse(xhr.responseText);
      applyUserFiltersAndSort();
    }
  };
  xhr.send();
}

function loadAuditLogs() {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "../../../backend/router.php/api/superadmin/logs", true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      auditLogsData = JSON.parse(xhr.responseText);
      applyAuditLogFiltersAndSort();
    }
  };
  xhr.send();
}

// Apply filters and sorting
function applyUserFiltersAndSort() {
  const search = document.getElementById("user-search")?.value.trim() || "";
  const userId = document.getElementById("user-id-search")?.value.trim() || "";
  const role = document.getElementById("role-filter")?.value || "";
  const status = document.getElementById("status-filter")?.value || "";
  let filtered = filterUsers(search, userId, role, status);
  if (userSort.column) {
    filtered = sortData(filtered, userSort.column, userSort.ascending);
  }
  renderUsersTable(filtered);
}

function applyAuditLogFiltersAndSort() {
  const search = document.getElementById("audit-search")?.value.trim() || "";
  const status = document.getElementById("audit-status-filter")?.value || "";
  const logType = document.getElementById("audit-log-type-filter")?.value || "";
  const logLevel = document.getElementById("audit-log-level-filter")?.value || "";
  let filtered = filterAuditLogs(search, status, logType, logLevel);
  if (auditLogSort.column) {
    filtered = sortData(filtered, auditLogSort.column, auditLogSort.ascending);
  }
  renderAuditLogsTable(filtered);
}

// Add sorting buttons
function addSortingButtons() {
  // Users
  const userHeaders = document.querySelectorAll("#user-roles table thead th");
  userHeaders.forEach((th, index) => {
    if (index === 4 || index === 6) return; // skip status and actions
    const columnMap = [
      "id",
      "name",
      "email",
      "role_id",
      "status",
      "created_at",
    ];
    const column = columnMap[index];

    const btnAsc = document.createElement("button");
    btnAsc.textContent = "▲";
    btnAsc.className = "sort-btn";
    btnAsc.style.marginLeft = "5px";
    btnAsc.addEventListener("click", () => {
      userSort.column = column;
      userSort.ascending = true;
      applyUserFiltersAndSort();
    });
    th.appendChild(btnAsc);

    const btnDesc = document.createElement("button");
    btnDesc.textContent = "▼";
    btnDesc.className = "sort-btn";
    btnDesc.style.marginLeft = "5px";
    btnDesc.addEventListener("click", () => {
      userSort.column = column;
      userSort.ascending = false;
      applyUserFiltersAndSort();
    });
    th.appendChild(btnDesc);
  });
  // Audit Logs
  const auditHeaders = document.querySelectorAll("#audit-logs table thead th");
  auditHeaders.forEach((th, index) => {
    if (index === 3 || index === 4 || index === 5 || index === 6) return; // skip action, details, status, failure reason
    const columnMap = ["timestamp", "user", "log_type", "action", "status", "failure_reason"];
    const column = columnMap[index];

    const btnAsc = document.createElement("button");
    btnAsc.textContent = "▲";
    btnAsc.className = "sort-btn";
    btnAsc.style.marginLeft = "5px";
    btnAsc.addEventListener("click", () => {
      auditLogSort.column = column;
      auditLogSort.ascending = true;
      applyAuditLogFiltersAndSort();
    });
    th.appendChild(btnAsc);

    const btnDesc = document.createElement("button");
    btnDesc.textContent = "▼";
    btnDesc.className = "sort-btn";
    btnDesc.style.marginLeft = "5px";
    btnDesc.addEventListener("click", () => {
      auditLogSort.column = column;
      auditLogSort.ascending = false;
      applyAuditLogFiltersAndSort();
    });
    th.appendChild(btnDesc);
  });
}

// Event listeners
function setupEventListeners() {
  const userSearchInput = document.getElementById("user-search");
  const userIdSearchInput = document.getElementById("user-id-search");
  const roleFilter = document.getElementById("role-filter");
  const statusFilter = document.getElementById("status-filter");
  const auditSearchInput = document.getElementById("audit-search");
  const auditStatusFilter = document.getElementById("audit-status-filter");
  const auditLogTypeFilter = document.getElementById("audit-log-type-filter");
  const auditLogLevelFilter = document.getElementById("audit-log-level-filter");
  const autoBackupToggle = document.getElementById("auto-backup-toggle");

  if (userSearchInput)
    userSearchInput.addEventListener("input", applyUserFiltersAndSort);
  if (userIdSearchInput)
    userIdSearchInput.addEventListener("input", applyUserFiltersAndSort);
  if (roleFilter)
    roleFilter.addEventListener("change", applyUserFiltersAndSort);
  if (statusFilter)
    statusFilter.addEventListener("change", applyUserFiltersAndSort);
  if (auditSearchInput)
    auditSearchInput.addEventListener("input", applyAuditLogFiltersAndSort);
  if (auditStatusFilter)
    auditStatusFilter.addEventListener("change", applyAuditLogFiltersAndSort);
  if (auditLogTypeFilter)
    auditLogTypeFilter.addEventListener("change", applyAuditLogFiltersAndSort);
  if (auditLogLevelFilter)
    auditLogLevelFilter.addEventListener("change", applyAuditLogFiltersAndSort);

  if (autoBackupToggle) {
    autoBackupToggle.addEventListener("change", async (event) => {
      const enabled = event.target.checked;
      try {
        const response = await fetch(
          "../../../backend/router.php/api/superadmin/auto-backup",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ enabled }),
          }
        );
        if (!response.ok) {
          throw new Error("Failed to update auto-backup setting");
        }
        const result = await response.json();
        if (!result.success) {
          throw new Error("Failed to update auto-backup setting");
        }
        alert("Auto-backup " + (enabled ? "enabled" : "disabled"));
        loadStats(); // Refresh stats to update UI
      } catch (error) {
        alert("Failed to update auto-backup setting");
        // Revert checkbox state on failure
        event.target.checked = !enabled;
      }
    });
  }

  // Auto-backup interval button
  const autoBackupIntervalBtn = document.getElementById("auto-backup-interval-btn");
  if (autoBackupIntervalBtn) {
    autoBackupIntervalBtn.addEventListener("click", openAutoBackupIntervalModal);
  }

  // Close auto-backup interval modal
  const closeAutoBackupIntervalBtn = document.getElementById("close-auto-backup-interval");
  if (closeAutoBackupIntervalBtn) {
    closeAutoBackupIntervalBtn.addEventListener("click", closeAutoBackupIntervalModal);
  }

  // Auto-backup interval form submission
  const autoBackupIntervalForm = document.getElementById("auto-backup-interval-form");
  if (autoBackupIntervalForm) {
    autoBackupIntervalForm.addEventListener("submit", saveAutoBackupInterval);
  }

  // Manual backup button
  const manualBackupBtn = document.getElementById("manual-backup-btn");
  if (manualBackupBtn) {
    manualBackupBtn.addEventListener("click", async () => {
      const confirmed = confirm(
        "Are you sure you want to create a backup of the database? This may take a few moments."
      );
      if (!confirmed) return;

      manualBackupBtn.disabled = true;
      manualBackupBtn.textContent = "Backing up...";

      try {
        const response = await fetch(
          "../../../backend/router.php/api/superadmin/backup",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
          }
        );

        if (!response.ok) {
          throw new Error("Failed to create backup");
        }

        const result = await response.json();
        if (result.success) {
          alert("Database backup created successfully!");
          // Refresh stats to update last backup time
          loadStats();
          // Refresh backup files list
          loadBackupFiles();
        } else {
          alert("Backup failed: " + (result.message || "Unknown error"));
        }
      } catch (error) {
        alert("Backup failed due to network or server error.");
      } finally {
        manualBackupBtn.disabled = false;
        manualBackupBtn.textContent = "Backup Now";
      }
    });
  }

  // Remove refresh button event listener if it exists
  const refreshBtn = document.getElementById("refresh-users");
  if (refreshBtn) {
    refreshBtn.remove();
  }
}

function renderDashboardCards(stats) {
  // Server Status
  const serverStatusEl = document.querySelector(
    ".cards-container .card:nth-child(1) p"
  );
  if (serverStatusEl) {
    serverStatusEl.textContent = stats.server_status || "Unknown";
    serverStatusEl.className =
      stats.server_status === "Online" ? "status-online" : "status-offline";
  }
  const uptimeEl = document.querySelector(
    ".cards-container .card:nth-child(1) span"
  );
  if (uptimeEl) uptimeEl.textContent = `Uptime: ${stats.uptime || "N/A"}`;

  // Active Users
  const activeUsersEl = document.querySelector(
    ".cards-container .card:nth-child(2) p"
  );
  if (activeUsersEl)
    activeUsersEl.textContent = stats.users?.toLocaleString() || "0";

  // Error Logs
  const errorLogsEl = document.querySelector(
    ".cards-container .card:nth-child(3) p"
  );
  if (errorLogsEl) {
    errorLogsEl.textContent = stats.error_logs_24h?.toString() || "0";
    // Show critical error message if errors > 0
    errorLogsEl.className =
      stats.error_logs_24h > 0 ? "metric error" : "metric";
  }
  const errorLogsMessageEl = document.querySelector(
    ".cards-container .card:nth-child(3) span"
  );
  if (errorLogsMessageEl) {
    if (stats.error_logs_24h > 0) {
      errorLogsMessageEl.textContent = "Critical errors need attention";
      errorLogsMessageEl.style.color = "#ef4444"; // red
    } else {
      errorLogsMessageEl.textContent = "No critical errors";
      errorLogsMessageEl.style.color = "#10b981"; // green
    }
  }

  // Database Health
  const dbHealthEl = document.querySelector(
    ".cards-container .card:nth-child(4) p"
  );
  if (dbHealthEl) {
    dbHealthEl.textContent = stats.db_health || "Unknown";
    // Apply color coding based on health status
    dbHealthEl.className =
      "status-" + (stats.db_health_color || "healthy").toLowerCase();
  }
  const lastBackupEl = document.querySelector(
    ".cards-container .card:nth-child(4) span"
  );
  if (lastBackupEl)
    lastBackupEl.textContent = `Last Backup: ${stats.last_backup || "Never"}`;

  // Auto-backup toggle
  const autoBackupToggle = document.getElementById("auto-backup-toggle");
  if (autoBackupToggle) {
    autoBackupToggle.checked = stats.auto_backup_enabled || false;
  }

  // Database Size and Health in Database Management section
  const dbSizeEl = document.getElementById("db-size");
  if (dbSizeEl) dbSizeEl.textContent = stats.db_size || "Unknown";

  const dbHealthMgmtEl = document.getElementById("db-health");
  if (dbHealthMgmtEl) {
    dbHealthMgmtEl.textContent = stats.db_health || "Unknown";
    // Apply color coding based on health status
    dbHealthMgmtEl.className =
      "status-" + (stats.db_health_color || "healthy").toLowerCase();
  }

  const dbHealthMessageEl = document.getElementById("db-health-message");
  if (dbHealthMessageEl)
    dbHealthMessageEl.textContent = stats.db_health_message || "";

  const lastBackupMgmtEl = document.getElementById("last-backup");
  if (lastBackupMgmtEl)
    lastBackupMgmtEl.textContent = stats.last_backup || "Never";

  // Update Database Health Progress Bar
  const progressFill = document.getElementById("progress-fill");
  if (progressFill && stats.db_size) {
    const colorClass = stats.db_health_color
      ? stats.db_health_color.toLowerCase()
      : "healthy";
    progressFill.className = "progress-fill " + colorClass;
    // Calculate width based on actual database size (max 3GB for critical)
    const sizeMatch = stats.db_size.match(/(\d+(\.\d+)?)/);
    const sizeGB = sizeMatch ? parseFloat(sizeMatch[1]) : 0;
    const widthPercent = Math.min((sizeGB / 3) * 100, 100); // Cap at 100%
    progressFill.style.width = widthPercent + "%";
  }
}

function renderRecentActivity(logs) {
  const tbody = document.getElementById("recent-activity");
  if (!tbody) return;
  tbody.innerHTML = "";

  if (logs.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4">No recent activity found.</td></tr>';
    return;
  }

  logs.forEach((log) => {
    const statusClass =
      log.status.toLowerCase() === "success" ? "success" : "error";
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${log.timestamp}</td>
      <td>${log.user}</td>
      <td>${log.action}</td>
      <td><span class="status-tag ${statusClass}">${log.status}</span></td>
    `;
    tbody.appendChild(row);
  });
}

function loadStats() {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "../../../backend/router.php/api/superadmin/stats", true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      statsData = JSON.parse(xhr.responseText);
      renderDashboardCards(statsData);
      loadAutoBackupInterval(); // Load interval after stats
    }
  };
  xhr.send();
}

function loadAutoBackupInterval() {
  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "../../../backend/router.php/api/superadmin/auto-backup-interval",
    true
  );
  xhr.onload = function () {
    if (xhr.status === 200) {
      const data = JSON.parse(xhr.responseText);
      const intervalInput = document.getElementById("auto-backup-interval");
      const modalIntervalInput = document.getElementById("modal-auto-backup-interval");
      if (intervalInput) {
        intervalInput.value = data.interval;
      }
      if (modalIntervalInput) {
        modalIntervalInput.value = data.interval;
      }
    }
  };
  xhr.send();
}

// Open auto-backup interval modal
function openAutoBackupIntervalModal() {
  // Create modal if it doesn't exist
  let modal = document.getElementById('auto-backup-interval-modal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'auto-backup-interval-modal';
    modal.className = 'modal';
    modal.innerHTML = `
      <div class="modal-content">
        <h3>Auto-backup Interval Settings</h3>
        <form id="auto-backup-interval-form">
          <div class="form-group">
            <label for="modal-auto-backup-interval">Interval (hours):</label>
            <input type="number" id="modal-auto-backup-interval" min="1" max="168" value="24" />
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Save Interval</button>
            <button type="button" id="close-auto-backup-interval" class="btn-secondary">Cancel</button>
          </div>
        </form>
      </div>
    `;
    document.body.appendChild(modal);

    // Load current interval into modal
    loadAutoBackupInterval();

    // Event listeners
    modal.querySelector('#close-auto-backup-interval').addEventListener('click', () => {
      modal.style.display = 'none';
    });

    modal.querySelector('#auto-backup-interval-form').addEventListener('submit', (e) => {
      e.preventDefault();
      saveAutoBackupInterval(e);
    });
  }

  modal.style.display = 'block';
}

// Close auto-backup interval modal
function closeAutoBackupIntervalModal() {
  const modal = document.getElementById('auto-backup-interval-modal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// Save auto-backup interval
function saveAutoBackupInterval(e) {
  e.preventDefault();
  const interval = document.getElementById('modal-auto-backup-interval').value;

  fetch('../../../backend/router.php/api/superadmin/auto-backup-interval', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ interval: parseInt(interval) })
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Auto-backup interval updated successfully!');
        document.getElementById('auto-backup-interval-modal').style.display = 'none';
        loadStats(); // Refresh stats to update UI
      } else {
        alert('Failed to update auto-backup interval: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Failed to update auto-backup interval due to network or server error.');
    });
}

function loadRecentActivity() {
  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "../../../backend/router.php/api/superadmin/logs?limit=5",
    true
  );
  xhr.onload = function () {
    if (xhr.status === 200) {
      recentActivityData = JSON.parse(xhr.responseText);
      renderRecentActivity(recentActivityData);
    }
  };
  xhr.send();
}

// Deactivate user
function deactivateUser(userId) {
  if (!confirm("Are you sure you want to deactivate this user?")) return;

  const xhr = new XMLHttpRequest();
  xhr.open(
    "POST",
    "../../../backend/router.php/api/superadmin/users/deactivate",
    true
  );
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onload = function () {
    if (xhr.status === 200) {
      loadUsers();
    } else {
      alert("Failed to deactivate user");
    }
  };
  xhr.send(JSON.stringify({ user_id: userId }));
}

// Activate user
function activateUser(userId) {
  if (!confirm("Are you sure you want to activate this user?")) return;

  const xhr = new XMLHttpRequest();
  xhr.open(
    "POST",
    "../../../backend/router.php/api/superadmin/users/activate",
    true
  );
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onload = function () {
    if (xhr.status === 200) {
      loadUsers();
    } else {
      alert("Failed to activate user");
    }
  };
  xhr.send(JSON.stringify({ user_id: userId }));
}

// Open edit panel
function openEditPanel(userId) {
  const user = usersData.find((u) => u.id == userId);
  if (!user) return;

  document.getElementById("edit-user-id").value = user.id;
  document.getElementById("edit-user-name").value = user.name;
  document.getElementById("edit-user-email").value = user.email;
  document.getElementById("edit-user-role").value = user.role_id;

  const panel = document.getElementById("edit-user-panel");
  panel.classList.add("show");
}

document.addEventListener("click", (e) => {
  if (e.target.id === "close-edit-panel") {
    const panel = document.getElementById("edit-user-panel");
    if (
      confirm(
        "Are you sure you want to cancel editing? Changes will not be saved."
      )
    ) {
      panel.classList.remove("show");
    }
  }
});

document.getElementById("edit-user-form").addEventListener("submit", (e) => {
  e.preventDefault();
  if (!confirm("Are you sure you want to save changes?")) {
    return;
  }
  const userId = document.getElementById("edit-user-id").value;
  const data = {
    name: document.getElementById("edit-user-name").value,
    email: document.getElementById("edit-user-email").value,
    role_id: document.getElementById("edit-user-role").value,
  };

  const xhr = new XMLHttpRequest();
  xhr.open(
    "PUT",
    "../../../backend/router.php/api/superadmin/users/" + userId,
    true
  );
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onload = function () {
    if (xhr.status === 200) {
      const panel = document.getElementById("edit-user-panel");
      panel.classList.remove("show");
      loadUsers();
    } else {
      alert("Failed to save changes");
    }
  };
  xhr.send(JSON.stringify(data));
});

function initSuperAdmin() {
  loadUsers();
  loadAuditLogs();
  loadStats();
  loadRecentActivity();
  loadBackupFiles(); // Load backup files for restore functionality
  addSortingButtons();
  setupEventListeners();
  //setupScrollAndTabHighlight();
  // Auto refresh every 5 seconds
  setInterval(() => {
    loadUsers();
    loadAuditLogs();
    loadStats();
    loadRecentActivity();
  }, 5000);
}

document.addEventListener("DOMContentLoaded", initSuperAdmin);

// Restore Point functionality

let selectedBackupFile = null;

function loadBackupFiles() {
  const backupListEl = document.getElementById("backup-files-list");
  const restoreBtn = document.getElementById("restore-btn");
  if (!backupListEl || !restoreBtn) return;

  backupListEl.innerHTML = '<p class="loading">Loading backup files...</p>';
  restoreBtn.disabled = true;
  selectedBackupFile = null;

  fetch("../../../backend/router.php/api/superadmin/backup-files")
    .then((response) => response.json())
    .then((files) => {
      if (!files || files.length === 0) {
        backupListEl.innerHTML = "<p>No backup files found.</p>";
        return;
      }
      backupListEl.innerHTML = "";
      files.forEach((fileObj) => {
        const fileItem = document.createElement("div");
        fileItem.className = "backup-file-item";

        // Display filename and snapshot date
        const fileInfo = document.createElement("div");
        fileInfo.className = "file-info";
        fileInfo.innerHTML = `
          <div class="file-name">${fileObj.filename}</div>
          <div class="file-date">Snapshot: ${fileObj.snapshot_date}</div>
        `;

        const openBtn = document.createElement("button");
        openBtn.textContent = "Open";
        openBtn.className = "btn-secondary btn-small";
        openBtn.addEventListener("click", () => {
          window.open(`../../../../backups/${fileObj.filename}`, "_blank");
        });

        const copyBtn = document.createElement("button");
        copyBtn.textContent = "Copy";
        copyBtn.className = "btn-secondary btn-small";
        copyBtn.addEventListener("click", () => {
          navigator.clipboard.writeText(fileObj.filename).then(() => {
            alert("Filename copied to clipboard");
          });
        });

        // Wrap buttons in btn-group for layout
        const btnGroup = document.createElement("div");
        btnGroup.className = "btn-group";
        btnGroup.appendChild(openBtn);
        btnGroup.appendChild(copyBtn);
        fileItem.appendChild(fileInfo);
        fileItem.appendChild(btnGroup);

        fileItem.addEventListener("click", () => {
          // Deselect all
          document
            .querySelectorAll(".backup-file-item")
            .forEach((el) => el.classList.remove("selected"));
          fileItem.classList.add("selected");
          selectedBackupFile = fileObj.filename;
          restoreBtn.disabled = false;
        });

        backupListEl.appendChild(fileItem);
      });
    })
    .catch(() => {
      backupListEl.innerHTML = "<p>Error loading backup files.</p>";
    });
}

function restoreBackup() {
  if (!selectedBackupFile) {
    alert("Please select a backup file to restore.");
    return;
  }
  if (
    !confirm(
      `Are you sure you want to restore the database from backup file "${selectedBackupFile}"? This action cannot be undone.`
    )
  ) {
    return;
  }

  fetch("../../../backend/router.php/api/superadmin/restore", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ filename: selectedBackupFile }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Database restored successfully.");
      } else {
        alert("Restore failed: " + (result.message || "Unknown error"));
      }
    })
    .catch(() => {
      alert("Restore failed due to network or server error.");
    });
}

document
  .getElementById("restore-btn")
  ?.addEventListener("click", restoreBackup);
document
  .getElementById("refresh-backups-btn")
  ?.addEventListener("click", loadBackupFiles);

// Load backup files on page load
document.addEventListener("DOMContentLoaded", loadBackupFiles);

// Enable draggable horizontal scroll for audit logs table
function enableDragScroll() {
  const container = document.querySelector("#audit-logs .user-table-container");
  if (!container) return;

  let isDown = false;
  let startX;
  let scrollLeft;

  container.addEventListener("mousedown", (e) => {
    isDown = true;
    container.classList.add("active");
    startX = e.pageX - container.offsetLeft;
    scrollLeft = container.scrollLeft;
  });

  container.addEventListener("mouseleave", () => {
    isDown = false;
    container.classList.remove("active");
  });

  container.addEventListener("mouseup", () => {
    isDown = false;
    container.classList.remove("active");
  });

  container.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - container.offsetLeft;
    const walk = (x - startX) * 2; // Scroll speed multiplier
    container.scrollLeft = scrollLeft - walk;
  });
}

document.addEventListener("DOMContentLoaded", enableDragScroll);

// CSV Management functionality
let csvFilesData = [];
let csvStatsData = {};

// Toggle CSV export options panel
function toggleCsvExportOptions() {
  const optionsPanel = document.getElementById('csv-export-options');
  if (optionsPanel) {
    optionsPanel.classList.toggle('hidden');
  }
}

// CSV Export functionality
function exportLogsToCsv() {
  const startDate = document.getElementById('export-start-date')?.value || '';
  const endDate = document.getElementById('export-end-date')?.value || '';
  const logType = document.getElementById('export-log-type')?.value || '';
  const status = document.getElementById('export-status')?.value || '';

  const params = new URLSearchParams();
  if (startDate) params.append('start_date', startDate);
  if (endDate) params.append('end_date', endDate);
  if (logType) params.append('log_type', logType);
  if (status) params.append('status', status);

  const url = `../../../backend/router.php/api/superadmin/logs/export-csv?${params.toString()}`;

  fetch(url)
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('CSV export initiated successfully! File: ' + result.filename);
        loadCsvFiles(); // Refresh CSV files list
        loadCsvStats(); // Refresh statistics
        // Hide export options panel after successful export
        const optionsPanel = document.getElementById('csv-export-options');
        if (optionsPanel) {
          optionsPanel.classList.add('hidden');
        }
      } else {
        alert('Export failed: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Export failed due to network or server error.');
    });
}

// Load CSV files list
function loadCsvFiles() {
  const csvFilesList = document.getElementById('csv-files-list');
  if (!csvFilesList) return;

  csvFilesList.innerHTML = '<p class="loading">Loading CSV files...</p>';

  fetch('../../../backend/router.php/api/superadmin/logs/csv-files')
    .then(response => response.json())
    .then(files => {
      csvFilesData = files;
      if (!files || files.length === 0) {
        csvFilesList.innerHTML = '<p>No CSV files found.</p>';
        return;
      }

      csvFilesList.innerHTML = '';
      files.forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.className = 'csv-file-item';

        const fileInfo = document.createElement('div');
        fileInfo.className = 'file-info';
        fileInfo.innerHTML = `
          <div class="file-name">${file.filename}</div>
          <div class="file-details">
            <span>Size: ${file.size}</span>
            <span>Created: ${file.created_at}</span>
            <span>Records: ${file.record_count}</span>
          </div>
        `;

        const fileActions = document.createElement('div');
        fileActions.className = 'file-actions';

        const downloadBtn = document.createElement('button');
        downloadBtn.textContent = 'Download';
        downloadBtn.className = 'btn-secondary btn-small';
        downloadBtn.addEventListener('click', () => downloadCsvFile(file.filename));

        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Delete';
        deleteBtn.className = 'btn-danger btn-small';
        deleteBtn.addEventListener('click', () => deleteCsvFile(file.filename));

        fileActions.appendChild(downloadBtn);
        fileActions.appendChild(deleteBtn);

        fileItem.appendChild(fileInfo);
        fileItem.appendChild(fileActions);
        csvFilesList.appendChild(fileItem);
      });
    })
    .catch(error => {
      csvFilesList.innerHTML = '<p>Error loading CSV files.</p>';
    });
}

// Download CSV file
function downloadCsvFile(filename) {
  const link = document.createElement('a');
  link.href = `../../../backend/router.php/api/superadmin/logs/csv-download?filename=${encodeURIComponent(filename)}`;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Delete CSV file
function deleteCsvFile(filename) {
  if (!confirm(`Are you sure you want to delete "${filename}"? This action cannot be undone.`)) {
    return;
  }

  fetch(`../../../backend/router.php/api/superadmin/logs/csv-delete?filename=${encodeURIComponent(filename)}`, {
    method: 'DELETE'
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('CSV file deleted successfully!');
        loadCsvFiles(); // Refresh the list
        loadCsvStats(); // Refresh statistics
      } else {
        alert('Delete failed: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Delete failed due to network or server error.');
    });
}

// Load CSV statistics
function loadCsvStats() {
  const statsGrid = document.getElementById('csv-stats-grid');
  if (!statsGrid) return;

  fetch('../../../backend/router.php/api/superadmin/logs/csv-stats')
    .then(response => response.json())
    .then(stats => {
      csvStatsData = stats;

      document.getElementById('total-files').textContent = stats.total_files || 0;
      document.getElementById('total-records').textContent = stats.total_records || 0;
      document.getElementById('total-size').textContent = stats.total_size || '0 MB';
      document.getElementById('oldest-file').textContent = stats.oldest_file || '-';
      document.getElementById('newest-file').textContent = stats.newest_file || '-';
    })
    .catch(error => {
      console.error('Error loading CSV stats:', error);
    });
}

// Load CSV configuration
function loadCsvConfig() {
  fetch('../../../backend/router.php/api/superadmin/logs/csv-config')
    .then(response => response.json())
    .then(config => {
      // Update any config display if needed
      console.log('CSV Config:', config);
    })
    .catch(error => {
      console.error('Error loading CSV config:', error);
    });
}

// CSV Settings functionality
function openCsvSettings() {
  // Create settings modal if it doesn't exist
  let settingsModal = document.getElementById('csv-settings-modal');
  if (!settingsModal) {
    settingsModal = document.createElement('div');
    settingsModal.id = 'csv-settings-modal';
    settingsModal.className = 'modal';
    settingsModal.innerHTML = `
      <div class="modal-content">
        <h3>CSV Logging Settings</h3>
        <form id="csv-settings-form">
          <div class="form-group">
            <label>
              <input type="checkbox" id="csv-logging-enabled" />
              Enable CSV Logging
            </label>
          </div>
          <div class="form-group">
            <label for="csv-retention-days">Retention Period (days):</label>
            <input type="number" id="csv-retention-days" min="1" max="365" />
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Save Settings</button>
            <button type="button" id="close-csv-settings" class="btn-secondary">Cancel</button>
          </div>
        </form>
      </div>
    `;
    document.body.appendChild(settingsModal);

    // Load current settings
    loadCsvSettings();

    // Event listeners
    settingsModal.querySelector('#close-csv-settings').addEventListener('click', () => {
      settingsModal.style.display = 'none';
    });

    settingsModal.querySelector('#csv-settings-form').addEventListener('submit', (e) => {
      e.preventDefault();
      saveCsvSettings();
    });
  }

  settingsModal.style.display = 'block';
}

// Load CSV settings
function loadCsvSettings() {
  fetch('../../../backend/router.php/api/superadmin/logs/csv-settings')
    .then(response => response.json())
    .then(settings => {
      document.getElementById('csv-logging-enabled').checked = settings.csv_logging_enabled || false;
      document.getElementById('csv-retention-days').value = settings.csv_retention_days || 30;
    })
    .catch(error => {
      console.error('Error loading CSV settings:', error);
    });
}

// Save CSV settings
function saveCsvSettings() {
  const enabled = document.getElementById('csv-logging-enabled').checked;
  const retentionDays = document.getElementById('csv-retention-days').value;

  fetch('../../../backend/router.php/api/superadmin/logs/csv-settings', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      enabled: enabled,
      retention_days: parseInt(retentionDays)
    })
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('CSV settings saved successfully!');
        document.getElementById('csv-settings-modal').style.display = 'none';
        loadCsvStats(); // Refresh stats
      } else {
        alert('Failed to save settings: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Failed to save settings due to network or server error.');
    });
}

// Cleanup old CSV logs
function cleanupOldCsvLogs() {
  const retentionDays = prompt('Enter retention period in days (default: 30):', '30');
  if (retentionDays === null) return;

  const days = parseInt(retentionDays) || 30;

  if (!confirm(`Are you sure you want to delete CSV files older than ${days} days?`)) {
    return;
  }

  fetch('../../../backend/router.php/api/superadmin/logs/csv-cleanup', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ retention_days: days })
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert(`Cleanup completed! ${result.deleted_files_count || 0} files deleted.`);
        loadCsvFiles(); // Refresh the list
        loadCsvStats(); // Refresh statistics
      } else {
        alert('Cleanup failed: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Cleanup failed due to network or server error.');
    });
}

// Event listeners for CSV functionality
function setupCsvEventListeners() {
  // Toggle CSV export options panel
  const toggleExportOptionsBtn = document.getElementById('toggle-export-options-btn');
  if (toggleExportOptionsBtn) {
    toggleExportOptionsBtn.addEventListener('click', toggleCsvExportOptions);
  }

  // Export CSV button
  const exportCsvBtn = document.getElementById('export-csv-btn');
  if (exportCsvBtn) {
    exportCsvBtn.addEventListener('click', exportLogsToCsv);
  }

  // Refresh CSV files button
  const refreshCsvFilesBtn = document.getElementById('refresh-csv-files-btn');
  if (refreshCsvFilesBtn) {
    refreshCsvFilesBtn.addEventListener('click', loadCsvFiles);
  }

  // CSV Settings button
  const csvSettingsBtn = document.getElementById('csv-settings-btn');
  if (csvSettingsBtn) {
    csvSettingsBtn.addEventListener('click', openCsvSettings);
  }

  // Cleanup CSV logs button
  const cleanupCsvBtn = document.getElementById('cleanup-csv-btn');
  if (cleanupCsvBtn) {
    cleanupCsvBtn.addEventListener('click', cleanupOldCsvLogs);
  }

  // Close modal when clicking outside
  document.addEventListener('click', (e) => {
    const modal = document.getElementById('csv-settings-modal');
    if (modal && e.target === modal) {
      modal.style.display = 'none';
    }
  });
}

// Initialize CSV functionality
function initCsvManagement() {
  setupCsvEventListeners();
  loadCsvFiles();
  loadCsvStats();
  loadCsvConfig();
}

function loadSystemSettings() {
  fetch('../../../backend/router.php/api/superadmin/settings')
    .then(response => response.json())
    .then(settings => {
      // Populate general settings form
      const generalForm = document.getElementById('general-settings-form');
      if (generalForm) {
        const systemNameInput = generalForm.querySelector('input[name="system_name"]');
        const themeColorInput = generalForm.querySelector('input[name="theme_color"]');
        const defaultPasswordResetInput = generalForm.querySelector('input[name="default_password_reset"]');
        const sessionTimeoutInput = generalForm.querySelector('input[name="session_timeout"]');

        if (systemNameInput) systemNameInput.value = settings.system_name || '';
        if (themeColorInput) themeColorInput.value = settings.theme_color || '#007bff';
        if (defaultPasswordResetInput) defaultPasswordResetInput.value = settings.default_password_reset || '';
        if (sessionTimeoutInput) sessionTimeoutInput.value = settings.session_timeout || 30;
      }

      // Populate security policies form
      const securityForm = document.getElementById('security-policies-form');
      if (securityForm) {
        const passwordMinLengthEnabled = securityForm.querySelector('input[name="password_min_length_enabled"]');
        const passwordMinLengthInput = securityForm.querySelector('input[name="password_min_length"]');
        const passwordUppercaseRequired = securityForm.querySelector('input[name="password_uppercase_required"]');
        const passwordLowercaseRequired = securityForm.querySelector('input[name="password_lowercase_required"]');
        const passwordNumbersRequired = securityForm.querySelector('input[name="password_numbers_required"]');
        const passwordSpecialCharsRequired = securityForm.querySelector('input[name="password_special_chars_required"]');
        const passwordHistoryCountInput = securityForm.querySelector('input[name="password_history_count"]');
        const maxLoginAttemptsInput = securityForm.querySelector('input[name="max_login_attempts"]');
        const lockoutDurationInput = securityForm.querySelector('input[name="lockout_duration"]');
        const passwordExpirationDaysInput = securityForm.querySelector('input[name="password_expiration_days"]');
        const twoFactorRequired = securityForm.querySelector('input[name="two_factor_required"]');

        if (passwordMinLengthEnabled) passwordMinLengthEnabled.checked = settings.password_min_length_enabled === '1' || settings.password_min_length_enabled === true;
        if (passwordMinLengthInput) passwordMinLengthInput.value = settings.password_min_length || 8;
        if (passwordUppercaseRequired) passwordUppercaseRequired.checked = settings.password_uppercase_required === '1' || settings.password_uppercase_required === true;
        if (passwordLowercaseRequired) passwordLowercaseRequired.checked = settings.password_lowercase_required === '1' || settings.password_lowercase_required === true;
        if (passwordNumbersRequired) passwordNumbersRequired.checked = settings.password_numbers_required === '1' || settings.password_numbers_required === true;
        if (passwordSpecialCharsRequired) passwordSpecialCharsRequired.checked = settings.password_special_chars_required === '1' || settings.password_special_chars_required === true;
        if (passwordHistoryCountInput) passwordHistoryCountInput.value = settings.password_history_count || 5;
        if (maxLoginAttemptsInput) maxLoginAttemptsInput.value = settings.max_login_attempts || 5;
        if (lockoutDurationInput) lockoutDurationInput.value = settings.lockout_duration || 15;
        if (passwordExpirationDaysInput) passwordExpirationDaysInput.value = settings.password_expiration_days || 90;
        if (twoFactorRequired) twoFactorRequired.checked = settings.two_factor_required === '1' || settings.two_factor_required === true;
      }
    })
    .catch(error => {
      console.error('Error loading system settings:', error);
      alert('Failed to load system settings.');
    });
}

function saveGeneralSettings() {
  const generalForm = document.getElementById('general-settings-form');
  if (!generalForm) return;

  const formData = new FormData(generalForm);
  const settings = {};
  for (let [key, value] of formData.entries()) {
    settings[key] = value;
  }

  fetch('../../../backend/router.php/api/superadmin/settings', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(settings)
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('General settings saved successfully!');
        loadSystemSettings(); // Reload to confirm
      } else {
        alert('Failed to save general settings: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error saving general settings:', error);
      alert('Failed to save general settings.');
    });
}

function saveSecurityPolicies() {
  const securityForm = document.getElementById('security-policies-form');
  if (!securityForm) return;

  const formData = new FormData(securityForm);
  const settings = {};
  for (let [key, value] of formData.entries()) {
    if (key.endsWith('_enabled') || key.endsWith('_required')) {
      settings[key] = formData.get(key) === 'on' ? '1' : '0';
    } else {
      settings[key] = value;
    }
  }

  // Only include password_min_length if enabled
  if (!settings.password_min_length_enabled || settings.password_min_length_enabled === '0') {
    delete settings.password_min_length;
  }

  fetch('../../../backend/router.php/api/superadmin/settings', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(settings)
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Security policies saved successfully!');
        loadSystemSettings(); // Reload to confirm
      } else {
        alert('Failed to save security policies: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error saving security policies:', error);
      alert('Failed to save security policies.');
    });
}

function loadGradingScales() {
  const listContainer = document.getElementById('grading-scales-list');
  if (!listContainer) return;

  listContainer.innerHTML = '<p class="loading">Loading grading scales...</p>';

  fetch('../../../backend/router.php/api/superadmin/grading-scales')
    .then(response => response.json())
    .then(scales => {
      if (!scales || scales.length === 0) {
        listContainer.innerHTML = '<p>No grading scales found.</p>';
        return;
      }

      listContainer.innerHTML = '';
      scales.forEach(scale => {
        const scaleItem = document.createElement('div');
        scaleItem.className = 'grading-scale-item';
        const activeClass = scale.is_active ? 'active' : 'inactive';
        const range = `${scale.min_score}-${scale.max_score}`;
        scaleItem.innerHTML = `
          <div class="scale-info">
            <div class="scale-name">${scale.name}</div>
            <div class="scale-range">Range: ${range}</div>
            <div class="scale-grade">Grade: ${scale.grade_letter}</div>
            <div class="scale-status ${activeClass}">Active: ${scale.is_active ? 'Yes' : 'No'}</div>
          </div>
          <div class="scale-actions">
            <button class="btn-icon edit-scale-btn" data-id="${scale.id}">Edit</button>
            <button class="btn-icon delete-scale-btn" data-id="${scale.id}">Delete</button>
            <button class="btn-icon ${activeClass}-btn activate-scale-btn" data-id="${scale.id}">
              ${scale.is_active ? 'Deactivate' : 'Activate'}
            </button>
          </div>
        `;
        listContainer.appendChild(scaleItem);
      });

      // Add event listeners
      document.querySelectorAll('.edit-scale-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = e.target.dataset.id;
          openGradingScaleModal('edit', id);
        });
      });

      document.querySelectorAll('.delete-scale-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = e.target.dataset.id;
          if (confirm('Are you sure you want to delete this grading scale?')) {
            deleteGradingScale(id);
          }
        });
      });

      document.querySelectorAll('.activate-scale-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = e.target.dataset.id;
          activateGradingScale(id);
        });
      });
    })
    .catch(error => {
      console.error('Error loading grading scales:', error);
      listContainer.innerHTML = '<p>Error loading grading scales.</p>';
    });
}

function openGradingScaleModal(mode, id = null) {
  const modal = document.getElementById('grading-scale-modal');
  if (!modal) {
    // Create modal if it doesn't exist
    const modalHtml = `
      <div id="grading-scale-modal" class="modal">
        <div class="modal-content">
          <h3 id="modal-title">${mode === 'add' ? 'Add New Grading Scale' : 'Edit Grading Scale'}</h3>
          <form id="grading-scale-form">
            <input type="hidden" id="scale-id" value="${id || ''}">
            <div class="form-group">
              <label for="scale-name">Name:</label>
              <input type="text" id="scale-name" required>
            </div>
            <div class="form-group">
              <label for="scale-min-score">Min Score:</label>
              <input type="number" id="scale-min-score" step="0.01" min="0" max="100" required>
            </div>
            <div class="form-group">
              <label for="scale-max-score">Max Score:</label>
              <input type="number" id="scale-max-score" step="0.01" min="0" max="100" required>
            </div>
            <div class="form-group">
              <label for="scale-grade-letter">Grade Letter:</label>
              <input type="text" id="scale-grade-letter" maxlength="5" required>
            </div>
            <div class="form-group">
              <label>
                <input type="checkbox" id="scale-active"> Set as Active
              </label>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-primary">Save</button>
              <button type="button" id="close-grading-modal" class="btn-secondary">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Event listeners for modal
    document.getElementById('close-grading-modal').addEventListener('click', () => {
      modal.style.display = 'none';
    });

    document.getElementById('grading-scale-form').addEventListener('submit', (e) => {
      e.preventDefault();
      if (mode === 'add') {
        createGradingScale();
      } else {
        updateGradingScale(id);
      }
    });

    // Close on outside click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }

  modal.style.display = 'block';
  document.getElementById('modal-title').textContent = mode === 'add' ? 'Add New Grading Scale' : 'Edit Grading Scale';

  if (mode === 'edit') {
    // Load existing data
    fetch(`../../../backend/router.php/api/superadmin/grading-scales/${id}`)
      .then(response => response.json())
      .then(scale => {
        document.getElementById('scale-id').value = scale.id;
        document.getElementById('scale-name').value = scale.name;
        document.getElementById('scale-min-score').value = scale.min_score;
        document.getElementById('scale-max-score').value = scale.max_score;
        document.getElementById('scale-grade-letter').value = scale.grade_letter;
        document.getElementById('scale-active').checked = scale.is_active;
      })
      .catch(error => {
        console.error('Error loading scale:', error);
        alert('Failed to load grading scale.');
      });
  } else {
    // Reset form for add
    document.getElementById('scale-id').value = '';
    document.getElementById('scale-name').value = '';
    document.getElementById('scale-min-score').value = '';
    document.getElementById('scale-max-score').value = '';
    document.getElementById('scale-grade-letter').value = '';
    document.getElementById('scale-active').checked = false;
  }
}

function createGradingScale() {
  const formData = {
    name: document.getElementById('scale-name').value,
    min_score: parseFloat(document.getElementById('scale-min-score').value),
    max_score: parseFloat(document.getElementById('scale-max-score').value),
    grade_letter: document.getElementById('scale-grade-letter').value,
    is_active: document.getElementById('scale-active').checked
  };

  fetch('../../../backend/router.php/api/superadmin/grading-scales', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(formData)
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Grading scale created successfully!');
        document.getElementById('grading-scale-modal').style.display = 'none';
        loadGradingScales();
      } else {
        alert('Failed to create grading scale: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error creating scale:', error);
      alert('Failed to create grading scale.');
    });
}

function updateGradingScale(id) {
  const formData = {
    name: document.getElementById('scale-name').value,
    min_score: parseFloat(document.getElementById('scale-min-score').value),
    max_score: parseFloat(document.getElementById('scale-max-score').value),
    grade_letter: document.getElementById('scale-grade-letter').value,
    is_active: document.getElementById('scale-active').checked
  };

  fetch(`../../../backend/router.php/api/superadmin/grading-scales/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(formData)
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Grading scale updated successfully!');
        document.getElementById('grading-scale-modal').style.display = 'none';
        loadGradingScales();
      } else {
        alert('Failed to update grading scale: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error updating scale:', error);
      alert('Failed to update grading scale.');
    });
}

function deleteGradingScale(id) {
  fetch(`../../../backend/router.php/api/superadmin/grading-scales/${id}`, {
    method: 'DELETE'
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Grading scale deleted successfully!');
        loadGradingScales();
      } else {
        alert('Failed to delete grading scale: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error deleting scale:', error);
      alert('Failed to delete grading scale.');
    });
}

function activateGradingScale(id) {
  fetch(`../../../backend/router.php/api/superadmin/grading-scales/${id}/activate`, {
    method: 'PATCH'
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Grading scale activated successfully!');
        loadGradingScales();
      } else {
        alert('Failed to activate grading scale: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error activating scale:', error);
      alert('Failed to activate grading scale.');
    });
}

function loadEncryptionStatus() {
  const statusContainer = document.getElementById('encryption-status-container');
  if (!statusContainer) return;

  fetch('../../../backend/router.php/api/superadmin/encryption-status')
    .then(response => response.json())
    .then(status => {
      // Update DB Encryption
      const dbStatusEl = document.getElementById('db-encryption-status');
      if (dbStatusEl) {
        const statusClass = status.db_encryption.status === 'enabled' ? 'healthy' : 'error';
        dbStatusEl.className = `status-indicator ${statusClass}`;
        dbStatusEl.innerHTML = `
          <i class="icon ${statusClass}"></i>
          <span>${status.db_encryption.status}</span>
          <small>${status.db_encryption.details}</small>
        `;
      }

      // Update File Encryption
      const fileStatusEl = document.getElementById('file-encryption-status');
      if (fileStatusEl) {
        const statusClass = status.file_encryption.status === 'enabled' ? 'healthy' : 'warning';
        fileStatusEl.className = `status-indicator ${statusClass}`;
        fileStatusEl.innerHTML = `
          <i class="icon ${statusClass}"></i>
          <span>${status.file_encryption.status}</span>
          <small>${status.file_encryption.details}</small>
        `;
      }

      // Update SSL Status
      const sslStatusEl = document.getElementById('ssl-status');
      if (sslStatusEl) {
        const statusClass = status.ssl_status.status === 'valid' ? 'healthy' : 'warning';
        sslStatusEl.className = `status-indicator ${statusClass}`;
        sslStatusEl.innerHTML = `
          <i class="icon ${statusClass}"></i>
          <span>${status.ssl_status.status}</span>
          <small>${status.ssl_status.details}</small>
        `;
      }

      // Update API Security
      const apiStatusEl = document.getElementById('api-security-status');
      if (apiStatusEl) {
        const statusClass = status.api_security.status === 'secure' ? 'healthy' : 'error';
        apiStatusEl.className = `status-indicator ${statusClass}`;
        apiStatusEl.innerHTML = `
          <i class="icon ${statusClass}"></i>
          <span>${status.api_security.status}</span>
          <small>${status.api_security.details}</small>
        `;
      }
    })
    .catch(error => {
      console.error('Error loading encryption status:', error);
      alert('Failed to load encryption status.');
    });
}

// Event listeners for settings and grading scales
function setupSettingsAndScalesEventListeners() {
  // General settings form
  const generalForm = document.getElementById('general-settings-form');
  if (generalForm) {
    generalForm.addEventListener('submit', (e) => {
      e.preventDefault();
      saveGeneralSettings();
    });
  }

  // Security policies form
  const securityForm = document.getElementById('security-policies-form');
  if (securityForm) {
    securityForm.addEventListener('submit', (e) => {
      e.preventDefault();
      saveSecurityPolicies();
    });
  }

  // Add grading scale button
  const addScaleBtn = document.getElementById('add-grading-scale-btn');
  if (addScaleBtn) {
    addScaleBtn.addEventListener('click', () => openGradingScaleModal('add'));
  }

  // Refresh encryption status button
  const refreshStatusBtn = document.getElementById('refresh-encryption-status-btn');
  if (refreshStatusBtn) {
    refreshStatusBtn.addEventListener('click', loadEncryptionStatus);
  }

  // View encryption logs button
  const viewLogsBtn = document.getElementById('view-encryption-logs-btn');
  if (viewLogsBtn) {
    viewLogsBtn.addEventListener('click', () => {
      // Navigate to audit logs section
      const auditSection = document.getElementById('audit-logs-section');
      if (auditSection) {
        auditSection.scrollIntoView({ behavior: 'smooth' });
        // Filter for security logs
        const logTypeFilter = document.getElementById('audit-log-type-filter');
        if (logTypeFilter) {
          logTypeFilter.value = 'security';
          applyAuditLogFiltersAndSort();
        }
      }
    });
  }
}

// Initialize Super Admin functionality including CSV management, system settings, grading scales, and encryption status
function initSuperAdmin() {
  loadUsers();
  loadAuditLogs();
  loadStats();
  loadRecentActivity();
  loadBackupFiles();
  addSortingButtons();
  setupEventListeners();
  initCsvManagement(); // Initialize CSV management
  loadSystemSettings();
  loadGradingScales();
  loadEncryptionStatus();
  setupSettingsAndScalesEventListeners();
  // Auto refresh every 5 seconds
  setInterval(() => {
    loadUsers();
    loadAuditLogs();
    loadStats();
    loadRecentActivity();
    loadCsvStats(); // Refresh CSV stats
    loadGradingScales();
    loadEncryptionStatus();
  }, 5000);
}
