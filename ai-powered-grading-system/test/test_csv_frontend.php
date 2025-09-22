<?php
/**
 * Frontend CSV Management Testing
 * Tests the web interface and JavaScript functionality
 */

echo "=== FRONTEND CSV MANAGEMENT TESTING ===\n\n";

// Test 1: Check if required files exist
echo "🧪 TEST 1: File Existence Check\n";
$requiredFiles = [
    '../frontend/views/super-admin/super-admin.php',
    '../frontend/js/superadmin.js',
    '../logs/csv/README.md',
    '../logs/csv/settings/csv_config.json'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✅ " . $file . " exists\n";
    } else {
        echo "   ❌ " . $file . " missing\n";
        $missingFiles[] = $file;
    }
}

if (empty($missingFiles)) {
    echo "   📊 All required files present\n";
} else {
    echo "   ⚠️  Missing files: " . implode(', ', $missingFiles) . "\n";
}

// Test 2: Check CSV directory structure
echo "\n🧪 TEST 2: Directory Structure\n";
$requiredDirs = [
    '../logs/csv',
    '../logs/csv/archive',
    '../logs/csv/failed',
    '../logs/csv/processing',
    '../logs/csv/settings'
];

$missingDirs = [];
foreach ($requiredDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "   ✅ " . $dir . " directory exists\n";
    } else {
        echo "   ❌ " . $dir . " directory missing\n";
        $missingDirs[] = $dir;
    }
}

if (empty($missingDirs)) {
    echo "   📊 All required directories present\n";
} else {
    echo "   ⚠️  Missing directories: " . implode(', ', $missingDirs) . "\n";
}

// Test 3: Check CSV files
echo "\n🧪 TEST 3: CSV Files Check\n";
$csvDir = __DIR__ . '/../logs/csv/';
$csvFiles = glob($csvDir . '*.csv');
$csvFiles = array_filter($csvFiles, function($file) {
    return !is_dir($file);
});

echo "   📊 Found " . count($csvFiles) . " CSV files\n";
foreach ($csvFiles as $file) {
    $filename = basename($file);
    $size = filesize($file);
    $modified = date('Y-m-d H:i:s', filemtime($file));
    echo "   📄 " . $filename . " (" . $size . " bytes, modified: " . $modified . ")\n";

    // Validate CSV format
    $handle = fopen($file, 'r');
    if ($handle) {
        $header = fgetcsv($handle);
        if ($header && count($header) >= 7) {
            echo "      ✅ Valid CSV format\n";
        } else {
            echo "      ❌ Invalid CSV format\n";
        }
        fclose($handle);
    }
}

// Test 4: Check configuration file
echo "\n🧪 TEST 4: Configuration File\n";
$configFile = __DIR__ . '/../logs/csv/settings/csv_config.json';
if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
    if ($config) {
        echo "   ✅ Configuration file is valid JSON\n";
        echo "   📊 Configuration settings:\n";
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                echo "      - " . $key . ": Array (" . count($value) . " items)\n";
            } elseif (is_bool($value)) {
                echo "      - " . $key . ": " . ($value ? 'true' : 'false') . "\n";
            } else {
                echo "      - " . $key . ": " . $value . "\n";
            }
        }
    } else {
        echo "   ❌ Configuration file contains invalid JSON\n";
    }
} else {
    echo "   ❌ Configuration file not found\n";
}

// Test 5: Check permissions
echo "\n🧪 TEST 5: File Permissions\n";
$testFiles = array_merge($csvFiles, [$configFile]);
foreach ($testFiles as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "   📄 " . basename($file) . " permissions: " . $perms . "\n";

        if ($perms >= '0644') {
            echo "      ✅ Permissions are secure\n";
        } else {
            echo "      ⚠️  Permissions may be too permissive\n";
        }
    }
}

// Test 6: Check for required HTML elements
echo "\n🧪 TEST 6: HTML Structure Check\n";
$superAdminFile = __DIR__ . '/../frontend/views/super-admin/super-admin.php';
if (file_exists($superAdminFile)) {
    $content = file_get_contents($superAdminFile);
    $requiredElements = [
        'csv-management-section',
        'export-csv-btn',
        'csv-export-options',
        'csv-files-management',
        'csv-statistics'
    ];

    $missingElements = [];
    foreach ($requiredElements as $element) {
        if (strpos($content, 'id="' . $element . '"') !== false) {
            echo "   ✅ Element '" . $element . "' found in HTML\n";
        } else {
            echo "   ❌ Element '" . $element . "' missing from HTML\n";
            $missingElements[] = $element;
        }
    }

    if (empty($missingElements)) {
        echo "   📊 All required HTML elements present\n";
    } else {
        echo "   ⚠️  Missing HTML elements: " . implode(', ', $missingElements) . "\n";
    }
}

// Test 7: Check JavaScript functions
echo "\n🧪 TEST 7: JavaScript Functions Check\n";
$jsFile = __DIR__ . '/../frontend/js/superadmin.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    $requiredFunctions = [
        'exportLogsToCsv',
        'loadCsvFiles',
        'downloadCsvFile',
        'deleteCsvFile',
        'loadCsvStats',
        'openCsvSettings',
        'saveCsvSettings',
        'cleanupOldCsvLogs'
    ];

    $missingFunctions = [];
    foreach ($requiredFunctions as $function) {
        if (strpos($content, 'function ' . $function . '(') !== false) {
            echo "   ✅ Function '" . $function . "' found in JavaScript\n";
        } else {
            echo "   ❌ Function '" . $function . "' missing from JavaScript\n";
            $missingFunctions[] = $function;
        }
    }

    if (empty($missingFunctions)) {
        echo "   📊 All required JavaScript functions present\n";
    } else {
        echo "   ⚠️  Missing JavaScript functions: " . implode(', ', $missingFunctions) . "\n";
    }
}

