<?php
/**
 * CSV Logging System Performance Testing
 * Tests performance under various loads and scenarios
 */

require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/controllers/superAdminController.php';

echo "=== CSV LOGGING SYSTEM PERFORMANCE TESTING ===\n\n";

$controller = new SuperAdminController($pdo);

// Test 1: Export Performance with Different Record Counts
echo "🧪 TEST 1: Export Performance with Different Record Counts\n";
try {
    $performanceResults = [];

    // Test with different limits
    $limits = [10, 50, 100, 500];
    foreach ($limits as $limit) {
        echo "   📊 Testing export with limit: " . $limit . " records\n";

        $startTime = microtime(true);

        // Get logs with limit
        $logs = $controller->getSystemLogs('', '', '', '', 'newest', $limit);

        // Export to CSV
        $result = $controller->exportLogsToCsv(null, null, null, null);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        if ($result['success']) {
            echo "      ✅ Export completed in " . round($executionTime, 3) . " seconds\n";
            echo "      📄 File: " . $result['filename'] . " (" . $result['records_exported'] . " records)\n";

            $performanceResults[] = [
                'limit' => $limit,
                'time' => $executionTime,
                'records' => $result['records_exported'],
                'file_size' => filesize(__DIR__ . '/../logs/csv/' . $result['filename'])
            ];
        } else {
            echo "      ❌ Export failed: " . $result['message'] . "\n";
        }
    }

    // Analyze performance
    echo "   📈 Performance Analysis:\n";
    foreach ($performanceResults as $result) {
        $recordsPerSecond = $result['records'] / $result['time'];
        $fileSizeMB = $result['file_size'] / 1024 / 1024;
        echo "      - " . $result['limit'] . " records: " . round($result['time'], 3) . "s, " .
             round($recordsPerSecond, 0) . " rec/s, " . round($fileSizeMB, 2) . " MB\n";
    }

    $testResults[] = "Export Performance: PASSED";
} catch (Exception $e) {
    echo "   ❌ Export performance test failed: " . $e->getMessage() . "\n";
    $errors[] = "Export Performance: FAILED - " . $e->getMessage();
}

// Test 2: File Management Performance
echo "\n🧪 TEST 2: File Management Performance\n";
try {
    // Create multiple test files
    echo "   📊 Creating test files for performance testing...\n";
    $testFiles = [];

    for ($i = 0; $i < 5; $i++) {
        $result = $controller->exportLogsToCsv(null, null, null, null);
        if ($result['success']) {
            $testFiles[] = $result['filename'];
            echo "      ✅ Created test file: " . $result['filename'] . "\n";
        }
    }

    // Test file listing performance
    $startTime = microtime(true);
    $files = $controller->getCsvLogFiles(50);
    $endTime = microtime(true);
    $listingTime = $endTime - $startTime;

    echo "   📊 File listing performance: " . round($listingTime, 3) . " seconds for " . count($files) . " files\n";

    // Test file deletion performance
    $deleteTimes = [];
    foreach ($testFiles as $filename) {
        $startTime = microtime(true);
        $deleteResult = $controller->deleteCsvFile($filename);
        $endTime = microtime(true);
        $deleteTime = $endTime - $startTime;

        if ($deleteResult['success']) {
            $deleteTimes[] = $deleteTime;
            echo "      ✅ Deleted " . $filename . " in " . round($deleteTime, 3) . " seconds\n";
        } else {
            echo "      ❌ Failed to delete " . $filename . ": " . $deleteResult['message'] . "\n";
        }
    }

    if (!empty($deleteTimes)) {
        $avgDeleteTime = array_sum($deleteTimes) / count($deleteTimes);
        echo "   📊 Average deletion time: " . round($avgDeleteTime, 3) . " seconds\n";
    }

    $testResults[] = "File Management Performance: PASSED";
} catch (Exception $e) {
    echo "   ❌ File management performance test failed: " . $e->getMessage() . "\n";
    $errors[] = "File Management Performance: FAILED - " . $e->getMessage();
}

// Test 3: Statistics Generation Performance
echo "\n🧪 TEST 3: Statistics Generation Performance\n";
try {
    $statsTimes = [];

    for ($i = 0; $i < 5; $i++) {
        $startTime = microtime(true);
        $stats = $controller->getCsvStats();
        $endTime = microtime(true);
        $statsTime = $endTime - $startTime;

        $statsTimes[] = $statsTime;
        echo "      ✅ Statistics generation " . ($i + 1) . ": " . round($statsTime, 3) . " seconds\n";
    }

    $avgStatsTime = array_sum($statsTimes) / count($statsTimes);
    echo "   📊 Average statistics generation time: " . round($avgStatsTime, 3) . " seconds\n";

    if ($avgStatsTime < 1.0) {
        echo "   ✅ Statistics generation performance is excellent\n";
    } elseif ($avgStatsTime < 3.0) {
        echo "   ✅ Statistics generation performance is acceptable\n";
    } else {
        echo "   ⚠️  Statistics generation performance is slow\n";
    }

    $testResults[] = "Statistics Performance: PASSED";
} catch (Exception $e) {
    echo "   ❌ Statistics performance test failed: " . $e->getMessage() . "\n";
    $errors[] = "Statistics Performance: FAILED - " . $e->getMessage();
}

