// Main JavaScript for AI-Powered Grading System

// ================================
// Super Admin Navigation & Tabs JS
// ================================

// Cache DOM elements
const navLinks = document.querySelectorAll(".sidebar-nav a");
const navItems = document.querySelectorAll(".sidebar-nav ul li");
const sections = document.querySelectorAll(".tab-section");

// --------------------
// Handle click active state for sidebar
// --------------------
navLinks.forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const targetId = link.getAttribute("href").substring(1);

    // Update active class in nav
    navItems.forEach((li) => li.classList.remove("active"));
    link.parentElement.classList.add("active");

    // Show corresponding section
    sections.forEach((section) => {
      section.classList.toggle("hidden", section.id !== targetId);
      section.classList.toggle("active", section.id === targetId);
    });

    // Smooth scroll to section
    const targetSection = document.getElementById(targetId);
    if (targetSection) {
      targetSection.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
});

// --------------------
// Highlight nav item on scroll
// --------------------
window.addEventListener("scroll", () => {
  const scrollY = window.pageYOffset;
  let currentSectionId = "";

  sections.forEach((section, index) => {
    const sectionTop = section.offsetTop - 100;
    const sectionHeight = section.offsetHeight;

    if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
      currentSectionId = section.id;
    }

    // Ensure last section is active if near bottom
    if (
      index === sections.length - 1 &&
      window.innerHeight + scrollY >= document.body.offsetHeight - 50
    ) {
      currentSectionId = section.id;
    }
  });

  navItems.forEach((li) => {
    li.classList.toggle(
      "active",
      li.querySelector("a").getAttribute("href") === `#${currentSectionId}`
    );
  });
});

// Super-Admin Java Script
// Show the panel when edit is clicked
function openEditPanel(userId, name, email, role) {
  document.getElementById("edit-user-id").value = userId;
  document.getElementById("edit-user-name").value = name;
  document.getElementById("edit-user-email").value = email;
  document.getElementById("edit-user-role").value = role;

  document.getElementById("edit-user-panel").style.display = "block";
}

// Close button
document
  .getElementById("close-edit-panel")
  .addEventListener("click", function () {
    document.getElementById("edit-user-panel").style.display = "none";
  });

// Load system stats for dashboard
function loadStats() {
  fetch("/backend/routes/api.php?path=/api/superadmin/stats")
    .then((response) => response.json())
    .then((data) => {
      // Update Server Status
      const serverStatus = document.querySelector(".card:nth-child(1) p");
      serverStatus.textContent = data.server_status;
      serverStatus.className =
        data.server_status === "Online" ? "status-online" : "status-offline";

      // Update Uptime
      const uptimeSpan = document.querySelector(".card:nth-child(1) span");
      uptimeSpan.textContent = "Uptime: " + data.uptime;

      // Update Active Users
      const activeUsers = document.querySelector(".card:nth-child(2) .metric");
      activeUsers.textContent = data.users;

      // Update Error Logs (24h)
      const errorLogs = document.querySelector(".card:nth-child(3) .metric");
      errorLogs.textContent = data.error_logs_24h;

      // Update Database Health
      const dbHealth = document.querySelector(".card:nth-child(4) p");
      dbHealth.textContent = data.db_health;
      dbHealth.className =
        data.db_health === "Healthy" ? "status-healthy" : "status-unhealthy";

      // Update Last Backup
      const lastBackupSpan = document.querySelector(".card:nth-child(4) span");
      lastBackupSpan.textContent = "Last Backup: " + data.last_backup;
    })
    .catch((err) => console.error("Failed to load stats:", err));
}

