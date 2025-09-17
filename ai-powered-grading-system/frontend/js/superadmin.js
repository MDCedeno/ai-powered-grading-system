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
  return text.toLowerCase().includes(search.toLowerCase());
}

// Get role name from role_id
function getRoleName(roleId) {
  const roles = {1: 'Super Admin', 2: 'MIS Admin', 3: 'Professor', 4: 'Student'};
  return roles[roleId] || 'Unknown';
}

// Render table rows for users
function renderUsersTable(data) {
  const tbody = document.querySelector('#user-roles table tbody');
  if (!tbody) return;
  tbody.innerHTML = '';
  if (data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7">No users found</td></tr>';
    return;
  }
  data.forEach(user => {
    const roleName = getRoleName(user.role_id);
    const status = user.active == 1 ? 'Active' : 'Inactive';
    const actionButtonLabel = user.active == 1 ? 'Deactivate' : 'Activate';
    const actionButtonClass = user.active == 1 ? 'danger deactivate-btn' : 'success activate-btn';
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${user.id}</td>
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${roleName}</td>
      <td><span class="status-tag ${status.toLowerCase()}">${status}</span></td>
      <td>${user.created_at || 'N/A'}</td>
      <td>
        <button class="btn-icon edit-btn" data-id="${user.id}">Edit</button>
        <button class="btn-icon ${actionButtonClass}" data-id="${user.id}">${actionButtonLabel}</button>
      </td>
    `;
    tbody.appendChild(row);
  });

  // Add event listeners to buttons
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const userId = e.target.dataset.id;
      openEditPanel(userId);
    });
  });
  document.querySelectorAll('.deactivate-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const userId = e.target.dataset.id;
      deactivateUser(userId);
    });
  });
  document.querySelectorAll('.activate-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const userId = e.target.dataset.id;
      activateUser(userId);
    });
  });
}

// Render table rows for audit logs
function renderAuditLogsTable(data) {
  const tbody = document.querySelector('#audit-logs table tbody');
  if (!tbody) return;
  tbody.innerHTML = '';
  if (data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4">No logs found</td></tr>';
    return;
  }
  data.forEach(log => {
    const statusClass = log.status.toLowerCase() === 'success' ? 'success' : 'error';
    const row = `<tr>
      <td>${log.timestamp}</td>
      <td>${log.user}</td>
      <td>${log.action}</td>
      <td><span class="status-tag ${statusClass}">${log.status}</span></td>
    </tr>`;
    tbody.innerHTML += row;
  });
}

// Sort data array by column
function sortData(data, column, ascending) {
  return data.slice().sort((a, b) => {
    let valA = a[column] || '';
    let valB = b[column] || '';
    if (typeof valA === 'string') valA = valA.toLowerCase();
    if (typeof valB === 'string') valB = valB.toLowerCase();
    if (valA < valB) return ascending ? -1 : 1;
    if (valA > valB) return ascending ? 1 : -1;
    return 0;
  });
}

// Filter data
function filterUsers(search, userId, role, status) {
  let filtered = usersData;
  if (search) {
    filtered = filtered.filter(u =>
      matchesSearch(u.name, search) ||
      matchesSearch(u.email, search) ||
      matchesSearch(getRoleName(u.role_id), search) ||
      matchesSearch(u.active == 1 ? 'Active' : 'Inactive', search)
    );
  }
  if (userId) {
    filtered = filtered.filter(u => u.id.toString().includes(userId));
  }
  if (role && role !== 'Filter by Role') {
    const roleId = Object.keys({1: 'Super Admin', 2: 'MIS Admin', 3: 'Professor', 4: 'Student'}).find(k => getRoleName(k) === role);
    filtered = filtered.filter(u => u.role_id == roleId);
  }
  if (status && status !== 'Filter by Status') {
    const isActive = status === 'Active' ? 1 : 0;
    filtered = filtered.filter(u => u.active == isActive);
  }
  return filtered;
}

function filterAuditLogs(search, status) {
  let filtered = auditLogsData;
  if (search) {
    filtered = filtered.filter(l =>
      matchesSearch(l.user, search) ||
      matchesSearch(l.action, search) ||
      matchesSearch(l.status, search)
    );
  }
  if (status && status !== 'Filter by Status') {
    filtered = filtered.filter(l => l.status === status);
  }
  return filtered;
}

// Load data using XMLHttpRequest
function loadUsers() {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', '../../../backend/router.php/api/superadmin/users?limit=10', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      usersData = JSON.parse(xhr.responseText);
      applyUserFiltersAndSort();
    }
  };
  xhr.send();
}

function loadAuditLogs() {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', '../../../backend/router.php/api/superadmin/logs', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      auditLogsData = JSON.parse(xhr.responseText);
      applyAuditLogFiltersAndSort();
    }
  };
  xhr.send();
}

// Apply filters and sorting
function applyUserFiltersAndSort() {
  const search = document.getElementById('user-search')?.value.trim() || '';
  const userId = document.getElementById('user-id-search')?.value.trim() || '';
  const role = document.getElementById('role-filter')?.value || '';
  const status = document.getElementById('status-filter')?.value || '';
  let filtered = filterUsers(search, userId, role, status);
  if (userSort.column) {
    filtered = sortData(filtered, userSort.column, userSort.ascending);
  }
  renderUsersTable(filtered);
}

function applyAuditLogFiltersAndSort() {
  const search = document.querySelector('#audit-logs .toolbar input[type="text"]')?.value.trim() || '';
  const status = document.querySelector('#audit-logs .toolbar select')?.value || '';
  let filtered = filterAuditLogs(search, status);
  if (auditLogSort.column) {
    filtered = sortData(filtered, auditLogSort.column, auditLogSort.ascending);
  }
  renderAuditLogsTable(filtered);
}

// Add sorting buttons
function addSortingButtons() {
  // Users
  const userHeaders = document.querySelectorAll('#user-roles table thead th');
  userHeaders.forEach((th, index) => {
    if (index === 4 || index === 6) return; // skip status and actions
  const columnMap = ['id', 'name', 'email', 'role_id', 'status', 'created_at'];
    const column = columnMap[index];

    const btnAsc = document.createElement('button');
    btnAsc.textContent = '▲';
    btnAsc.className = 'sort-btn';
    btnAsc.style.marginLeft = '5px';
    btnAsc.addEventListener('click', () => {
      userSort.column = column;
      userSort.ascending = true;
      applyUserFiltersAndSort();
    });
    th.appendChild(btnAsc);

    const btnDesc = document.createElement('button');
    btnDesc.textContent = '▼';
    btnDesc.className = 'sort-btn';
    btnDesc.style.marginLeft = '5px';
    btnDesc.addEventListener('click', () => {
      userSort.column = column;
      userSort.ascending = false;
      applyUserFiltersAndSort();
    });
    th.appendChild(btnDesc);
  });
  // Audit Logs
  const auditHeaders = document.querySelectorAll('#audit-logs table thead th');
  auditHeaders.forEach((th, index) => {
    if (index === 3) return; // skip status
    const columnMap = ['timestamp', 'user', 'action', 'status'];
    const column = columnMap[index];

    const btnAsc = document.createElement('button');
    btnAsc.textContent = '▲';
    btnAsc.className = 'sort-btn';
    btnAsc.style.marginLeft = '5px';
    btnAsc.addEventListener('click', () => {
      auditLogSort.column = column;
      auditLogSort.ascending = true;
      applyAuditLogFiltersAndSort();
    });
    th.appendChild(btnAsc);

    const btnDesc = document.createElement('button');
    btnDesc.textContent = '▼';
    btnDesc.className = 'sort-btn';
    btnDesc.style.marginLeft = '5px';
    btnDesc.addEventListener('click', () => {
      auditLogSort.column = column;
      auditLogSort.ascending = false;
      applyAuditLogFiltersAndSort();
    });
    th.appendChild(btnDesc);
  });
}

// Event listeners
function setupEventListeners() {
  const userSearchInput = document.getElementById('user-search');
  const userIdSearchInput = document.getElementById('user-id-search');
  const roleFilter = document.getElementById('role-filter');
  const statusFilter = document.getElementById('status-filter');
  const auditInput = document.querySelector('#audit-logs .toolbar input[type="text"]');
  const auditSelect = document.querySelector('#audit-logs .toolbar select');

  if (userSearchInput) userSearchInput.addEventListener('input', applyUserFiltersAndSort);
  if (userIdSearchInput) userIdSearchInput.addEventListener('input', applyUserFiltersAndSort);
  if (roleFilter) roleFilter.addEventListener('change', applyUserFiltersAndSort);
  if (statusFilter) statusFilter.addEventListener('change', applyUserFiltersAndSort);
  if (auditInput) auditInput.addEventListener('input', applyAuditLogFiltersAndSort);
  if (auditSelect) auditSelect.addEventListener('change', applyAuditLogFiltersAndSort);

  // Remove refresh button event listener if it exists
  const refreshBtn = document.getElementById('refresh-users');
  if (refreshBtn) {
    refreshBtn.remove();
  }
}



function renderDashboardCards(stats) {
  // Server Status
  const serverStatusEl = document.querySelector('.cards-container .card:nth-child(1) p');
  if (serverStatusEl) {
    serverStatusEl.textContent = stats.server_status || 'Unknown';
    serverStatusEl.className = stats.server_status === 'Online' ? 'status-online' : 'status-offline';
  }
  const uptimeEl = document.querySelector('.cards-container .card:nth-child(1) span');
  if (uptimeEl) uptimeEl.textContent = `Uptime: ${stats.uptime || 'N/A'}`;

  // Active Users
  const activeUsersEl = document.querySelector('.cards-container .card:nth-child(2) p');
  if (activeUsersEl) activeUsersEl.textContent = stats.users?.toLocaleString() || '0';

  // Error Logs
  const errorLogsEl = document.querySelector('.cards-container .card:nth-child(3) p');
  if (errorLogsEl) {
    errorLogsEl.textContent = stats.error_logs_24h?.toString() || '0';
    errorLogsEl.className = stats.error_logs_24h > 0 ? 'metric error' : 'metric';
  }

  // Database Health
  const dbHealthEl = document.querySelector('.cards-container .card:nth-child(4) p');
  if (dbHealthEl) {
    dbHealthEl.textContent = stats.db_health || 'Unknown';
    dbHealthEl.className = stats.db_health === 'Healthy' ? 'status-healthy' : 'status-unhealthy';
  }
  const lastBackupEl = document.querySelector('.cards-container .card:nth-child(4) span');
  if (lastBackupEl) lastBackupEl.textContent = `Last Backup: ${stats.last_backup || 'Never'}`;
}

function renderRecentActivity(logs) {
  const tbody = document.getElementById('recent-activity');
  if (!tbody) return;
  tbody.innerHTML = '';

  if (logs.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4">No recent activity found.</td></tr>';
    return;
  }

  logs.forEach(log => {
    const statusClass = log.status.toLowerCase() === 'success' ? 'success' : 'error';
    const row = document.createElement('tr');
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
  xhr.open('GET', '../../../backend/router.php/api/superadmin/stats', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      statsData = JSON.parse(xhr.responseText);
      renderDashboardCards(statsData);
    }
  };
  xhr.send();
}

function loadRecentActivity() {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', '../../../backend/router.php/api/superadmin/logs?limit=5', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      recentActivityData = JSON.parse(xhr.responseText);
      renderRecentActivity(recentActivityData);
    }
  };
  xhr.send();
}

// Deactivate user
function deactivateUser(userId) {
  if (!confirm('Are you sure you want to deactivate this user?')) return;

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '../../../backend/router.php/api/superadmin/users/deactivate', true);
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if (xhr.status === 200) {
      loadUsers();
    } else {
      alert('Failed to deactivate user');
    }
  };
  xhr.send(JSON.stringify({ user_id: userId }));
}

// Activate user
function activateUser(userId) {
  if (!confirm('Are you sure you want to activate this user?')) return;

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '../../../backend/router.php/api/superadmin/users/activate', true);
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if (xhr.status === 200) {
      loadUsers();
    } else {
      alert('Failed to activate user');
    }
  };
  xhr.send(JSON.stringify({ user_id: userId }));
}

// Open edit panel
function openEditPanel(userId) {
  const user = usersData.find(u => u.id == userId);
  if (!user) return;

  document.getElementById('edit-user-id').value = user.id;
  document.getElementById('edit-user-name').value = user.name;
  document.getElementById('edit-user-email').value = user.email;
  document.getElementById('edit-user-role').value = user.role_id;

  const panel = document.getElementById('edit-user-panel');
  panel.classList.add('show');
}

document.addEventListener('click', (e) => {
  if (e.target.id === 'close-edit-panel') {
    const panel = document.getElementById('edit-user-panel');
    if (confirm('Are you sure you want to cancel editing? Changes will not be saved.')) {
      panel.classList.remove('show');
    }
  }
});

document.getElementById('edit-user-form').addEventListener('submit', (e) => {
  e.preventDefault();
  if (!confirm('Are you sure you want to save changes?')) {
    return;
  }
  const userId = document.getElementById('edit-user-id').value;
  const data = {
    name: document.getElementById('edit-user-name').value,
    email: document.getElementById('edit-user-email').value,
    role_id: document.getElementById('edit-user-role').value
  };

  const xhr = new XMLHttpRequest();
  xhr.open('PUT', '../../../backend/router.php/api/superadmin/users/' + userId, true);
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if (xhr.status === 200) {
      const panel = document.getElementById('edit-user-panel');
      panel.classList.remove('show');
      loadUsers();
    } else {
      alert('Failed to save changes');
    }
  };
  xhr.send(JSON.stringify(data));
});

function initSuperAdmin() {
  loadUsers();
  loadAuditLogs();
  loadStats();
  loadRecentActivity();
  addSortingButtons();
  setupEventListeners();
  // Auto refresh every 5 seconds
  setInterval(() => {
    loadUsers();
    loadAuditLogs();
    loadStats();
    loadRecentActivity();
  }, 5000);
}

document.addEventListener('DOMContentLoaded', initSuperAdmin);
