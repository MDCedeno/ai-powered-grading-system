<?php
/**
 * Browser-based CSV Testing Interface
 * This file can be accessed through the web browser to test CSV functionality
 */

session_start();
require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/controllers/superAdminController.php';

$controller = new SuperAdminController($pdo);

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    switch ($_GET['action']) {
        case 'get_csv_stats':
            echo json_encode($controller->getCsvStats());
            break;
        case 'get_csv_files':
            echo json_encode($controller->getCsvLogFiles(20));
            break;
        case 'export_csv':
            $result = $controller->exportLogsToCsv(
                $_GET['start_date'] ?? null,
                $_GET['end_date'] ?? null,
                $_GET['log_type'] ?? null,
                $_GET['status'] ?? null
            );
            echo json_encode($result);
            break;
        case 'delete_csv':
            $result = $controller->deleteCsvFile($_GET['filename']);
            echo json_encode($result);
            break;
        case 'get_config':
            echo json_encode($controller->getCsvConfig());
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Logging System - Browser Testing Interface</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .test-title {
            color: #333;
            margin-top: 0;
        }
        .test-button {
            background: #007cba;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover {
            background: #005a87;
        }
        .test-button.danger {
            background: #dc3545;
        }
        .test-button.danger:hover {
            background: #c82333;
        }
        .test-button.success {
            background: #28a745;
        }
        .test-button.success:hover {
            background: #218838;
        }
        .test-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .test-result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .test-result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .test-result.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007cba;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-label {
            display: block;
            font-weight: bold;
            color: #666;
        }
        .stat-value {
            display: block;
            font-size: 24px;
            color: #007cba;
            margin-top: 5px;
        }
        .file-list {
            margin-top: 15px;
        }
        .file-item {
            background: #f8f9fa;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-info {
            flex-grow: 1;
        }
        .file-actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        .export-options {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .form-group {
            margin: 10px 0;
        }
        .form-group label {
            display: inline-block;
            width: 120px;
        }
        .form-group input, .form-group select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .tab-button {
            background: #e9ecef;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px 5px 0 0;
        }
        .tab-button.active {
            background: white;
            border-bottom: 2px solid #007cba;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <h1>🧪 CSV Logging System - Browser Testing Interface</h1>

    <div class="container">
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('overview')">Overview</button>
            <button class="tab-button" onclick="showTab('export')">Export Test</button>
            <button class="tab-button" onclick="showTab('files')">File Management</button>
            <button class="tab-button" onclick="showTab('settings')">Settings Test</button>
            <button class="tab-button" onclick="showTab('performance')">Performance Test</button>
        </div>

        <!-- Overview Tab -->
        <div id="overview" class="tab-content active">
            <h2>System Overview</h2>
            <div class="test-section">
                <h3>CSV Statistics</h3>
                <button class="test-button" onclick="loadStats()">Load Statistics</button>
                <div id="stats-result" class="test-result"></div>
                <div id="stats-container"></div>
            </div>

            <div class="test-section">
                <h3>System Status</h3>
                <button class="test-button" onclick="testSystemStatus()">Test System Status</button>
                <div id="status-result" class="test-result"></div>
            </div>
        </div>

        <!-- Export Test Tab -->
        <div id="export" class="tab-content">
            <h2>CSV Export Testing</h2>
            <div class="test-section">
                <h3>Export Options</h3>
                <div class="export-options">
                    <div class="form-group">
                        <label>Start Date:</label>
                        <input type="date" id="start-date">
                    </div>
                    <div class="form-group">
                        <label>End Date:</label>
                        <input type="date" id="end-date">
                    </div>
                    <div class="form-group">
                        <label>Log Type:</label>
                        <select id="log-type">
                            <option value="">All Types</option>
                            <option value="authentication">Authentication</option>
                            <option value="permission_change">Permission Change</option>
                            <option value="sensitive_data_access">Sensitive Data Access</option>
                            <option value="data_modification">Data Modification</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status:</label>
                        <select id="status">
                            <option value="">All Status</option>
                            <option value="Success">Success</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                </div>

                <button class="test-button" onclick="testExport()">Export CSV</button>
                <div id="export-result" class="test-result"></div>
            </div>
        </div>

        <!-- File Management Tab -->
        <div id="files" class="tab-content">
            <h2>File Management Testing</h2>
            <div class="test-section">
                <h3>CSV Files</h3>
                <button class="test-button" onclick="loadFiles()">Load Files</button>
                <div id="files-result" class="test-result"></div>
                <div id="files-container" class="file-list"></div>
            </div>
        </div>

        <!-- Settings Test Tab -->
        <div id="settings" class="tab-content">
            <h2>Settings Testing</h2>
            <div class="test-section">
                <h3>Configuration Management</h3>
                <button class="test-button" onclick="loadConfig()">Load Configuration</button>
                <button class="test-button" onclick="testSettings()">Test Settings</button>
                <div id="settings-result" class="test-result"></div>
                <div id="config-container"></div>
            </div>
        </div>

        <!-- Performance Test Tab -->
        <div id="performance" class="tab-content">
            <h2>Performance Testing</h2>
            <div class="test-section">
                <h3>Performance Tests</h3>
                <button class="test-button" onclick="testPerformance()">Run Performance Test</button>
                <div id="performance-result" class="test-result"></div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function showResult(elementId, message, type = 'info') {
            const result = document.getElementById(elementId);
            result.textContent = message;
            result.className = 'test-result ' + type;
            result.style.display = 'block';
        }

        function showLoading(elementId) {
            const result = document.getElementById(elementId);
            result.innerHTML = '<div class="loading"></div> Loading...';
            result.className = 'test-result info';
            result.style.display = 'block';
        }

        async function loadStats() {
            showLoading('stats-result');

            try {
                const response = await fetch('?action=get_csv_stats');
                const data = await response.json();

                if (data) {
                    let html = '<div class="stats-grid">';
                    html += '<div class="stat-item"><span class="stat-label">Total Files</span><span class="stat-value">' + (data.total_files || 0) + '</span></div>';
                    html += '<div class="stat-item"><span class="stat-label">Total Records</span><span class="stat-value">' + (data.total_records || 0) + '</span></div>';
                    html += '<div class="stat-item"><span class="stat-label">Total Size</span><span class="stat-value">' + (data.total_size || '0 MB') + '</span></div>';
                    html += '<div class="stat-item"><span class="stat-label">Oldest File</span><span class="stat-value">' + (data.oldest_file || '-') + '</span></div>';
                    html += '<div class="stat-item"><span class="stat-label">Newest File</span><span class="stat-value">' + (data.newest_file || '-') + '</span></div>';
                    html += '</div>';

                    document.getElementById('stats-container').innerHTML = html;
                    showResult('stats-result', '✅ Statistics loaded successfully', 'success');
                } else {
                    showResult('stats-result', '❌ Failed to load statistics', 'error');
                }
            } catch (error) {
                showResult('stats-result', '❌ Error: ' + error.message, 'error');
            }
        }

        async function testSystemStatus() {
            showLoading('status-result');

            try {
                // Test multiple endpoints
                const promises = [
                    fetch('?action=get_csv_stats'),
                    fetch('?action=get_csv_files'),
                    fetch('?action=get_config')
                ];

                const results = await Promise.all(promises);
                const successCount = results.filter(r => r.ok).length;

                if (successCount === promises.length) {
                    showResult('status-result', '✅ All systems operational', 'success');
                } else {
                    showResult('status-result', '⚠️ Some systems may have issues (' + successCount + '/' + promises.length + ' working)', 'error');
                }
            } catch (error) {
                showResult('status-result', '❌ System status check failed: ' + error.message, 'error');
            }
        }

        async function testExport() {
            showLoading('export-result');

            try {
                const params = new URLSearchParams({
                    action: 'export_csv',
                    start_date: document.getElementById('start-date').value,
                    end_date: document.getElementById('end-date').value,
                    log_type: document.getElementById('log-type').value,
                    status: document.getElementById('status').value
                });

                const response = await fetch('?' + params.toString());
                const data = await response.json();

                if (data.success) {
                    showResult('export-result', '✅ Export successful: ' + data.filename + ' (' + data.records_exported + ' records)', 'success');
                    // Refresh file list
                    loadFiles();
                } else {
                    showResult('export-result', '❌ Export failed: ' + data.message, 'error');
                }
            } catch (error) {
                showResult('export-result', '❌ Export error: ' + error.message, 'error');
            }
        }

        async function loadFiles() {
            showLoading('files-result');

            try {
                const response = await fetch('?action=get_csv_files');
                const files = await response.json();

                if (files && files.length > 0) {
                    let html = '<div class="file-list">';
                    files.forEach(file => {
                        html += `
                            <div class="file-item">
                                <div class="file-info">
                                    <strong>${file.filename}</strong><br>
                                    Size: ${file.size} bytes | Created: ${file.created_at} | Records: ${file.record_count}
                                </div>
                                <div class="file-actions">
                                    <button class="test-button btn-small" onclick="downloadFile('${file.filename}')">Download</button>
                                    <button class="test-button danger btn-small" onclick="deleteFile('${file.filename}')">Delete</button>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';

                    document.getElementById('files-container').innerHTML = html;
                    showResult('files-result', '✅ Files loaded successfully (' + files.length + ' files)', 'success');
                } else {
                    document.getElementById('files-container').innerHTML = '<p>No CSV files found.</p>';
                    showResult('files-result', 'ℹ️ No CSV files found', 'info');
                }
            } catch (error) {
                showResult('files-result', '❌ Error loading files: ' + error.message, 'error');
            }
        }

        async function downloadFile(filename) {
            window.open('?action=download&filename=' + encodeURIComponent(filename));
        }

        async function deleteFile(filename) {
            if (!confirm('Are you sure you want to delete ' + filename + '?')) {
                return;
            }

            try {
                const response = await fetch('?action=delete_csv&filename=' + encodeURIComponent(filename));
                const data = await response.json();

                if (data.success) {
                    alert('File deleted successfully');
                    loadFiles(); // Refresh list
                } else {
                    alert('Delete failed: ' + data.message);
                }
            } catch (error) {
                alert('Delete error: ' + error.message);
            }
        }

        async function loadConfig() {
            showLoading('settings-result');

            try {
                const response = await fetch('?action=get_config');
                const config = await response.json();

                if (config) {
                    let html = '<div class="stats-grid">';
                    Object.keys(config).forEach(key => {
                        const value = config[key];
                        const displayValue = typeof value === 'boolean' ? (value ? 'Yes' : 'No') : value;
                        html += `<div class="stat-item"><span class="stat-label">${key.replace(/_/g, ' ').toUpperCase()}</span><span class="stat-value">${displayValue}</span></div>`;
                    });
                    html += '</div>';

                    document.getElementById('config-container').innerHTML = html;
                    showResult('settings-result', '✅ Configuration loaded successfully', 'success');
                } else {
                    showResult('settings-result', '❌ Failed to load configuration', 'error');
                }
            } catch (error) {
                showResult('settings-result', '❌ Error: ' + error.message, 'error');
            }
        }

        async function testSettings() {
            showLoading('settings-result');

            try {
                // Test enabling/disabling CSV logging
                const testResults = [];

                // Test 1: Check current status
                const configResponse = await fetch('?action=get_config');
                const initialConfig = await configResponse.json();
                testResults.push('Initial CSV logging enabled: ' + (initialConfig.enabled ? 'Yes' : 'No'));

                // Test 2: Toggle setting
                const toggleResponse = await fetch('?action=toggle_csv&enabled=' + (initialConfig.enabled ? 'false' : 'true'));
                const toggleResult = await toggleResponse.json();

                if (toggleResult.success) {
                    testResults.push('✅ Settings toggle successful');
                } else {
                    testResults.push('❌ Settings toggle failed: ' + toggleResult.message);
                }

                // Test 3: Verify change
                const finalConfigResponse = await fetch('?action=get_config');
                const finalConfig = await finalConfigResponse.json();
                testResults.push('Final CSV logging enabled: ' + (finalConfig.enabled ? 'Yes' : 'No'));

                showResult('settings-result', '✅ Settings test completed:\n' + testResults.join('\n'), 'success');
            } catch (error) {
                showResult('settings-result', '❌ Settings test error: ' + error.message, 'error');
            }
        }

        async function testPerformance() {
            showLoading('performance-result');

            try {
                const startTime = Date.now();
                const testResults = [];

                // Test 1: Multiple exports
                testResults.push('🧪 Testing multiple exports...');
                const exportPromises = [];
                for (let i = 0; i < 3; i++) {
                    exportPromises.push(fetch('?action=export_csv'));
                }

                const exportResults = await Promise.all(exportPromises);
                const successfulExports = exportResults.filter(r => r.ok).length;
                testResults.push('✅ ' + successfulExports + '/3 exports successful');

                // Test 2: Statistics generation
                testResults.push('🧪 Testing statistics generation...');
                const statsPromises = [];
                for (let i = 0; i < 5; i++) {
                    statsPromises.push(fetch('?action=get_csv_stats'));
                }

                const statsResults = await Promise.all(statsPromises);
                const successfulStats = statsResults.filter(r => r.ok).length;
                testResults.push('✅ ' + successfulStats + '/5 statistics calls successful');

                // Test 3: File operations
                testResults.push('🧪 Testing file operations...');
                const filesResponse = await fetch('?action=get_csv_files');
                const files = await filesResponse.json();

                if (files && files.length > 0) {
                    const deleteResponse = await fetch('?action=delete_csv&filename=' + encodeURIComponent(files[0].filename));
                    const deleteResult = await deleteResponse.json();

                    if (deleteResult.success) {
                        testResults.push('✅ File deletion successful');
                    } else {
                        testResults.push('❌ File deletion failed: ' + deleteResult.message);
                    }
                } else {
                    testResults.push('⚠️ No files available for deletion test');
                }

                const endTime = Date.now();
                const totalTime = (endTime - startTime) / 1000;

                testResults.push('⏱️ Total test time: ' + totalTime + ' seconds');

                showResult('performance-result', '✅ Performance test completed:\n' + testResults.join('\n'), 'success');
            } catch (error) {
                showResult('performance-result', '❌ Performance test error: ' + error.message, 'error');
            }
        }

        // Load initial data
        window.addEventListener('load', function() {
            loadStats();
        });
    </script>
</body>
</html>