function loadLogs() {
  const search = document.querySelector('#audit-logs input[type="text"]').value;
  const status = document.querySelector(
    "#audit-logs select:nth-of-type(1)"
  ).value;
  const sort = document.querySelector(
    "#audit-logs select:nth-of-type(2)"
  ).value;
  const params = new URLSearchParams({
    search: search,
    status: status !== "Filter by Status" ? status.toLowerCase() : "",
    sort:
      sort !== "Sort by" ? sort.toLowerCase().replace(" first", "") : "newest",
  });
  fetch(`/backend/routes/api.php?path=/api/superadmin/logs&${params}`)
    .then((response) => response.json())
    .then((data) => {
      const tbody = document.querySelector("#audit-logs table tbody");
      tbody.innerHTML = "";
      data.forEach((log) => {
        const row = `<tr>
                <td>${log.timestamp}</td>
                <td>${log.user_id}</td>
                <td>${log.action}</td>
                <td><span class="status-tag ${
                  log.status === "success" ? "success" : "error"
                }">${log.status}</span></td>
              </tr>`;
        tbody.innerHTML += row;
      });
    })
    .catch((err) => console.error("Failed to load logs:", err));
}

function getRoleName(role_id) {
  const roles = {
    1: "Super Admin",
    2: "MIS Admin",
    3: "Professor",
    4: "Student",
  };
  return roles[role_id] || "Unknown";
}

function deactivateUser(userId) {
  fetch("/backend/routes/api.php?path=/api/superadmin/users/deactivate", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      user_id: userId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.success ? "User deactivated" : "Failed");
      loadUsers();
    });
}

function activateUser(userId) {
  fetch("/backend/routes/api.php?path=/api/superadmin/users/activate", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      user_id: userId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.success ? "User activated" : "Failed");
      loadUsers();
    });
}

function editUser(userId) {
  // Fetch user data and populate modal
  fetch(`/backend/routes/api.php?path=/api/superadmin/users`)
    .then((response) => response.json())
    .then((data) => {
      const user = data.find((u) => u.id == userId);
      if (user) {
        document.getElementById("edit-user-id").value = user.id;
        document.getElementById("edit-user-name").value = user.name;
        document.getElementById("edit-user-email").value = user.email;
        document.getElementById("edit-user-role").value = user.role_id;
        document.getElementById("edit-user-modal").classList.remove("hidden");
      }
    });
}

// Close modal
document.querySelector(".close-modal").addEventListener("click", () => {
  document.getElementById("edit-user-modal").classList.add("hidden");
});

// Edit user form submit
document.getElementById("edit-user-form").addEventListener("submit", (e) => {
  e.preventDefault();
  const userId = document.getElementById("edit-user-id").value;
  const name = document.getElementById("edit-user-name").value;
  const email = document.getElementById("edit-user-email").value;
  const role = document.getElementById("edit-user-role").value;
  fetch(`/backend/routes/api.php?path=/api/superadmin/users/${userId}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      name,
      email,
      role_id: role,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.success ? "User updated" : "Failed");
      document.getElementById("edit-user-modal").classList.add("hidden");
      loadUsers();
    });
});

// Load AI config
function loadAIConfig() {
  fetch("/backend/routes/api.php?path=/api/superadmin/ai-config")
    .then((response) => response.json())
    .then((data) => {
      const form = document.querySelector("#ai-config form");
      form.querySelector('input[type="checkbox"]:nth-of-type(1)').checked =
        data.enabled;
      // Update other fields if needed
    });
}

// Load system settings
function loadSystemSettings() {
  fetch("/backend/routes/api.php?path=/api/superadmin/settings")
    .then((response) => response.json())
    .then((data) => {
      const form = document.querySelector("#settings form");
      form.querySelector('input[type="text"]').value = data.site_name;
      // Update other fields
    });
}

// Load data when sections are shown
document
  .querySelector('a[href="#user-roles"]')
  .addEventListener("click", loadUsers);
document
  .querySelector('a[href="#audit-logs"]')
  .addEventListener("click", loadLogs);
document
  .querySelector('a[href="#ai-config"]')
  .addEventListener("click", loadAIConfig);
document
  .querySelector('a[href="#settings"]')
  .addEventListener("click", loadSystemSettings);

// Search functionality for users
document
  .querySelector('#user-roles input[type="text"]')
  .addEventListener("input", (e) => {
    loadUsers();
  });

// Filter functionality for users
document
  .querySelector("#user-roles select:nth-of-type(1)")
  .addEventListener("change", (e) => {
    loadUsers();
  });

// Sort functionality for users
document
  .querySelector("#user-roles select:nth-of-type(2)")
  .addEventListener("change", (e) => {
    loadUsers();
  });

// Search functionality for logs
document
  .querySelector('#audit-logs input[type="text"]')
  .addEventListener("input", (e) => {
    loadLogs();
  });

// Filter functionality for logs
document
  .querySelector("#audit-logs select:nth-of-type(1)")
  .addEventListener("change", (e) => {
    loadLogs();
  });

// Sort functionality for logs
document
  .querySelector("#audit-logs select:nth-of-type(2)")
  .addEventListener("change", (e) => {
    loadLogs();
  });

function deleteUser(userId) {
  if (confirm("Are you sure you want to delete this user?")) {
    fetch(`../../backend/routes/api.php?path=/api/superadmin/users/${userId}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.success ? "User deleted" : "Failed");
        loadUsers();
      });
  }
}

