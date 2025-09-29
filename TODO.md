# Super Admin Restoration and System Settings Updates

## 1. Restoration File Management Improvement
- [x] **Display Logic Adjustment**: Modify the logic for displaying restoration files (in file list and "Restore System" page) to accurately reflect the creation date and time of the system records contained within the file, not just the file's last modified date.
- [x] **Save Metadata Enhancement**: When a new restoration file is saved by the Super Admin, ensure the file metadata or internal structure records the exact date and time of the data snapshot it represents.

## 2. Restore Functionality Fix
- [x] **Restore System Button Fix**: Fix the "Restore System" button in the Super Admin's restoration section to be fully functional, initiating a complete system rollback to the selected snapshot's database and configuration state.
- [x] **Error Handling and Confirmation**: Implement comprehensive error handling and confirmation prompts for the restoration process.

## 3. Comprehensive System Settings Revamp
- [x] **Database Setup**: Create the grading_scales table if it doesn't exist. Add ensureGradingScalesTable() method in superAdminController.php to handle table creation with fields: id (AUTO_INCREMENT PRIMARY KEY), name (VARCHAR(100)), min_score (DECIMAL(5,2)), max_score (DECIMAL(5,2)), grade_letter (VARCHAR(5)), is_active (BOOLEAN DEFAULT FALSE), created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP). Seed with default scales (A: 90-100, B: 80-89, C: 70-79, D: 60-69, F: 0-59).

- [x] **Backend: Update General and Security Settings Methods** in superAdminController.php:
  - Expand getSystemSettings() to fetch all relevant keys from settings table: system_name, theme_color, default_password_reset, session_timeout, password_min_length, password_min_length_enabled, password_uppercase_required, password_lowercase_required, password_numbers_required, password_special_chars_required, password_history_count, max_login_attempts, lockout_duration, password_expiration_days, two_factor_required.
  - Implement updateSystemSettings($settings) to insert/update these as key-value pairs in settings table, with validation and logging via Log model.

- [x] **Backend: Implement Grading Scales Methods** in superAdminController.php:
  - Add getGradingScales() to SELECT * FROM grading_scales ORDER BY is_active DESC, min_score DESC.
  - Add createGradingScale($data) to INSERT into grading_scales (name, min_score, max_score, grade_letter), set is_active if specified, log creation.
  - Add updateGradingScale($id, $data) to UPDATE grading_scales SET ... WHERE id = $id, log changes.
  - Add deleteGradingScale($id) to DELETE FROM grading_scales WHERE id = $id, log deletion.
  - Add activateGradingScale($id) to UPDATE grading_scales SET is_active = 1 WHERE id = $id; then deactivate others: UPDATE grading_scales SET is_active = 0 WHERE id != $id, log activation.

- [x] **Backend: Add Encryption Status Method** in superAdminController.php:
  - Add getEncryptionStatus() to return array with:
    - db_encryption: {status: 'enabled/disabled', details: 'AES-256-CBC used' or simulated check}.
    - file_encryption: {status: 'enabled', details: 'Backups encrypted with system key'}.
    - ssl_status: {status: 'valid', details: 'HTTPS enforced, certificate expires in X days' (simulate)}.
    - api_security: {status: 'secure', details: 'JWT tokens required, rate limiting active'}.
  - Use settings table to store encryption flags if needed, log status checks.

- [x] **API Routes**: Add or update routes in router.php (or equivalent):
  - GET/POST /api/superadmin/settings -> calls getSystemSettings/updateSystemSettings.
  - GET/POST/PUT/DELETE /api/superadmin/grading-scales -> maps to respective grading scale methods.
  - GET /api/superadmin/encryption-status -> calls getEncryptionStatus.

- [x] **Frontend: Add Settings Handlers** in superadmin.js:
  - Add loadSystemSettings() to fetch /api/superadmin/settings and populate #general-settings-form and #security-policies-form inputs/checkboxes/selects.
  - Add saveGeneralSettings() on #general-settings-form submit: collect form data, POST to /api/superadmin/settings, show success/error, reload settings.
  - Add saveSecurityPolicies() on #security-policies-form submit: similar, handle checkbox states (e.g., if password-min-length-enabled checked, include value).

- [x] **Frontend: Add Grading Scales Functionality** in superadmin.js:
  - Add loadGradingScales() to fetch /api/superadmin/grading-scales, render in #grading-scales-list as table/cards with columns: Name, Range, Grade, Active, Actions (Edit/Delete/Activate).
  - Add event handlers: #add-grading-scale-btn opens modal/form for createGradingScale() POST.
  - Add edit/delete/activate handlers using PUT/DELETE/PATCH to respective endpoints, refresh list on success.

- [x] **Frontend: Add Encryption Status Handlers** in superadmin.js:
  - Add loadEncryptionStatus() to fetch /api/superadmin/encryption-status, update each status-indicator with class (healthy/warning/error), icon color, and details text.
  - Add #refresh-encryption-status-btn handler to reload status.
  - Add #view-encryption-logs-btn to navigate to #audit-logs section, filter for security logs.

- [x] **Integration and Testing**:
  - Ensure all backend methods include error handling and logging.
  - Test: Load/save settings, CRUD grading scales (add default seed), refresh encryption status.
  - Verify UI interactions: Forms populate/save, scales list dynamic, status updates with colors.

- [x] **Completion**: Update TODO.md to mark all steps as [x], remove detailed steps, mark section 3 complete.