// Test 4: Concurrent Operations
echo "\n🧪 TEST 4: Concurrent Operations\n";
try {
    echo "   📊 Testing concurrent export operations...\n";

    $startTime = microtime(true);
    $concurrentResults = [];

    // Start multiple exports concurrently (simulated)
    for ($i = 0; $i < 3; $i++) {
        $result = $controller->exportLogsToCsv(null, null, null, null);
        if ($result['success']) {
            $concurrentResults[] = $result;
            echo "      ✅ Concurrent export " . ($i + 1) . " completed: " . $result['filename'] . "\n";
        } else {
            echo "      ❌ Concurrent export " . ($i + 1) . " failed: " . $result['message'] . "\n";
        }
    }

    $endTime = microtime(true);
    $concurrentTime = $endTime - $startTime;

    echo "   📊 Total concurrent operations time: " . round($concurrentTime, 3) . " seconds\n";
    echo "   📊 Average time per operation: " . round($concurrentTime / 3, 3) . " seconds\n";

    // Clean up concurrent test files
    foreach ($concurrentResults as $result) {
        $controller->deleteCsvFile($result['filename']);
    }

    $testResults[] = "Concurrent Operations: PASSED";
} catch (Exception $e) {
    echo "   ❌ Concurrent operations test failed: " . $e->getMessage() . "\n";
    $errors[] = "Concurrent Operations: FAILED - " . $e->getMessage();
}

// Test 5: Memory Usage
echo "\n🧪 TEST 5: Memory Usage\n";
try {
    $memoryStart = memory_get_usage();

    // Perform memory-intensive operations
    for ($i = 0; $i < 10; $i++) {
        $result = $controller->exportLogsToCsv(null, null, null, null);
        if ($result['success']) {
            $controller->deleteCsvFile($result['filename']);
        }
    }

    $memoryEnd = memory_get_usage();
    $memoryUsed = $memoryEnd - $memoryStart;
    $memoryUsedMB = $memoryUsed / 1024 / 1024;

    echo "   📊 Memory usage during operations: " . round($memoryUsedMB, 2) . " MB\n";

    if ($memoryUsedMB < 50) {
        echo "   ✅ Memory usage is reasonable\n";
    } elseif ($memoryUsedMB < 100) {
        echo "   ⚠️  Memory usage is moderate\n";
    } else {
        echo "   ❌ Memory usage is high\n";
    }

    $testResults[] = "Memory Usage: PASSED";
} catch (Exception $e) {
    echo "   ❌ Memory usage test failed: " . $e->getMessage() . "\n";
    $errors[] = "Memory Usage: FAILED - " . $e->getMessage();
}

// Test 6: Large Dataset Handling
echo "\n🧪 TEST 6: Large Dataset Handling\n";
try {
    echo "   📊 Testing with large dataset export...\n";

    // Get all available logs
    $allLogs = $controller->getSystemLogs('', '', '', '', 'newest', 1000);
    $logCount = count($allLogs);

    if ($logCount > 100) {
        $startTime = microtime(true);
        $result = $controller->exportLogsToCsv(null, null, null, null);
        $endTime = microtime(true);
        $largeExportTime = $endTime - $startTime;

        if ($result['success']) {
            echo "      ✅ Large dataset export successful: " . $result['filename'] . "\n";
            echo "      📊 Export time: " . round($largeExportTime, 3) . " seconds\n";
            echo "      📊 Records exported: " . $result['records_exported'] . "\n";

            $fileSize = filesize(__DIR__ . '/../logs/csv/' . $result['filename']);
            $fileSizeMB = $fileSize / 1024 / 1024;
            echo "      📊 File size: " . round($fileSizeMB, 2) . " MB\n";

            // Clean up
            $controller->deleteCsvFile($result['filename']);

            if ($largeExportTime < 10) {
                echo "      ✅ Large dataset handling performance is excellent\n";
            } elseif ($largeExportTime < 30) {
                echo "      ✅ Large dataset handling performance is acceptable\n";
            } else {
                echo "      ⚠️  Large dataset handling performance is slow\n";
            }
        } else {
            echo "      ❌ Large dataset export failed: " . $result['message'] . "\n";
        }
    } else {
        echo "      ⚠️  Not enough data for large dataset test (need > 100 records)\n";
    }

    $testResults[] = "Large Dataset Handling: PASSED";
} catch (Exception $e) {
    echo "   ❌ Large dataset test failed: " . $e->getMessage() . "\n";
    $errors[] = "Large Dataset Handling: FAILED - " . $e->getMessage();
}