// Update loadUsers to include delete button
function loadUsers() {
  const search = document
    .querySelector('#user-roles input[type="text"]')
    .value.trim();
  const role = document.querySelector(
    "#user-roles select:nth-of-type(1)"
  ).value;
  const sort = document.querySelector(
    "#user-roles select:nth-of-type(2)"
  ).value;

  const params = new URLSearchParams({
    search: search,
    role: role !== "Filter by Role" ? role : "",
    sort:
      sort !== "Sort by"
        ? sort
            .toLowerCase()
            .replace(" (a-z)", "")
            .replace("date created", "date")
        : "name",
  });

  fetch(`/backend/routes/api.php?path=/api/superadmin/users&${params}`)
    .then((response) => response.json())
    .then((data) => {
      const tbody = document.querySelector("#user-roles table tbody");
      tbody.innerHTML = "";

      if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="6">No users found</td></tr>`;
        return;
      }

      data.forEach((user) => {
        const roleName = getRoleName(user.role_id);
        const status = user.active ? "Active" : "Inactive";
        const statusClass = user.active ? "success" : "error";

        const row = `
          <tr>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${roleName}</td>
            <td><span class="status-tag ${statusClass}">${status}</span></td>
            <td>${user.created_at || "N/A"}</td>
            <td>
              <button class="btn-icon" onclick="editUser(${
                user.id
              })">Edit</button>
              ${
                user.active
                  ? `<button class="btn-icon danger" onclick="deactivateUser(${user.id})">Deactivate</button>`
                  : `<button class="btn-icon" onclick="activateUser(${user.id})">Activate</button>`
              }
              <button class="btn-icon danger" onclick="deleteUser(${
                user.id
              })">Delete</button>
            </td>
          </tr>`;
        tbody.innerHTML += row;
      });
    })
    .catch((err) => {
      console.error("Failed to load users:", err);
    });
}

// Backup database button
document
  .querySelector("#database .card:nth-child(3) button")
  .addEventListener("click", () => {
    fetch("/backend/routes/api.php?path=/api/superadmin/backup", {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.success ? "Backup created: " + data.file : "Backup failed");
      });
  });

// Export logs button
const exportBtn = document.createElement("button");
exportBtn.textContent = "Export Logs";
exportBtn.className = "btn-primary";
document.querySelector("#audit-logs .toolbar").appendChild(exportBtn);
exportBtn.addEventListener("click", () => {
  fetch("/backend/routes/api.php?path=/api/superadmin/logs")
    .then((response) => response.json())
    .then((data) => {
      const csv =
        "Timestamp,User,Action,Status\n" +
        data
          .map(
            (log) =>
              `${log.timestamp},${log.user_id},${log.action},${log.status}`
          )
          .join("\n");
      const blob = new Blob([csv], {
        type: "text/csv",
      });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "audit_logs.csv";
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
const userRolesToolbar = document.querySelector("#user-roles .toolbar");
const refreshBtn = document.createElement("button");
refreshBtn.textContent = "Refresh Users";
refreshBtn.className = "btn-secondary";
refreshBtn.style.marginLeft = "10px";
refreshBtn.addEventListener("click", () => {
  loadUsers();
});
userRolesToolbar.appendChild(refreshBtn);

// For AI config save
document.querySelector("#ai-config form").addEventListener("submit", (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const config = {
    enabled: formData.get("enable_ai") === "on",
  };
  fetch("../../backend/routes/api.php?path=/api/superadmin/ai-config", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(config),
  })
    .then((response) => response.json())
    .then((data) => alert(data.success ? "AI config saved" : "Failed"));
});

// For settings save
document.querySelector("#settings form").addEventListener("submit", (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const settings = {
    site_name: formData.get("site_name"),
  };
  fetch("../../backend/routes/api.php?path=/api/superadmin/settings", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(settings),
  })
    .then((response) => response.json())
    .then((data) => alert(data.success ? "Settings saved" : "Failed"));
});

function loadDashboardData() {
  const role = document.body.getAttribute("data-role");
  if (role === "super-admin") {
    loadSuperAdminData();
  } else if (role === "admin") {
    loadAdminData();
  } else if (role === "professor") {
    loadProfessorData();
  } else if (role === "student") {
    loadStudentData();
  }
}

function loadSuperAdminData() {
  // Removed to avoid conflict with inline script in super-admin.php
}

function loadLogs() {
  // Removed to avoid conflict with inline script in super-admin.php
}

function loadUsers() {
  // Removed to avoid conflict with inline script in super-admin.php
}

function loadSystemStats() {
  // Removed to avoid conflict with inline script in super-admin.php
}

function loadAdminData() {
  // Load students, professors, courses, grades
  fetch("../../backend/routes/api.php?path=/api/admin/students")
    .then((response) => response.json())
    .then((data) => {
      // Populate student table
      const studentTable = document.querySelector(
        "#student-records table tbody"
      );
      if (studentTable) {
        studentTable.innerHTML = data
          .map(
            (student) => `
                    <tr>
                        <td>${student.id}</td>
                        <td>${student.name}</td>
                        <td>${student.program}</td>
                        <td><span class="status-tag active">Active</span></td>
                        <td>${student.created_at || "N/A"}</td>
                        <td>
                            <button class="btn-icon">Edit</button>
                            <button class="btn-icon danger">Remove</button>
                        </td>
                    </tr>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading students:", error);
      alert("Failed to load students.");
    });

  fetch("../../backend/routes/api.php?path=/api/admin/professors")
    .then((response) => response.json())
    .then((data) => {
      // Populate professor table
      const professorTable = document.querySelector(
        "#professor-records table tbody"
      );
      if (professorTable) {
        professorTable.innerHTML = data
          .map(
            (professor) => `
                    <tr>
                        <td>${professor.id}</td>
                        <td>${professor.name}</td>
                        <td>Computer Studies</td>
                        <td><span class="status-tag active">Active</span></td>
                        <td>${professor.created_at || "N/A"}</td>
                        <td>
                            <button class="btn-icon">Edit</button>
                            <button class="btn-icon danger">Remove</button>
                        </td>
                    </tr>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading professors:", error);
      alert("Failed to load professors.");
    });

  loadAdminLogs();
  loadAdminStats();
}

function loadAdminLogs() {
  fetch("../../backend/routes/api.php?path=/api/admin/audit-logs")
    .then((response) => response.json())
    .then((data) => {
      const logTable = document.querySelector("#audit-logs table tbody");
      if (logTable) {
        logTable.innerHTML = data
          .map(
            (log) => `
                    <tr>
                        <td>${log.timestamp}</td>
                        <td>${log.user_id}</td>
                        <td>${log.action}</td>
                        <td><span class="status-tag ${
                          log.status === "success" ? "success" : "error"
                        }">${log.status}</span></td>
                    </tr>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading admin logs:", error);
      alert("Failed to load audit logs.");
    });
}