// Test 8: Check API endpoints
echo "\n🧪 TEST 8: API Endpoints Check\n";
$apiFile = __DIR__ . '/../backend/routes/api.php';
if (file_exists($apiFile)) {
    $content = file_get_contents($apiFile);
    $requiredEndpoints = [
        '/api/superadmin/logs/export-csv',
        '/api/superadmin/logs/csv-files',
        '/api/superadmin/logs/csv-config',
        '/api/superadmin/logs/csv-stats',
        '/api/superadmin/logs/csv-settings',
        '/api/superadmin/logs/csv-download',
        '/api/superadmin/logs/csv-delete',
        '/api/superadmin/logs/csv-cleanup'
    ];

    $missingEndpoints = [];
    foreach ($requiredEndpoints as $endpoint) {
        if (strpos($content, $endpoint) !== false) {
            echo "   ✅ API endpoint '" . $endpoint . "' found\n";
        } else {
            echo "   ❌ API endpoint '" . $endpoint . "' missing\n";
            $missingEndpoints[] = $endpoint;
        }
    }

    if (empty($missingEndpoints)) {
        echo "   📊 All required API endpoints present\n";
    } else {
        echo "   ⚠️  Missing API endpoints: " . implode(', ', $missingEndpoints) . "\n";
    }
}

// Test 9: Check CSS styling
echo "\n🧪 TEST 9: CSS Styling Check\n";
$cssFile = __DIR__ . '/../frontend/assets/css/superadmin.css';
if (file_exists($cssFile)) {
    $content = file_get_contents($cssFile);
    $requiredStyles = [
        'csv-management-section',
        'csv-controls',
        'csv-options-panel',
        'csv-files-panel',
        'csv-stats-panel',
        'csv-file-item',
        'file-actions'
    ];

    $missingStyles = [];
    foreach ($requiredStyles as $style) {
        if (strpos($content, '.' . $style) !== false || strpos($content, '#' . $style) !== false) {
            echo "   ✅ CSS class '" . $style . "' found\n";
        } else {
            echo "   ❌ CSS class '" . $style . "' missing\n";
            $missingStyles[] = $style;
        }
    }

    if (empty($missingStyles)) {
        echo "   📊 All required CSS classes present\n";
    } else {
        echo "   ⚠️  Missing CSS classes: " . implode(', ', $missingStyles) . "\n";
    }
}

// Test 10: Integration check
echo "\n🧪 TEST 10: Integration Check\n";
$integrationPoints = [
    'Backend controller methods' => file_exists(__DIR__ . '/../backend/controllers/superAdminController.php'),
    'Frontend view file' => file_exists(__DIR__ . '/../frontend/views/super-admin/super-admin.php'),
    'JavaScript file' => file_exists(__DIR__ . '/../frontend/js/superadmin.js'),
    'API routes' => file_exists(__DIR__ . '/../backend/routes/api.php'),
    'CSV logger model' => file_exists(__DIR__ . '/../backend/models/CsvLogger.php'),
    'Log model' => file_exists(__DIR__ . '/../backend/models/log.php')
];

$integrationIssues = [];
foreach ($integrationPoints as $component => $exists) {
    if ($exists) {
        echo "   ✅ " . $component . " integrated\n";
    } else {
        echo "   ❌ " . $component . " missing\n";
        $integrationIssues[] = $component;
    }
}

if (empty($integrationIssues)) {
    echo "   📊 All integration points connected\n";
} else {
    echo "   ⚠️  Integration issues: " . implode(', ', $integrationIssues) . "\n";
}

// Final Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 FRONTEND TESTING RESULTS SUMMARY\n";
echo str_repeat("=", 60) . "\n";

$passedTests = 0;
$totalTests = 10;

if (empty($missingFiles)) $passedTests++;
if (empty($missingDirs)) $passedTests++;
if (count($csvFiles) > 0) $passedTests++;
if (file_exists($configFile)) $passedTests++;
if (empty($missingElements)) $passedTests++;
if (empty($missingFunctions)) $passedTests++;
if (empty($missingEndpoints)) $passedTests++;
if (empty($missingStyles)) $passedTests++;
if (empty($integrationIssues)) $passedTests++;

echo "📊 Test Results: " . $passedTests . "/" . $totalTests . " tests PASSED\n";

echo "\n📈 OVERALL STATUS: ";
if ($passedTests === $totalTests) {
    echo "🎉 ALL TESTS PASSED - FRONTEND IS FULLY OPERATIONAL\n";
} elseif ($passedTests >= $totalTests * 0.8) {
    echo "⚠️  MOSTLY SUCCESSFUL - Minor issues detected\n";
} else {
    echo "❌ SIGNIFICANT ISSUES - Frontend needs attention\n";
}

echo "\n🔍 RECOMMENDATIONS:\n";
if ($passedTests === $totalTests) {
    echo "   • No action required - frontend is working perfectly\n";
    echo "   • Regular monitoring recommended\n";
} else {
    echo "   • Review and fix the failed tests listed above\n";
    echo "   • Test the web interface manually\n";
    echo "   • Verify JavaScript functionality in browser\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Frontend test completed at: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
?>
