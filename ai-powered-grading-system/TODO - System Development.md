# TODO: AI-Powered Grading System Development

## System Setup
- [x] Set up database: Create migrations for users, courses, grades, logs (backend/database/migrations/)
- [x] Implement authentication: Develop login/signup in authController.php and frontend/views/login.php, signup.php
- [x] Seed database: Use UserSeeder.php to add dummy users for all roles
- [x] Configure routes: Set up API and web routes in backend/routes/

## AI Module Development
- [x] Develop AI grading algorithms in ai-module/app.py
- [x] Train models: Use data in ai-module/data/ and models in ai-module/models/
- [x] Integrate AI with backend: Connect to grading logic in grade.php model

## User Roles Implementation

### SUPER ADMIN
- [x] Develop system dashboard: Server status, uptime, error logs, user activity (frontend/views/super-admin/super-admin.php)
- [x] User role management: Create/edit/deactivate accounts (superAdminController.php)
- [x] Database management: Backup, restore, integrity checks
- [x] Audit logs: Full activity tracking with export
- [x] AI module configuration: Adjust algorithms, thresholds
- [ ] System settings: Grading scales, security, encryption

### ADMIN
- [x] Dashboard: System usage, grading status, analytics (frontend/views/admin/mis-admin.php)
- [x] User management: Approve/assign roles, update records (adminController.php)
- [x] Class & course management: Add/update courses, assign professors
- [x] Reports & analytics: Generate performance reports
- [x] Audit trail: View grading logs

### PROFESSOR
- [x] Dashboard: Student performance summary, grading progress (frontend/views/professor/professor.php)
- [x] Grade entry: Input scores, review AI computation (professorController.php)
- [x] Performance analytics: Trends, charts, at-risk indicators
- [x] Feedback & communication: Send alerts, comments
- [x] Resource suggestions: Attach materials

### STUDENT
- [x] Dashboard: Visual grade summary (frontend/views/student/student.php)
- [x] Grade breakdown: Detailed scores per activity
- [x] Performance insights: AI recommendations, study tips
- [x] Progress tracker: Track over time, compare semesters
- [x] Notifications: Alerts for updates, feedback

## Frontend Development
- [ ] Style with CSS: Use assets/css/ for each role
- [x] Add JavaScript: Enhance interactivity in frontend/js/main.js
- [ ] Components: Header, navbar, footer in frontend/components/

## Testing and Deployment
- [ ] Test all features: Ensure functionality across roles
- [ ] Debug and fix issues: Use logs in log.php model
- [ ] Deploy system: Set up on server with XAMPP