function loadAdminStats() {
  // Load stats for admin dashboard
  fetch("../../backend/routes/api.php?path=/api/admin/students")
    .then((response) => response.json())
    .then((data) => {
      const studentCount = document.querySelector(".card:nth-child(1) p");
      if (studentCount) studentCount.textContent = `${data.length} Students`;
    })
    .catch((error) => console.error("Error loading student stats:", error));

  fetch("../../backend/routes/api.php?path=/api/admin/professors")
    .then((response) => response.json())
    .then((data) => {
      const professorCount = document.querySelector(".card:nth-child(2) p");
      if (professorCount)
        professorCount.textContent = `${data.length} Professors`;
    })
    .catch((error) => console.error("Error loading professor stats:", error));

  fetch("../../backend/routes/api.php?path=/api/admin/courses")
    .then((response) => response.json())
    .then((data) => {
      const courseCount = document.querySelector(".card:nth-child(3) p");
      if (courseCount) courseCount.textContent = `${data.length} Courses`;
    })
    .catch((error) => console.error("Error loading course stats:", error));

  fetch("../../backend/routes/api.php?path=/api/admin/grades")
    .then((response) => response.json())
    .then((data) => {
      const gradeCount = document.querySelector(".card:nth-child(4) p");
      if (gradeCount) gradeCount.textContent = `${data.length} Grades`;
    })
    .catch((error) => console.error("Error loading grade stats:", error));
}

