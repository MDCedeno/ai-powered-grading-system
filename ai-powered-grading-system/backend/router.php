<?php
// Router for PHP built-in server
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$request = strtok($request, '?');

// Route API requests to api.php
if (strpos($request, '/api/') === 0) {
    include __DIR__ . '/routes/api.php';
    exit;
}

// For other requests, serve static files or return 404
http_response_code(404);
echo 'Not Found';
?>
