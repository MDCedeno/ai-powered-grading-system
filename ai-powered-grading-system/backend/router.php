<?php
// Router for PHP built-in server
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Debug: Log the request (removed)

// Check if path is passed as query parameter (for compatibility with existing JS)
if (isset($_GET['path'])) {
    $request = $_GET['path'];
} else {
    // Remove query string
    $request = strtok($request, '?');

    // Remove the router.php part from the URI if present
    $scriptName = basename($_SERVER['SCRIPT_NAME']); // should be router.php
    $pos = strpos($request, '/' . $scriptName);
    if ($pos !== false) {
        $request = substr($request, $pos + strlen($scriptName) + 1);
        $request = '/' . ltrim($request, '/');
    }
}

// Route API requests to api.php
// Handle both direct /api/ requests and /backend/router.php/api/ requests
if (strpos($request, '/api/') === 0) {
    include __DIR__ . '/routes/api.php';
    exit;
}

// Route web requests to web.php for admin, professor, student, superadmin
if (preg_match('#^/(admin|professor|student|superadmin)/#', $request)) {
    include __DIR__ . '/routes/web.php';
    exit;
}

// For other requests, serve static files or return 404
$publicDir = realpath(__DIR__ . '/../frontend');

$requestedPath = $request;

// Strip leading /frontend from request URI if present
if (strpos($requestedPath, '/frontend') === 0) {
    $requestedPath = substr($requestedPath, strlen('/frontend'));
}

$requestedFile = realpath($publicDir . $requestedPath);

if ($requestedFile && strpos($requestedFile, $publicDir) === 0 && is_file($requestedFile)) {
    // Serve the static file
    return false; // Let the built-in server serve the file
} else {
    http_response_code(404);
    echo 'Not Found';
}
?>