function loadProfessorData() {
  // Load courses for class management
  fetch("../../backend/routes/api.php?path=/api/professor/courses")
    .then((response) => response.json())
    .then((data) => {
      // Populate courses table
      const courseTable = document.querySelector(
        "#class-management table tbody"
      );
      if (courseTable) {
        courseTable.innerHTML = data
          .map(
            (course) => `
                    <tr>
                        <td>${course.code}</td>
                        <td>${course.name}</td>
                        <td>${course.schedule}</td>
                        <td>${course.enrolled || "N/A"}</td>
                        <td>
                            <button class="btn-icon">View</button>
                            <button class="btn-icon danger">Remove</button>
                        </td>
                    </tr>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading courses:", error);
      alert("Failed to load courses.");
    });

  // Load students and grades
  fetch("../../backend/routes/api.php?path=/api/professor/students")
    .then((response) => response.json())
    .then((data) => {
      // Populate student list
      const studentList = document.getElementById("student-list");
      if (studentList) {
        studentList.innerHTML = data
          .map(
            (student) => `
                    <option value="${student.id}">${student.name}</option>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading students:", error);
      alert("Failed to load students.");
    });

  loadGrades();
}

function loadStudentData() {
  // Load student's grades and courses
  fetch("../../backend/routes/api.php?path=/api/student/grades")
    .then((response) => response.json())
    .then((data) => {
      // Populate grades table
      const gradesTable = document.querySelector("#grades-table tbody");
      if (gradesTable) {
        gradesTable.innerHTML = data
          .map(
            (grade) => `
                    <tr>
                        <td>${grade.course_name}</td>
                        <td>${grade.midterm_grade}</td>
                        <td>${grade.final_grade}</td>
                        <td>${grade.gpa}</td>
                    </tr>
                `
          )
          .join("");
      }

      // Update dashboard cards with stats
      if (data.length > 0) {
        const gpaCard = document.querySelector(".card:nth-child(1) p");
        if (gpaCard) {
          const avgGpa =
            data.reduce((sum, grade) => sum + parseFloat(grade.gpa || 0), 0) /
            data.length;
          gpaCard.textContent = `${avgGpa.toFixed(2)} GPA`;
        }

        const passedCard = document.querySelector(".card:nth-child(2) p");
        if (passedCard) {
          const passedCount = data.filter(
            (grade) => parseFloat(grade.gpa || 0) >= 2.0
          ).length;
          passedCard.textContent = `${passedCount} Passed`;
        }

        const atRiskCard = document.querySelector(".card:nth-child(3) p");
        if (atRiskCard) {
          const atRiskCount = data.filter(
            (grade) => parseFloat(grade.gpa || 0) < 2.0
          ).length;
          atRiskCard.textContent = `${atRiskCount} At Risk`;
        }
      }
    })
    .catch((error) => {
      console.error("Error loading grades:", error);
      alert("Failed to load grades.");
    });

  // Load notifications
  fetch("../../backend/routes/api.php?path=/api/student/notifications")
    .then((response) => response.json())
    .then((data) => {
      const notificationsList = document.querySelector("#notifications ul");
      if (notificationsList) {
        notificationsList.innerHTML = data
          .map(
            (notification) => `
                    <li>${notification.message}</li>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading notifications:", error);
    });

  // Load quizzes
  fetch("../../backend/routes/api.php?path=/api/student/quizzes")
    .then((response) => response.json())
    .then((data) => {
      const quizzesTable = document.querySelector("#quizzes table tbody");
      if (quizzesTable) {
        quizzesTable.innerHTML = data
          .map(
            (quiz) => `
                    <tr>
                        <td>${quiz.title}</td>
                        <td>${quiz.subject}</td>
                        <td><span class="status-tag ${quiz.status.toLowerCase()}">${
              quiz.status
            }</span></td>
                        <td><a href="quiz.php?id=${
                          quiz.id
                        }" class="btn-primary">${quiz.action}</a></td>
                    </tr>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading quizzes:", error);
    });
}

function setupGradeForm() {
  const form = document.getElementById("grade-form");
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    fetch("../../backend/routes/api.php?path=/api/professor/grades", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          alert("Grade submitted successfully");
          loadGrades();
        } else {
          alert("Error submitting grade");
        }
      })
      .catch((error) => {
        console.error("Error submitting grade:", error);
        alert("Error submitting grade");
      });
  });
}

