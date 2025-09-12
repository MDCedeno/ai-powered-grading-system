# TODO: AI-Powered Grading System Development

## System Setup
- [x] Set up database: Create migrations for users, courses, grades, logs (backend/database/migrations/)
- [x] Implement authentication: Develop login/signup in authController.php and frontend/views/login.php, signup.php
- [x] Seed database: Use UserSeeder.php to add dummy users for all roles
- [x] Configure routes: Set up API and web routes in backend/routes/

## AI Module Development
- [x] Develop AI grading algorithms in ai-module/app.py
- [ ] Train models: Use data in ai-module/data/ and models in ai-module/models/
- [x] Integrate AI with backend: Connect to grading logic in grade.php model

## User Roles Implementation

### SUPER ADMIN
- [ ] Develop system dashboard: Server status, uptime, error logs, user activity (frontend/views/super-admin/super-admin.php)
- [ ] User role management: Create/edit/deactivate accounts (superAdminController.php)
- [ ] Database management: Backup, restore, integrity checks
- [ ] Audit logs: Full activity tracking with export
- [ ] AI module configuration: Adjust algorithms, thresholds
- [ ] System settings: Grading scales, security, encryption

### ADMIN
- [ ] Dashboard: System usage, grading status, analytics (frontend/views/admin/mis-admin.php)
- [ ] User management: Approve/assign roles, update records (adminController.php)
- [ ] Class & course management: Add/update courses, assign professors
- [ ] Reports & analytics: Generate performance reports
- [ ] Audit trail: View grading logs

### PROFESSOR
- [ ] Dashboard: Student performance summary, grading progress (frontend/views/professor/professor.php)
- [ ] Grade entry: Input scores, review AI computation (professorController.php)
- [ ] Performance analytics: Trends, charts, at-risk indicators
- [ ] Feedback & communication: Send alerts, comments
- [ ] Resource suggestions: Attach materials

### STUDENT
- [ ] Dashboard: Visual grade summary (frontend/views/student/student.php)
- [ ] Grade breakdown: Detailed scores per activity
- [ ] Performance insights: AI recommendations, study tips
- [ ] Progress tracker: Track over time, compare semesters
- [ ] Notifications: Alerts for updates, feedback

## Frontend Development
- [ ] Style with CSS: Use assets/css/ for each role
- [ ] Add JavaScript: Enhance interactivity in frontend/js/main.js
- [ ] Components: Header, navbar, footer in frontend/components/

## Testing and Deployment
- [ ] Test all features: Ensure functionality across roles
- [ ] Debug and fix issues: Use logs in log.php model
- [ ] Deploy system: Set up on server with XAMPP