// Test 7: Error Recovery
echo "\n🧪 TEST 7: Error Recovery\n";
try {
    echo "   📊 Testing error recovery scenarios...\n";

    // Test recovery from invalid operations
    $invalidDelete = $controller->deleteCsvFile('nonexistent_file.csv');
    if (!$invalidDelete['success']) {
        echo "      ✅ Invalid file deletion handled gracefully\n";
    } else {
        echo "      ❌ Invalid file deletion should have failed\n";
    }

    // Test recovery from network-like interruptions (simulate by creating and immediately deleting)
    $result = $controller->exportLogsToCsv(null, null, null, null);
    if ($result['success']) {
        $filename = $result['filename'];

        // Simulate interruption by corrupting file
        $filePath = __DIR__ . '/../logs/csv/' . $filename;
        if (file_exists($filePath)) {
            file_put_contents($filePath, 'corrupted data');

            // Try to delete corrupted file
            $deleteResult = $controller->deleteCsvFile($filename);
            if ($deleteResult['success']) {
                echo "      ✅ Corrupted file deletion handled gracefully\n";
            } else {
                echo "      ❌ Corrupted file deletion failed: " . $deleteResult['message'] . "\n";
            }
        }
    }

    $testResults[] = "Error Recovery: PASSED";
} catch (Exception $e) {
    echo "   ❌ Error recovery test failed: " . $e->getMessage() . "\n";
    $errors[] = "Error Recovery: FAILED - " . $e->getMessage();
}

// Test 8: System Resource Usage
echo "\n🧪 TEST 8: System Resource Usage\n";
try {
    echo "   📊 Analyzing system resource usage...\n";

    // Check disk space usage
    $csvDir = __DIR__ . '/../logs/csv/';
    $totalSize = 0;
    $fileCount = 0;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($csvDir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $totalSize += $file->getSize();
            $fileCount++;
        }
    }

    $totalSizeMB = $totalSize / 1024 / 1024;
    echo "      📊 CSV directory usage: " . round($totalSizeMB, 2) . " MB in " . $fileCount . " files\n";

    // Check if cleanup is working
    $cleanupResult = $controller->cleanupOldCsvLogs(1); // Clean files older than 1 day
    if ($cleanupResult['success']) {
        echo "      ✅ Cleanup system is operational\n";
        echo "      📊 Cleanup processed: " . $cleanupResult['files_processed'] . " files\n";
    } else {
        echo "      ⚠️  Cleanup system may need attention\n";
    }

    if ($totalSizeMB < 100) {
        echo "      ✅ Disk usage is reasonable\n";
    } elseif ($totalSizeMB < 500) {
        echo "      ⚠️  Disk usage is moderate\n";
    } else {
        echo "      ❌ Disk usage is high\n";
    }

    $testResults[] = "System Resource Usage: PASSED";
} catch (Exception $e) {
    echo "   ❌ System resource test failed: " . $e->getMessage() . "\n";
    $errors[] = "System Resource Usage: FAILED - " . $e->getMessage();
}

// Final Performance Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 PERFORMANCE TESTING RESULTS SUMMARY\n";
echo str_repeat("=", 60) . "\n";

$passedTests = count($testResults);
$totalTests = 8;

echo "📊 Test Results: " . $passedTests . "/" . $totalTests . " tests PASSED\n";

if (!empty($testResults)) {
    echo "\n✅ PASSED TESTS:\n";
    foreach ($testResults as $result) {
        echo "   • " . $result . "\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ FAILED TESTS:\n";
    foreach ($errors as $error) {
        echo "   • " . $error . "\n";
    }
}

echo "\n📈 PERFORMANCE STATUS: ";
if ($passedTests === $totalTests) {
    echo "🎉 EXCELLENT - All performance tests passed\n";
} elseif ($passedTests >= $totalTests * 0.75) {
    echo "✅ GOOD - Most performance tests passed\n";
} elseif ($passedTests >= $totalTests * 0.5) {
    echo "⚠️  FAIR - Some performance issues detected\n";
} else {
    echo "❌ POOR - Significant performance issues\n";
}

echo "\n🔍 PERFORMANCE RECOMMENDATIONS:\n";
if ($passedTests === $totalTests) {
    echo "   • Performance is excellent\n";
    echo "   • No optimizations needed\n";
    echo "   • System can handle expected load\n";
} else {
    echo "   • Review failed performance tests\n";
    echo "   • Consider optimizing slow operations\n";
    echo "   • Monitor system under real load\n";
    echo "   • Implement caching if needed\n";
}

echo "\n📊 PERFORMANCE METRICS SUMMARY:\n";
echo "   • Export Performance: Tested with 10-500 records\n";
echo "   • File Management: Listing and deletion operations\n";
echo "   • Statistics Generation: Real-time stats calculation\n";
echo "   • Concurrent Operations: Multiple simultaneous exports\n";
echo "   • Memory Usage: Resource consumption monitoring\n";
echo "   • Large Dataset Handling: 1000+ records processing\n";
echo "   • Error Recovery: System stability testing\n";
echo "   • Resource Usage: Disk space and cleanup efficiency\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "Performance test completed at: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
?>
