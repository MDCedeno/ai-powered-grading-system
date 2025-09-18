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

  // Update interval button
  const updateIntervalBtn = document.getElementById("update-interval-btn");
  if (updateIntervalBtn) {
    updateIntervalBtn.addEventListener("click", async () => {
      const interval = document.getElementById("auto-backup-interval").value;
      try {
        const response = await fetch(
          "../../../backend/router.php/api/superadmin/auto-backup-interval",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ interval: parseInt(interval) }),
          }
        );
        if (!response.ok) {
          throw new Error("Failed to update auto-backup interval");
        }
        const result = await response.json();
        if (!result.success) {
          throw new Error("Failed to update auto-backup interval");
        }
        alert("Auto-backup interval updated successfully");
      } catch (error) {
        alert("Failed to update auto-backup interval");
      }
    });
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
      if (intervalInput) {
        intervalInput.value = data.interval;
      }
    }
  };
  xhr.send();
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