function loadGrades() {
  fetch("../../backend/routes/api.php?path=/api/professor/grades")
    .then((response) => response.json())
    .then((data) => {
      const gradesTable = document.querySelector("#grades-table tbody");
      if (gradesTable) {
        gradesTable.innerHTML = data
          .map(
            (grade) => `
                    <tr>
                        <td>${grade.student_name}</td>
                        <td>${grade.course_name}</td>
                        <td>${grade.midterm_quizzes}</td>
                        <td>${grade.midterm_exam}</td>
                        <td>${grade.final_quizzes}</td>
                        <td>${grade.final_exam}</td>
                        <td>${grade.midterm_grade}</td>
                        <td>${grade.final_grade}</td>
                        <td>${grade.gpa}</td>
                    </tr>
                `
          )
          .join("");
      }
    })
    .catch((error) => {
      console.error("Error loading grades:", error);
      alert("Failed to load grades.");
    });
}

function deactivateUser(userId) {
  fetch("../../backend/routes/api.php?path=/api/superadmin/users/deactivate", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ user_id: userId }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("User deactivated");
        loadSuperAdminData();
      } else {
        alert("Error deactivating user");
      }
    })
    .catch((error) => {
      console.error("Error deactivating user:", error);
      alert("Error deactivating user");
    });
}

function setupAdditionalEventHandlers() {
  // Add event listeners for buttons and forms that were missing

  // Example: Report generation button in admin dashboard
  const reportBtn = document.querySelector("#department-reports button");
  if (reportBtn) {
    reportBtn.addEventListener("click", () => {
      alert("Report generation is not yet implemented.");
    });
  }

  // Example: Save settings forms
  const settingsForms = document.querySelectorAll(".settings-form");
  settingsForms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("Settings saved (mock).");
    });
  });

  // Example: Edit user button placeholder
  const editButtons = document.querySelectorAll(".btn-icon");
  editButtons.forEach((button) => {
    button.addEventListener("click", () => {
      alert("Edit functionality is not yet implemented.");
    });
  });

  // Example: Announcement post form
  const announcementForm = document.querySelector("#announcements form");
  if (announcementForm) {
    announcementForm.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("Announcement posted (mock).");
    });
  }

  // Example: AI config save form
  const aiConfigForm = document.querySelector("#ai-config form");
  if (aiConfigForm) {
    aiConfigForm.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("AI config saved (mock).");
    });
  }
}
