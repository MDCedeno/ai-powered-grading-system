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
      files.forEach((filename) => {
        const fileItem = document.createElement("div");
        fileItem.className = "backup-file-item";
        fileItem.textContent = filename;

        const openBtn = document.createElement("button");
        openBtn.textContent = "Open";
        openBtn.className = "btn-secondary btn-small";
        openBtn.addEventListener("click", () => {
          window.open(`../../../../backups/${filename}`, "_blank");
        });

        const copyBtn = document.createElement("button");
        copyBtn.textContent = "Copy";
        copyBtn.className = "btn-secondary btn-small";
        copyBtn.addEventListener("click", () => {
          navigator.clipboard.writeText(filename).then(() => {
            alert("Filename copied to clipboard");
          });
        });

        // Wrap buttons in btn-group for layout
        const btnGroup = document.createElement("div");
        btnGroup.className = "btn-group";
        btnGroup.appendChild(openBtn);
        btnGroup.appendChild(copyBtn);
        fileItem.appendChild(btnGroup);

        fileItem.addEventListener("click", () => {
          // Deselect all
          document
            .querySelectorAll(".backup-file-item")
            .forEach((el) => el.classList.remove("selected"));
          fileItem.classList.add("selected");
          selectedBackupFile = filename;
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

// Update the main init function to include CSV management
function initSuperAdmin() {
  loadUsers();
  loadAuditLogs();
  loadStats();
  loadRecentActivity();
  loadBackupFiles();
  addSortingButtons();
  setupEventListeners();
  initCsvManagement(); // Initialize CSV management
  // Auto refresh every 5 seconds
  setInterval(() => {
    loadUsers();
    loadAuditLogs();
    loadStats();
    loadRecentActivity();
    loadCsvStats(); // Refresh CSV stats
  }, 5000);
}
